<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Cache;

use AcMarche\PivotAi\Enums\ContentLevel;
use Generator;
use JsonException;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * SQLite store holding one row per Pivot offer, per content level.
 *
 * This exists to keep whole-file reads off the request path: rendering one
 * offer page is an indexed lookup decoding a few KB, instead of json_decode()
 * over an entire level (22 MB at ContentLevel::Full). Lists filtered by offer
 * type are served by an index rather than by filtering every offer in PHP.
 *
 * The JSON files under data/pivot/ remain the raw API archive and the source
 * for `pivot:index`, but are no longer read to serve a request.
 */
class OfferStore
{
    private ?PDO $pdo = null;

    /** @var array<int, true> Levels already checked for content in this process. */
    private array $ensured = [];

    public function __construct(
        private readonly PivotCache $pivotCache,
        #[Autowire('%pivot.db_path%')]
        private readonly string $dbPath,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * Populate a level from its JSON archive if the table holds nothing for it.
     *
     * This makes the store self-healing: a fresh checkout, or a deleted
     * database file, costs one request an import (~0.3s for the largest level)
     * instead of silently serving an empty site.
     */
    public function ensureLevel(ContentLevel $level): void
    {
        if (isset($this->ensured[$level->value])) {
            return;
        }

        $this->ensured[$level->value] = true;

        if ($this->rowCount($level) > 0) {
            return;
        }

        $data = $this->pivotCache->get($level);
        if ($data === null) {
            $this->logger?->warning('Offer store: no rows and no JSON archive for level', [
                'level' => $level->value,
            ]);

            return;
        }

        $this->logger?->info('Offer store: importing level from JSON archive', ['level' => $level->value]);
        $this->replaceLevel($level, $data);
    }

    /**
     * @return array<string, mixed>|null The raw offer payload, or null when absent.
     */
    public function findByCode(string $codeCgt, ContentLevel $level): ?array
    {
        $this->ensureLevel($level);

        $statement = $this->connect()->prepare(
            'SELECT json FROM offer WHERE level = :level AND code_cgt = :code'
        );
        $statement->execute(['level' => $level->value, 'code' => $codeCgt]);

        $json = $statement->fetchColumn();
        $statement->closeCursor();

        if (!is_string($json)) {
            return null;
        }

        return $this->decode($json);
    }

    /**
     * @return Generator<int, array<string, mixed>>
     */
    public function findAll(ContentLevel $level): Generator
    {
        $this->ensureLevel($level);

        $statement = $this->connect()->prepare(
            'SELECT json FROM offer WHERE level = :level ORDER BY rowid'
        );
        $statement->execute(['level' => $level->value]);

        while (($json = $statement->fetchColumn()) !== false) {
            $offer = $this->decode($json);
            if ($offer !== null) {
                yield $offer;
            }
        }

        $statement->closeCursor();
    }

    /**
     * @param int[] $typeIds
     * @return Generator<int, array<string, mixed>>
     */
    public function findByTypeIds(array $typeIds, ContentLevel $level): Generator
    {
        if ($typeIds === []) {
            return;
        }

        $this->ensureLevel($level);

        $placeholders = implode(', ', array_fill(0, count($typeIds), '?'));
        $statement = $this->connect()->prepare(
            sprintf('SELECT json FROM offer WHERE level = ? AND type_offre IN (%s) ORDER BY rowid', $placeholders)
        );
        $statement->execute([$level->value, ...array_values($typeIds)]);

        while (($json = $statement->fetchColumn()) !== false) {
            $offer = $this->decode($json);
            if ($offer !== null) {
                yield $offer;
            }
        }

        $statement->closeCursor();
    }

    /**
     * Offer identity only, read from indexed columns without touching the JSON.
     *
     * @return list<array{codeCgt: string, nom: string, type: string}>
     */
    public function findShorts(ContentLevel $level): array
    {
        $this->ensureLevel($level);

        $statement = $this->connect()->prepare(
            'SELECT code_cgt, nom, type_label FROM offer WHERE level = :level ORDER BY nom COLLATE NOCASE'
        );
        $statement->execute(['level' => $level->value]);

        $shorts = [];
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $shorts[] = [
                'codeCgt' => (string) $row['code_cgt'],
                'nom' => (string) ($row['nom'] ?? ''),
                'type' => (string) ($row['type_label'] ?? ''),
            ];
        }

        return $shorts;
    }

    public function countOffers(ContentLevel $level): int
    {
        $this->ensureLevel($level);

        return $this->rowCount($level);
    }

    /**
     * Whether the level is already indexed, without triggering an import.
     */
    public function hasLevel(ContentLevel $level): bool
    {
        return $this->rowCount($level) > 0;
    }

    private function rowCount(ContentLevel $level): int
    {
        $statement = $this->connect()->prepare('SELECT COUNT(*) FROM offer WHERE level = :level');
        $statement->execute(['level' => $level->value]);

        return (int) $statement->fetchColumn();
    }

    /**
     * Replace every offer of one level, atomically.
     *
     * Readers keep seeing the previous contents until the transaction commits.
     *
     * @param array<string, mixed> $data A raw Pivot query response: {count, offre[]}.
     * @return int Number of offers written.
     */
    public function replaceLevel(ContentLevel $level, array $data): int
    {
        $offers = $data['offre'] ?? [];
        if (!is_array($offers)) {
            throw new RuntimeException('Pivot response has no usable "offre" list');
        }

        $pdo = $this->connect();
        $pdo->beginTransaction();

        try {
            $delete = $pdo->prepare('DELETE FROM offer WHERE level = :level');
            $delete->execute(['level' => $level->value]);

            $insert = $pdo->prepare(
                'INSERT INTO offer (level, code_cgt, type_offre, nom, type_label, json)
                 VALUES (:level, :code, :type, :nom, :type_label, :json)'
            );

            $written = 0;
            foreach ($offers as $offer) {
                if (!is_array($offer) || !isset($offer['codeCgt'])) {
                    continue;
                }

                $insert->execute([
                    'level' => $level->value,
                    'code' => (string) $offer['codeCgt'],
                    'type' => isset($offer['typeOffre']['idTypeOffre'])
                        ? (int) $offer['typeOffre']['idTypeOffre']
                        : null,
                    'nom' => $offer['nom'] ?? null,
                    'type_label' => $offer['typeOffre']['label'][0]['value'] ?? null,
                    'json' => json_encode($offer, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                ]);
                ++$written;
            }

            // Keep whatever the API sent alongside the offer list, so the
            // deserialized OfferResponse can carry the same envelope.
            $envelope = $data;
            unset($envelope['offre']);
            $meta = $pdo->prepare('INSERT OR REPLACE INTO meta (key, value) VALUES (:key, :value)');
            $meta->execute([
                'key' => sprintf('envelope_level_%d', $level->value),
                'value' => json_encode($envelope, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            ]);

            $pdo->commit();
        } catch (JsonException|PDOException $e) {
            $pdo->rollBack();
            $this->logger?->error('Offer store: failed to write level', [
                'level' => $level->value,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException(
                sprintf('Failed to index level %d: %s', $level->value, $e->getMessage()),
                0,
                $e,
            );
        }

        $this->ensured[$level->value] = true;

        $this->logger?->info('Offer store: level indexed', [
            'level' => $level->value,
            'offers' => $written,
        ]);

        return $written;
    }

    /**
     * The non-offer part of the original API response for this level.
     *
     * @return array<string, mixed>
     */
    public function getEnvelope(ContentLevel $level): array
    {
        $this->ensureLevel($level);

        $statement = $this->connect()->prepare('SELECT value FROM meta WHERE key = :key');
        $statement->execute(['key' => sprintf('envelope_level_%d', $level->value)]);

        $json = $statement->fetchColumn();
        $statement->closeCursor();

        if (!is_string($json)) {
            return [];
        }

        return $this->decode($json) ?? [];
    }

    public function deleteLevel(ContentLevel $level): bool
    {
        $pdo = $this->connect();
        $statement = $pdo->prepare('DELETE FROM offer WHERE level = :level');
        $statement->execute(['level' => $level->value]);

        $meta = $pdo->prepare('DELETE FROM meta WHERE key = :key');
        $meta->execute(['key' => sprintf('envelope_level_%d', $level->value)]);

        unset($this->ensured[$level->value]);

        return true;
    }

    public function getDbPath(): string
    {
        return $this->dbPath;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decode(string $json): ?array
    {
        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger?->warning('Offer store: invalid JSON in row', ['error' => $e->getMessage()]);

            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    private function connect(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dir = dirname($this->dbPath);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" could not be created', $dir));
        }

        $pdo = new PDO('sqlite:'.$this->dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // WAL lets the site keep reading the previous data while `pivot:fetch`
        // rewrites a level.
        $pdo->exec('PRAGMA journal_mode = WAL');
        $pdo->exec('PRAGMA synchronous = NORMAL');
        $pdo->exec('PRAGMA busy_timeout = 5000');

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS offer (
                level      INTEGER NOT NULL,
                code_cgt   TEXT    NOT NULL,
                type_offre INTEGER,
                nom        TEXT,
                type_label TEXT,
                json       TEXT    NOT NULL,
                PRIMARY KEY (level, code_cgt)
            )'
        );
        $pdo->exec('CREATE INDEX IF NOT EXISTS offer_level_type ON offer (level, type_offre)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS meta (key TEXT PRIMARY KEY, value TEXT NOT NULL)');

        return $this->pdo = $pdo;
    }
}
