<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Hebergement\Hotel;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Filtre\PivotFilter;
use AcMarche\Pivot\PivotTypeEnum;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
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
     * Retourne la liste des events
     * @return array|Event[]
     */
    public function getEvents(): array
    {
        $events = [];
        $responseQuery = $this->getAllDataFromRemote();
        $offresShort = PivotFilter::filterByType($responseQuery, PivotTypeEnum::EVENEMENT);
        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->offreByCgt($offreShort->codeCgt, $offreShort->dateModification, Event::class);
            $offre = $resultOfferDetail;
            $events[] = $offre;
            //break;
        }

        return $events;
    }

    /**
     * Retourne la liste des events
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getHotels(): array
    {
        $events = [];
        $responseQuery = $this->getAllDataFromRemote();
        dd($responseQuery);
        $offresShort = PivotFilter::filterByType($responseQuery, PivotTypeEnum::HOTEL);

        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->offreByCgt($offreShort->codeCgt, $offreShort->dateModification, Hotel::class);
            $offre = $resultOfferDetail;
            $events[] = $offre;
            //    break;
        }

        return $events;
    }

    /***
     * Retourne une offre
     * Si une classe est donnée au paramètre $class,
     * une instance de cette classe est retournée
     *
     * @param string $codeCgt
     * @param string $dateModification
     * @param string $class
     * @return ResultOfferDetail|Event|Offre|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function offreByCgt(
        string $codeCgt,
        string $dateModification = "xx",
        string $class = ResultOfferDetail::class
    ): ResultOfferDetail|Event|null|Offre {
        return $this->cache->get(
            'offre-'.time().$codeCgt.'-'.$dateModification,
            function () use ($codeCgt, $class) {
                $dataString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
                if ($class != ResultOfferDetail::class) {
                    $tmp = json_decode($dataString);
                    $dataString = json_encode($tmp->offre[0]);

                    return $this->serializer->deserialize($dataString, $class, 'json', [
                        DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                        AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                    ]);
                }
                try {
                    $t = $this->serializer->deserialize($dataString, ResultOfferDetail::class, 'json', [
                        DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                        AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                    ]);
                    return $t;
                } catch (PartialDenormalizationException $exception) {
                    $this->getErrors($exception);
                }

                return null;
            }
        );
    }

    /**
     * Retourne le json (string) complet du query
     * @return ResponseQuery|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getAllDataFromRemote(): ?ResponseQuery
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

    private function getErrors(\Exception|PartialDenormalizationException $exception)
    {
        $violations = new ConstraintViolationList();
        /** @var NotNormalizableValueException */
        foreach ($exception->getErrors() as $exception) {
            dump($exception);
            $message = sprintf(
                'The type must be one of "%s" ("%s" given).',
                implode(', ', $exception->getExpectedTypes()),
                $exception->getCurrentType()
            );
            $parameters = [];
            if ($exception->canUseMessageForUser()) {
                $parameters['hint'] = $exception->getMessage();
            }
            $violations->add(
                new ConstraintViolation($message, '', $parameters, null, $exception->getPath(), null)
            );
        }
    }
}
