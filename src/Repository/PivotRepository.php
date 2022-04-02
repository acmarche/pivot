<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Pivot\Offer;
use AcMarche\Pivot\Entities\Pivot\OffreShort;
use AcMarche\Pivot\Entities\Pivot\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Pivot\Response\ResultOfferDetail;
use AcMarche\Pivot\Filtre\PivotFilter;
use AcMarche\Pivot\PivotType;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PivotRepository
{
    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private SerializerInterface $serializer,
        private CacheInterface $cache
    ) {
    }

    /**
     * @return array|Offer[]
     */
    public function getEvents(): array
    {
        $events = [];
        $responseQuery = $this->getAllData();
        $offresShort = PivotFilter::filterByType($responseQuery, PivotType::EVENEMENT);
        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->offreByCgt($offreShort->codeCgt, $offreShort->dateModification);
            $events[] = $resultOfferDetail->getOffre();
            //break;
        }

        return $events;
    }

    public function offreByCgt(string $codeCgt, string $dateModification): ?ResultOfferDetail
    {
        return $this->cache->get(
            'offre-'.$codeCgt.'-'.$dateModification,
            function () use ($codeCgt) {
                $data = $this->pivotRemoteRepository->offreByCgt($codeCgt);

                return $this->serializer->deserialize($data, ResultOfferDetail::class, 'json', [
                    DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS,
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                ]);
            }
        );
    }

    private function getAllData(): ?ResponseQuery
    {
        return $this->cache->get('pivotAllData', function () {
            try {
                $dataString = $this->pivotRemoteRepository->query();

                return $this->serializer->deserialize($dataString, ResponseQuery::class, 'json');
            } catch (TransportExceptionInterface $e) {
            }

            return null;
        });
    }
}
