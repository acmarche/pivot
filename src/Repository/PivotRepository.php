<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Filtre\PivotFilter;
use AcMarche\Pivot\Parser\PivotParser;
use AcMarche\Pivot\Parser\PivotSerializer;
use AcMarche\Pivot\PivotTypeEnum;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Utils\SortUtils;
use Symfony\Contracts\Cache\CacheInterface;

class PivotRepository
{
    use SpecTrait;

    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private PivotParser $pivotParser,
        private PivotSerializer $pivotSerializer,
        private CacheInterface $cache
    ) {
    }

    /**
     * @return Offre[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffres(array $filtres): array
    {
        $offres = [];
        $responseQuery = $this->getAllDataFromRemote();

        $offresShort = PivotFilter::filterByTypes($responseQuery, $filtres);

        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->getOffreByCgt(
                $offreShort->codeCgt,
                $offreShort->dateModification,
                Offre::class
            );
            $offre = $resultOfferDetail;
            $offres[] = $offre;
            //    break;
        }

        array_map(function ($offre) {
            $this->pivotParser->parseOffre($offre);
        }, $offres);

        $this->parseRelOffres($offres);

        return $offres;
    }

    /**
     * Retourne la liste des events
     * @return Event[]
     */
    public function getEvents(bool $removeObsolete = false): array
    {
        $events = [];
        $responseQuery = $this->getAllDataFromRemote();
        $offresShort = PivotFilter::filterByTypes($responseQuery, [PivotTypeEnum::EVENEMENT]);
        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->getOffreByCgt(
                $offreShort->codeCgt,
                $offreShort->dateModification,
                Event::class
            );
            $offre = $resultOfferDetail;
            $events[] = $offre;
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
        ?string $dateModification = "xx",
        string $class = ResultOfferDetail::class
    ): ResultOfferDetail|Event|Offre|null {
        return $this->cache->get(
            'offre-'.$codeCgt.'-'.$dateModification,
            function () use ($codeCgt, $class) {
                $dataString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
                if ($class != ResultOfferDetail::class) {
                    $tmp = json_decode($dataString);
                    $dataStringOffre = json_encode($tmp->offre[0]);

                    $object = $this->pivotSerializer->deserializeToClass($dataStringOffre, $class);
                    $object->dataRaw = $dataString;

                    return $object;
                }
                $object = $this->pivotSerializer->deserializeToClass($dataString, ResultOfferDetail::class);
                $object->dataRaw = $dataString;

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

            return $this->pivotSerializer->deserializeToClass($dataString, ResponseQuery::class);
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
                    $item = $relation->offre;
                    $code = $item['codeCgt'];
                    $idType = $item['typeOffre']['idTypeOffre'];
                    try {
                        $sOffre = $this->getOffreByCgt($code, $item['dateModification']);
                        if ($sOffre) {
                            $this->specs = $sOffre->getOffre()->spec;
                            $images = $this->findByUrn(UrnList::URL);
                            if (count($images) > 0) {
                                foreach ($images as $image) {
                                    $offre->images[] = $image->value;
                                }
                            }
                            $images = $this->findByUrn(UrnList::MEDIAS_PARTIAL, true);
                            if (count($images) > 0) {
                                foreach ($images as $image) {
                                    $offre->images[] = $image->value;
                                }
                            }
                            $voirs = $this->findByUrn(UrnList::VOIR_AUSSI);
                            if (count($voirs) > 0) {
                                foreach ($voirs as $voir) {
                                    $offre->voirs_aussi[] = $voir;
                                }
                            }
                            $direction = $this->findByUrn(UrnList::CONTACT_DIRECTION);
                            if (count($direction) > 0) {
                                $offre->contact_direction = $direction[0];
                            }
                        }
                    } catch (\Exception $exception) {

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
        $data = [];
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

    public function getEventByIdHades(int $idHades): ?Event
    {
        $events = $this->getEvents(true);
        foreach ($events as $event) {
            if (count($event->hades_ids) > 0) {
                if ($idHades == $event->hades_ids[0]->value) {
                    return $event;
                }
            }
        }

        return null;
    }

    public function getTypesOffre(): array
    {
        return $this->cache->get('pivotAllTypes', function () {
            $resultString = $this->pivotRemoteRepository->query();

            $data = json_decode($resultString);

            $types = [];
            foreach ($data->offre as $offreInline) {
                $offreString = $this->pivotRemoteRepository->offreByCgt($offreInline->codeCgt);
                $offreObject = json_decode($offreString);
                $offre = $offreObject->offre[0];
                $type = $offre->typeOffre;
                $idType = $type->idTypeOffre;
                $labelType = $type->label[0]->value;
                $types[$idType] = $labelType;
            }

            ksort($types);

            return $types;
        });
    }

}
