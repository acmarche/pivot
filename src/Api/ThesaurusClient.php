<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Api;

use AcMarche\PivotAi\Cache\PivotCache;
use AcMarche\PivotAi\Entity\Pivot\Label;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ThesaurusClient
{
    /** @var array<string, array<int, array{lang: string, value: string}>>|null */
    private ?array $inMemoryCache = null;

    public function __construct(
        #[Autowire(env: 'PIVOT_BASE_URI')]
        private readonly string $baseUri,
        private readonly HttpClientInterface $httpClient,
        private readonly PivotCache $pivotCache,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * @return Label[]
     */
    public function getLabelsForUrn(string $urn): array
    {
        $cache = $this->loadCache();

        if (isset($cache[$urn])) {
            return $this->hydrateLabels($cache[$urn]);
        }

        $labels = $this->fetchUrnFromApi($urn);
        if ($labels !== null) {
            $cache[$urn] = $labels;
            $this->saveCache($cache);

            return $this->hydrateLabels($labels);
        }

        return [];
    }

    /**
     * Fetch and cache labels for multiple URNs at once.
     *
     * @param string[] $urns
     */
    public function fetchUrns(array $urns): int
    {
        $cache = $this->loadCache();
        $fetched = 0;

        foreach ($urns as $urn) {
            if (isset($cache[$urn])) {
                continue;
            }

            $labels = $this->fetchUrnFromApi($urn);
            if ($labels !== null) {
                $cache[$urn] = $labels;
                $fetched++;
            }
        }

        if ($fetched > 0) {
            $this->saveCache($cache);
        }

        return $fetched;
    }

    /**
     * @return array<string, array<int, array{lang: string, value: string}>>
     */
    public function loadCache(): array
    {
        if ($this->inMemoryCache !== null) {
            return $this->inMemoryCache;
        }

        $data = $this->pivotCache->getThesaurus();
        $this->inMemoryCache = $data ?? [];

        return $this->inMemoryCache;
    }

    public function clearCache(): void
    {
        $this->inMemoryCache = null;
        $this->pivotCache->clearThesaurus();
    }

    /**
     * @return array<int, array{lang: string, value: string}>|null
     */
    private function fetchUrnFromApi(string $urn): ?array
    {
        $url = sprintf('%s/urn/%s;fmt=json', $this->baseUri.'/thesaurus', $urn);

        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            $data = $response->toArray();
            unset($response);

            $spec = $data['spec'][0] ?? null;
            unset($data);
            if ($spec === null) {
                $this->logger?->warning('No spec data in thesaurus response', ['urn' => $urn]);

                return null;
            }

            return $spec['label'] ?? [];
        } catch (ExceptionInterface $e) {
            $this->logger?->warning('Failed to fetch thesaurus URN', [
                'urn' => $urn,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param array<int, array{lang: string, value: string}> $labelData
     * @return Label[]
     */
    private function hydrateLabels(array $labelData): array
    {
        return array_map(
            fn(array $item) => new Label(
                lang: $item['lang'],
                value: $item['value'],
            ),
            $labelData,
        );
    }

    /**
     * @param array<string, array<int, array{lang: string, value: string}>> $cache
     */
    private function saveCache(array $cache): void
    {
        $this->pivotCache->setThesaurus($cache);
        $this->inMemoryCache = $cache;
    }
}
