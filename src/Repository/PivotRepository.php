<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Hebergement\Hotel;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Entities\Specification\SpecEvent;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Filtre\PivotFilter;
use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Parser\PivotSerializer;
use AcMarche\Pivot\PivotTypeEnum;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Utils\SortUtils;
use Symfony\Contracts\Cache\CacheInterface;

class PivotRepository
{
    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private PivotParser $pivotParser,
        private PivotSerializer $pivotSerializer,
        private CacheInterface $cache
    ) {
    }

    /**
     * Retourne la liste des events
     * @return Event[]
     */
    public function getEvents(bool $removeObsolete = false): array
    {
        $events        = [];
        $responseQuery = $this->getAllDataFromRemote();
        $offresShort   = PivotFilter::filterByType($responseQuery, PivotTypeEnum::EVENEMENT);
        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->getOffreByCgt(
                $offreShort->codeCgt,
                $offreShort->dateModification,
                Event::class
            );
            $offre             = $resultOfferDetail;
            $events[]          = $offre;
            //break;
        }
        $this->pivotParser->parseEvents($events, $removeObsolete);
        $this->parseRelOffres($events);

        $events = SortUtils::sortEvents($events);
        if ($removeObsolete) {
            $events = EventUtils::removeObsolete($events);
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
        $events        = [];
        $responseQuery = $this->getAllDataFromRemote();
        $offresShort   = PivotFilter::filterByType($responseQuery, PivotTypeEnum::HOTEL);

        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->getOffreByCgt(
                $offreShort->codeCgt,
                $offreShort->dateModification,
                Hotel::class
            );
            $offre             = $resultOfferDetail;
            $events[]          = $offre;
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
     *
     * @return ResultOfferDetail|Event|Offre|null
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffreByCgt(
        string $codeCgt,
        string $dateModification = "xx",
        string $class = ResultOfferDetail::class
    ): ResultOfferDetail|Event|Offre|null {
        return $this->cache->get(
            'offre-'.$codeCgt.'-'.$dateModification,
            function () use ($codeCgt, $class) {
                $dataString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
                if ($class != ResultOfferDetail::class) {
                    $tmp             = json_decode($dataString);
                    $dataStringOffre = json_encode($tmp->offre[0]);

                    $object       = $this->pivotSerializer->deserializeToClass($dataStringOffre, $class);
                    $object->data = $dataString;

                    return $object;
                }
                $object       = $this->pivotSerializer->deserializeToClass($dataString, ResultOfferDetail::class);
                $object->data = $dataString;

                return $object;
            }
        );
    }

    public function getEvent(string $codeCgt): ?Event
    {
        $event = $this->getOffreByCgt($codeCgt, $codeCgt, Event::class);
        $this->pivotParser->parseEvent($event);
        $this->parseRelOffres([$event]);

        return $event;
    }

    /**
     * Retourne le json (string) complet du query
     */
    private function getAllDataFromRemote(): ?ResponseQuery
    {
        return $this->cache->get('pivotAllData', function () {
            $dataString = $this->pivotRemoteRepository->query();

            return $this->pivotSerializer->deserializeToClass($dataString, ResponseQuery::class, 'json');
        });
    }

    /**
     * @param array $offres
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function parseRelOffres(array $offres): void
    {
        foreach ($offres as $offre) {
            if (is_array($offre->relOffre)) {
                foreach ($offre->relOffre as $relation) {
                    $item   = $relation->offre;
                    $code   = $item['codeCgt'];
                    $idType = $item['typeOffre']['idTypeOffre'];
                    $sOffre = $this->getOffreByCgt($code, $item['dateModification']);
                    if ($sOffre) {
                        $itemSpec = new SpecEvent($sOffre->getOffre()->spec);
                        $images   = $itemSpec->findByUrn(UrnList::URL);
                        if (count($images) > 0) {
                            $offre->images[] = $images[0]->value;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Event $eventReffer
     *
     * @return Event[]
     */
    public function getSameEvents(Event $eventReffer): array
    {
        $data   = [];
        $events = $this->getEvents(true);
        foreach ($events as $event) {
            foreach ($event->categories as $category) {
                if (in_array(
                    $category->id,
                    array_map(function ($category) {
                        return $category->id;
                    }, $eventReffer->categories)
                )) {
                    $data[] = $event;
                }
            }
        }

        return $data;

    }
}
