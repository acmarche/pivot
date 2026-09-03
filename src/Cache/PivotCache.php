<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Cache;

use AcMarche\PivotAi\Enums\ContentLevel;
use JsonException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * File cache for Pivot API responses.
 *
 * Each content level is stored as a single JSON file under the data directory.
 * There is no separate in-memory layer: the files are the store, and the OS
 * page cache keeps the hot ones resident. Freshness comes from the
 * `pivot:fetch` command rewriting the files, not from an expiry.
 */
class PivotCache
{
    public function __construct(
        #[Autowire(env: 'PIVOT_CODE_QUERY'), \SensitiveParameter]
        private readonly string $codeQuery,
        #[Autowire('%kernel.project_dir%/data/pivot/')]
        private readonly string $dataDir,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function get(ContentLevel $level): ?array
    {
        return $this->readFromFile($level);
    }

    public function set(ContentLevel $level, array $data): void
    {
        $this->writeToFile($level, $data);
    }

    /**
     * No-op kept for API compatibility.
     *
     * With the file store there is nothing to invalidate: set() replaces the
     * file in place and the next read sees the new content. Use deleteFile()
     * to actually remove cached data.
     */
    public function clear(?ContentLevel $level = null): bool
    {
        return true;
    }

    public function deleteFile(ContentLevel $level): bool
    {
        $file = $this->getFilePath($level);
        if (file_exists($file) && !unlink($file)) {
            $this->logger?->error('Pivot cache: failed to delete file', ['file' => $file]);

            return false;
        }

        return true;
    }

    public function getThesaurus(): ?array
    {
        return $this->readThesaurusFromFile();
    }

    public function setThesaurus(array $data): void
    {
        $this->writeThesaurusToFile($data);
    }

    /**
     * No-op kept for API compatibility. See clear().
     */
    public function clearThesaurus(): bool
    {
        return true;
    }

    public function deleteThesaurusFile(): bool
    {
        $file = $this->getThesaurusFilePath();
        if (file_exists($file) && !unlink($file)) {
            $this->logger?->error('Thesaurus cache: failed to delete file', ['file' => $file]);

            return false;
        }

        return true;
    }

    public function getFilePath(ContentLevel $level): string
    {
        return sprintf('%spivot_offers_query_%s_level_%d.json', $this->dataDir, $this->codeQuery, $level->value);
    }

    private function readFromFile(ContentLevel $level): ?array
    {
        return $this->readJsonFromFile($this->getFilePath($level));
    }

    private function writeToFile(ContentLevel $level, array $data): void
    {
        $this->writeJsonToFile($this->getFilePath($level), $data);
    }

    private function readThesaurusFromFile(): ?array
    {
        return $this->readJsonFromFile($this->getThesaurusFilePath());
    }

    private function writeThesaurusToFile(array $data): void
    {
        $this->writeJsonToFile($this->getThesaurusFilePath(), $data);
    }

    private function getThesaurusFilePath(): string
    {
        return $this->dataDir.'thesaurus_urns.json';
    }

    private function readJsonFromFile(string $file): ?array
    {
        if (!file_exists($file) || !is_readable($file)) {
            return null;
        }

        try {
            $content = file_get_contents($file);
            if ($content === false) {
                $this->logger?->warning('Pivot cache: failed to read file', ['file' => $file]);

                return null;
            }

            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            unset($content);

            $this->logger?->debug('Pivot cache: loaded from file', ['file' => $file]);

            return $data;
        } catch (JsonException $e) {
            $this->logger?->warning('Pivot cache: invalid JSON in file', [
                'file' => $file,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function writeJsonToFile(string $file, array $data): void
    {
        try {
            $dir = dirname($file);
            if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new RuntimeException(sprintf('Directory "%s" could not be created', $dir));
            }

            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

            if (file_put_contents($file, $json) === false) {
                unset($json);
                throw new RuntimeException('Failed to write cache file');
            }

            unset($json);
            $this->logger?->debug('Pivot cache: written to file', ['file' => $file]);
        } catch (JsonException $e) {
            $this->logger?->error('Pivot cache: failed to encode JSON', ['error' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            $this->logger?->error('Pivot cache: failed to write file', [
                'file' => $file,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
