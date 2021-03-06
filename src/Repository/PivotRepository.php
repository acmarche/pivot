<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Api\ThesaurusEnum;
use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Family\Family;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Parser\OffreParser;
use AcMarche\Pivot\Parser\PivotSerializer;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\TypeOffre\PivotType;
use AcMarche\Pivot\Utils\SortUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Cache\CacheInterface;

class PivotRepository
{
    use SpecTrait;

    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private OffreParser $pivotParser,
        private PivotSerializer $pivotSerializer,
        private CacheInterface $cache,
        private SluggerInterface $slugger
    ) {
    }

    /**
     * @param TypeOffre[] $typesOffre
     * @return Offre[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getOffres(array $typesOffre): array
    {
        $offres = [];
        $responseQuery = $this->getAllDataFromRemote();

        foreach ($responseQuery->offre as $offreShort) {
            try {
                $offre = $this->getOffreByCgt(
                    $offreShort->codeCgt,
                    Offre::class,
                    $offreShort->dateModification
                );
                $offres[] = $offre;
            } catch (\Exception $exception) {
                //todo add logger
                var_dump($exception);
            }
        }

        $offres = PivotType::filterByTypeIdsOrUrns($offres, $typesOffre);

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
        $offresShort = PivotType::filterByTypes($responseQuery, [UrnTypeList::evenement()->typeId]);
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
     * Si une classe est donn??e au param??tre $class,
     * une instance de cette classe est retourn??e
     *
     * @param string $codeCgt
     * @param string $dateModification
     * @param string $class
     *
     * @return ResultOfferDetail|Event|Offre|null
     * @throws \Psr\Cache\InvalidArgumentException|\Exception
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

        $keyUnicode = new UnicodeString($cacheKey);
        $key = $this->slugger->slug($keyUnicode->ascii()->toString());

        return $this->cache->get(
            'offre-'.$key,
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
     * @return ResponseQuery|null
     * @throws \Psr\Cache\InvalidArgumentException|\Exception
     */
    public function getAllDataFromRemote(): ?ResponseQuery
    {
        return $this->cache->get('pivotAllData55', function () {
            if ($dataString = $this->pivotRemoteRepository->query()) {
                return $this->pivotSerializer->deserializeToClass($dataString, ResponseQuery::class);
            }

            return null;
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
        $typesOffre = [$offreReffer->typeOffre->idTypeOffre];
        $data = [];
        $offres = $this->getOffres($typesOffre);
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

    /**
     * @return Family[]
     * @throws \Exception
     */
    public function thesaurusFamilies(): array
    {
        $familiesObject = json_decode($this->pivotRemoteRepository->thesaurusFamily());

        return $this->pivotSerializer->deserializeToClass(
            json_encode($familiesObject->spec),
            'AcMarche\Pivot\Entities\Family\Family[]',
        );
    }

    /**
     * https://organismes.tourismewallonie.be/doc-pivot-gest/liste-des-types-durn/
     * @return TypeOffre[]
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Exception
     */
    private function getTypesRootForCreateTypesOffre(): array
    {
        $typesOffre = [];
        if ($data = $this->pivotRemoteRepository->thesaurus(ThesaurusEnum::THESAURUS_TYPE_OFFRE->value)) {
            $thesaurus = json_decode($data);
            foreach ($thesaurus->spec as $spec) {
                $typeOffre = new TypeOffre($spec->label[0]->value, $spec->order, $spec->urn, null);
                $typeOffre->root = $spec->root;
                $typeOffre->code = $spec->code;
                $typesOffre[] = $typeOffre;
            }
        }

        return $typesOffre;
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/261/urn:fld:cat;fmt=xml
     * @param TypeOffre $parent
     * @return TypeOffre[]
     * @throws \Exception
     */
    private function getSousTypesForCreateTypesOffre(TypeOffre $parent): array
    {
        $typesOffre = [];
        $urn = match ($parent->typeId) {
            9, 267, 258, 259 => $parent->code,
            default => ''
        };

        $dataString = $this->pivotRemoteRepository->thesaurusSousTypes($parent->typeId, $urn);

        $data = json_decode($dataString);
        foreach ($data->spec as $spec) {
            if (!isset($spec->spec)) {
                continue;
            }
            foreach ($spec->spec as $item) {
                $labels = $item->label;
                $typeOffre = new TypeOffre(
                    $item->label[0]->value,
                    $item->order,
                    $item->urn,
                    $parent,
                    self::getLabel($labels, 'nl'),
                    self::getLabel($labels, 'en'),
                    self::getLabel($labels, 'de'),
                );
                $typesOffre[] = $typeOffre;
            }
        }

        return $typesOffre;
    }

    private static function getLabel(array $labels, string $language): ?string
    {
        foreach ($labels as $label) {
            if ($label->lang == $language) {
                return $label->value;
            }
        }

        return null;
    }
}
