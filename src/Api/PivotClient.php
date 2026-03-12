<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Api;

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

        if ($useCache) {
            $data = $this->pivotCache->get($level);
            if ($data !== null) {
                $response = $this->pivotSerializer->deserializeOfferResponse($data);
                unset($data);

                return $response;
            }
        }

        $data = $this->fetchFromApi($level);
        $this->pivotCache->set($level, $data);

        $response = $this->pivotSerializer->deserializeOfferResponse($data);
        unset($data);

        return $response;
    }

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
        $cachedData = $this->pivotCache->get($level);

        if ($cachedData === null) {
            return null;
        }

        $matchedOfferData = null;
        foreach ($cachedData['offre'] ?? [] as $offerData) {
            if (($offerData['codeCgt'] ?? null) === $codeCgt) {
                $matchedOfferData = $offerData;
                break;
            }
        }
        unset($cachedData);

        if ($matchedOfferData === null) {
            return null;
        }

        $offer = $this->pivotSerializer->deserializeOffer($matchedOfferData);
        unset($matchedOfferData);

        return $offer;
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
