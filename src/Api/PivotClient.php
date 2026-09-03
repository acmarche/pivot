<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Api;

use AcMarche\PivotAi\Cache\OfferStore;
use AcMarche\PivotAi\Cache\PivotCache;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Entity\Pivot\OfferResponse;
use AcMarche\PivotAi\Enums\ContentLevel;
use AcMarche\PivotAi\Parser\PivotSerializer;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class PivotClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private PivotSerializer $pivotSerializer,
        private PivotCache $pivotCache,
        private OfferStore $offerStore,
        #[Autowire(env: 'PIVOT_BASE_URI')]
        private string $baseUri,
        #[Autowire(env: 'PIVOT_CODE_QUERY'), \SensitiveParameter]
        private string $codeQuery,
        #[Autowire(env: 'PIVOT_WS_KEY'), \SensitiveParameter]
        private string $wsKey,
        private ContentLevel $defaultContentLevel = ContentLevel::Full,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function fetchOffersByCriteria(?ContentLevel $contentLevel = null, bool $useCache = true): OfferResponse
    {
        $level = $contentLevel ?? $this->defaultContentLevel;

        if ($useCache && $this->offerStore->countOffers($level) > 0) {
            return $this->buildResponseFromStore($level);
        }

        $data = $this->fetchFromApi($level);
        $response = $this->pivotSerializer->deserializeOfferResponse($data);
        if ($response->count > 150) {
            $this->store($level, $data);
        }
        unset($data);

        return $response;
    }

    /**
     * Rebuild an OfferResponse one row at a time.
     *
     * Nothing ever holds the whole level as a decoded array, so peak memory is
     * the Offer objects alone rather than the objects plus a 22 MB payload.
     */
    private function buildResponseFromStore(ContentLevel $level): OfferResponse
    {
        $response = $this->pivotSerializer->deserializeOfferResponse($this->offerStore->getEnvelope($level));

        foreach ($this->offerStore->findAll($level) as $offerData) {
            $response->addOffre($this->pivotSerializer->deserializeOffer($offerData));
        }

        return $response;
    }

    /**
     * Persist a freshly fetched level: JSON file as the raw archive, SQLite as
     * the queryable index the request path reads.
     *
     * @param array<string, mixed> $data
     */
    private function store(ContentLevel $level, array $data): void
    {
        $this->pivotCache->set($level, $data);
        $this->offerStore->replaceLevel($level, $data);
    }

    /**
     * @param ContentLevel $level
     * @return array
     */
    public function fetchFromApi(ContentLevel $level): array
    {
        $url = sprintf('%s/query/%s;content=%d', $this->baseUri, $this->codeQuery, $level->value);

        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'ws_key' => $this->wsKey,
                ],
            ]);

            $data = $response->toArray();
            unset($response);

            return $data;
        } catch (ExceptionInterface $e) {
            $this->logger?->error('Failed to fetch offers from Pivot API', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Failed to fetch offers from Pivot API: '.$e->getMessage(), 0, $e);
        }
    }

    public function loadOffer(string $codeCgt, ?ContentLevel $contentLevel = null): ?Offer
    {
        $level = $contentLevel ?? $this->defaultContentLevel;

        $offerData = $this->offerStore->findByCode($codeCgt, $level);
        if ($offerData === null) {
            return null;
        }

        return $this->pivotSerializer->deserializeOffer($offerData);
    }

    /**
     * Offers of the given types, served by index rather than by filtering the
     * whole level in PHP.
     *
     * @param int[] $typeIds
     * @return Offer[]
     */
    public function loadOffersByTypeIds(array $typeIds, ?ContentLevel $contentLevel = null): array
    {
        $level = $contentLevel ?? $this->defaultContentLevel;

        $offers = [];
        foreach ($this->offerStore->findByTypeIds($typeIds, $level) as $offerData) {
            $offers[] = $this->pivotSerializer->deserializeOffer($offerData);
        }

        return $offers;
    }

    /**
     * Code, name and type only, read from indexed columns without deserializing
     * a single offer.
     *
     * @return list<array{codeCgt: string, nom: string, type: string}>
     */
    public function loadOffersShort(?ContentLevel $contentLevel = null): array
    {
        return $this->offerStore->findShorts($contentLevel ?? $this->defaultContentLevel);
    }

    public function fetchOfferByCode(string $codeCgt, ?ContentLevel $contentLevel = null): ?OfferResponse
    {
        $level = $contentLevel ?? $this->defaultContentLevel;
        $url = sprintf('%s/offer/%s;content=%d', $this->baseUri, $codeCgt, $level->value);

        $httpResponse = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'ws_key' => $this->wsKey,
            ],
        ]);

        $data = $httpResponse->toArray();
        unset($httpResponse);

        $response = $this->pivotSerializer->deserializeOfferResponse($data);
        unset($data);

        return $response;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function getCodeQuery(): string
    {
        return $this->codeQuery;
    }

    public function clearCache(?ContentLevel $contentLevel = null): bool
    {
        return $this->pivotCache->clear($contentLevel);
    }
}
