<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Entity\Filtre;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Filtre\PivotFilter;
use AcMarche\Pivot\Parser\OffreParser;
use AcMarche\Pivot\Parser\PivotSerializer;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Utils\SortUtils;
use Symfony\Contracts\Cache\CacheInterface;

class PivotRepository
{
    use SpecTrait;

    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private OffreParser $pivotParser,
        private PivotSerializer $pivotSerializer,
        private CacheInterface $cache
    ) {
    }

    /**
     * @param Filtre[] $filtres
     * @return Offre[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffres(array $filtres): array
    {
        $offres = [];
        $responseQuery = $this->getAllDataFromRemote();

        //$offresShort = PivotFilter::filterByReferencesOrUrns($responseQuery, $filtres);

        foreach ($responseQuery->offre as $offreShort) {
            try {
                $offre = $this->getOffreByCgt(
                    $offreShort->codeCgt,
                    Offre::class,
                    $offreShort->dateModification
                );
                $offres[] = $offre;
            } catch (\Exception $exception) {

            }
        }

        $offres = PivotFilter::filterByReferencesOrUrns($offres, $filtres);

        array_map(function ($offre) {
            $this->pivotParser->parseOffre($offre);
        }, $offres);

        $this->parseRelOffres($offres);
        $this->parseRelOffresTgt($offres);

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
        $offresShort = PivotFilter::filterByTypes($responseQuery, [UrnTypeList::evenement()->order]);
        foreach ($offresShort as $offreShort) {
            $resultOfferDetail = $this->getOffreByCgt(
                $offreShort->codeCgt,
                Event::class,
                $offreShort->dateModification
            );
            $offre = $resultOfferDetail;
            $events[] = $offre;
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
        string $class = ResultOfferDetail::class,
        string $cacheKeyPlus = null
    ): ResultOfferDetail|Event|Offre|null {

        $cacheKey = $codeCgt.$class;
        if ($cacheKeyPlus) {
            $cacheKey .= $cacheKeyPlus;
        }

        return $this->cache->get(
            'offre-'.$cacheKey,
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
                if ($object) {
                    $object->dataRaw = $dataString;
                }

                return $object;
            }
        );
    }

    public function getEvent(string $codeCgt): ?Event
    {
        $event = $this->getOffreByCgt($codeCgt, Event::class);
        $this->pivotParser->parseEvent($event);
        $this->parseRelOffres([$event]);

        return $event;
    }

    public function getOffreByCgtAndParse(string $codeCgt, string $class): ?Offre
    {
        $offre = $this->getOffreByCgt($codeCgt, $class);
        if ($offre) {
            $this->pivotParser->parseOffre($offre);
            $this->parseRelOffres([$offre]);
            $this->parseRelOffresTgt([$offre]);
        }

        return $offre;
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
     * @param Offre[] $offres
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function parseRelOffres(array $offres): void
    {
        foreach ($offres as $offre) {
            foreach ($offre->relOffre as $relation) {
                $item = $relation->offre;
                $code = $item['codeCgt'];
                try {
                    $sOffre = $this->getOffreByCgt($code);
                } catch (\Exception $exception) {
                    continue;
                }
                $this->specs = $sOffre->getOffre()->spec;
                if ($relation->urn == UrnList::MEDIAS_AUTRE->value) {
                    //   $offre->images[] = $this->getOffreByCgt($code, Offre::class);
                }
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
                if ($relation->urn == UrnList::CONTACT_DIRECTION->value) {
                    $offre->contact_direction = $this->getOffreByCgt($code, Offre::class);
                }
            }
        }
    }

    /**
     * @param Offre[] $offres
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function parseRelOffresTgt(array $offres): void
    {
        foreach ($offres as $offre) {
            foreach ($offre->relOffreTgt as $relOffreTgt) {
                $item = $relOffreTgt->offre;
                $code = $item['codeCgt'];
                try {
                    $offreTgt = $this->getOffreByCgt($code, Offre::class);
                } catch (\Exception $exception) {
                    continue;
                }
                if ($relOffreTgt->urn == UrnList::VOIR_AUSSI->value) {
                    $offre->voir_aussis[] = $offreTgt;
                }
                $this->specs = $offre->relOffreTgt;
                foreach ($this->findByUrn(UrnList::OFFRE_ENFANT) as $enfant) {
                    $offre->enfants[] = $offreTgt;
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

    /**
     * @param Offre $eventReffer
     *
     * @return Offre[]
     */
    public function getSameOffres(Offre $offreReffer): array
    {
        $filtres = [$offreReffer->typeOffre->idTypeOffre];
        $data = [];
        $offres = $this->getOffres($filtres);
        foreach ($offres as $offre) {
            if ($offreReffer->codeCgt != $offre->codeCgt) {
                $data[] = $offre;
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

    public function getOffreByIdHades(int $idHades): ?Offre
    {
        $offres = $this->getOffres([]);
        foreach ($offres as $offre) {
            if (count($offre->hades_ids) > 0) {
                if ($idHades == $offre->hades_ids[0]->value) {
                    return $offre;
                }
            }
        }

        return null;
    }

    public function getTypesRootForCreateFiltres(): array
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

    /**
     * @param int $reference
     * @return Offre[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffresForCreateFiltres(int $reference): array
    {
        $offres = [];
        $responseQuery = $this->getAllDataFromRemote();
        $offresShort = PivotFilter::filterByTypes($responseQuery, [$reference]);

        foreach ($offresShort as $offreShort) {
            try {
                $offre = $this->getOffreByCgt(
                    $offreShort->codeCgt,
                    Offre::class,
                    $offreShort->dateModification
                );
                $offres[] = $offre;
            } catch (\Exception $exception) {

            }
        }

        array_map(function ($offre) {
            $this->pivotParser->parseOffre($offre);
        }, $offres);

        return $offres;
    }

}
