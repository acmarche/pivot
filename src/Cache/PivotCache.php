<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Cache;

use AcMarche\PivotAi\Enums\ContentLevel;
use JsonException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Marshaller\TagAwareMarshaller;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PivotCache
{
    private const int TTL = 72000; // 20 hours

    public function __construct(
        private CacheInterface $pivotCache,
        #[Autowire(env: 'PIVOT_CODE_QUERY'), \SensitiveParameter]
        private readonly string $codeQuery,
        #[Autowire('%kernel.project_dir%/data/pivot/')]
        private readonly string $dataDir,
        private readonly ?LoggerInterface $logger = null,
    ) {
        $marshaller = new TagAwareMarshaller();
        $redis = RedisAdapter::createConnection('redis://localhost', [
            'timeout' => 15,
        ]);
        $this->pivotCache = new RedisAdapter($redis, 'visit_namespace', 3600, $marshaller);
    }

    public function get(ContentLevel $level): ?array
    {
        $key = $this->getCacheKey($level);

        // Try Redis first
        try {
            $data = $this->pivotCache->get($key, function (ItemInterface $item) use ($level) {
                // Redis miss — try file cache before calling API
                $fileData = $this->readFromFile($level);
                if ($fileData !== null) {
                    $item->expiresAfter(self::TTL);
                    $this->logger?->info('Pivot cache: loaded from file, stored in Redis', ['level' => $level->value]);

                    return $fileData;
                }

                // Neither Redis nor file has data — signal caller to fetch from API
                throw new CacheMissException();
            });

            $this->logger?->info('Pivot cache: hit from Redis', ['level' => $level->value]);

            return $data;
        } catch (CacheMissException) {
            return null;
        } catch (\Throwable $e) {
            $this->logger?->warning('Pivot cache: Redis error, falling back to file', [
                'level' => $level->value,
                'error' => $e->getMessage(),
            ]);
        }

        // Redis unavailable — try file directly
        return $this->readFromFile($level);
    }

    public function set(ContentLevel $level, array $data): void
    {
        // Write to file
        $this->writeToFile($level, $data);

        // Write to Redis
        $key = $this->getCacheKey($level);
        try {
            $this->pivotCache->delete($key);
            $this->pivotCache->get($key, function (ItemInterface $item) use ($data) {
                $item->expiresAfter(self::TTL);

                return $data;
            });
            $this->logger?->info('Pivot cache: stored in Redis', ['key' => $key]);
        } catch (\Throwable $e) {
            $this->logger?->warning('Pivot cache: failed to store in Redis', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function clear(?ContentLevel $level = null): bool
    {
        $levels = $level !== null ? [$level] : ContentLevel::cases();
        $success = true;

        foreach ($levels as $l) {
            // Clear Redis only — JSON files are kept as fallback until
            // new data is successfully fetched and written via set()
            try {
                $this->pivotCache->delete($this->getCacheKey($l));
            } catch (\Throwable $e) {
                $this->logger?->warning('Pivot cache: failed to clear Redis key', [
                    'level' => $l->value,
                    'error' => $e->getMessage(),
                ]);
                $success = false;
            }
        }

        return $success;
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
        $key = 'pivot_thesaurus_urns';

        try {
            $data = $this->pivotCache->get($key, function (ItemInterface $item) {
                $fileData = $this->readThesaurusFromFile();
                if ($fileData !== null) {
                    $item->expiresAfter(self::TTL);
                    $this->logger?->info('Thesaurus cache: loaded from file, stored in Redis');

                    return $fileData;
                }

                throw new CacheMissException();
            });

            return $data;
        } catch (CacheMissException) {
            return null;
        } catch (\Throwable $e) {
            $this->logger?->warning('Thesaurus cache: Redis error, falling back to file', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->readThesaurusFromFile();
    }

    public function setThesaurus(array $data): void
    {
        $this->writeThesaurusToFile($data);

        $key = 'pivot_thesaurus_urns';
        try {
            $this->pivotCache->delete($key);
            $this->pivotCache->get($key, function (ItemInterface $item) use ($data) {
                $item->expiresAfter(self::TTL);

                return $data;
            });
            $this->logger?->info('Thesaurus cache: stored in Redis');
        } catch (\Throwable $e) {
            $this->logger?->warning('Thesaurus cache: failed to store in Redis', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function clearThesaurus(): bool
    {
        $success = true;

        // Clear Redis only — JSON file kept as fallback until new data is written via setThesaurus()
        try {
            $this->pivotCache->delete('pivot_thesaurus_urns');
        } catch (\Throwable $e) {
            $this->logger?->warning('Thesaurus cache: failed to clear Redis key', [
                'error' => $e->getMessage(),
            ]);
            $success = false;
        }

        return $success;
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

    private function getCacheKey(ContentLevel $level): string
    {
        return sprintf('pivot_offers_%s_level_%d', $this->codeQuery, $level->value);
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

            $this->logger?->info('Pivot cache: loaded from file', ['file' => $file]);

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
            $this->logger?->info('Pivot cache: written to file', ['file' => $file]);
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
