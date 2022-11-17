<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Entities\Family\Family;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Response\ResultOfferDetail;
use AcMarche\Pivot\Entities\Urn\UrnDefinition;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Parser\OffreParser;
use AcMarche\Pivot\Serializer\PivotSerializer;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\TypeOffre\FilterUtils;
use AcMarche\Pivot\Utils\SortUtils;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Cache\CacheInterface;

class PivotRepository
{
    public function __construct(
        private PivotRemoteRepository $pivotRemoteRepository,
        private TypeOffreRepository $typeOffreRepository,
        private OffreParser $offreParser,
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
    public function fetchOffres(array $typesOffre, bool $parse = true): array
    {
        if (count($typesOffre) === 0) {
            return [];
        }

        $responseQuery = $this->getAllDataFromRemote();

        $offres = $this->cache->get('allOffresFetched', function () use ($responseQuery) {
            $offres = [];
            foreach ($responseQuery->offre as $offreShort) {
                try {
                    $offre = $this->fetchOffreByCgt(
                        $offreShort->codeCgt,
                        Offre::class,
                        $offreShort->dateModification
                    );
                    if ($offre instanceof Offre) {
                        $offres[] = $offre;
                    }
                } catch (\Exception $exception) {
                    //todo add logger
                    dd($exception);
                }
            }

            return $offres;
        });

        if (count($typesOffre) > 0) {
            $typeIds = FilterUtils::extractIds($typesOffre);
            $urns = array_column($typesOffre, 'urn');
            $offres = FilterUtils::filterByTypeIdsOrUrns($offres, $typeIds, $urns);
        }

        if ($parse) {
            array_map(function ($offre) {
                $this->offreParser->launchParse($offre);
            }, $offres);
        }

        return $offres;
    }

    /**
     * Retourne la liste des events
     * @return Offre[]
     */
    public function fetchEvents(bool $removeObsolete = false, $urnSelected = null): array
    {
        $filtres = [];
        if ($urnSelected) {
            $typeOffre = $this->typeOffreRepository->findOneByUrn($urnSelected);
            if ($typeOffre) {
                $filtres = [$typeOffre];
            }
        } else {
            $parent = $this->typeOffreRepository->findOneByUrn(UrnList::EVENTS->value);
            $filtres = $this->typeOffreRepository->findByParent($parent->id);
        }

        if (count($filtres) === 0) {
            return [];
        }

        $events = $this->fetchOffres($filtres);

        foreach ($events as $key => $event) {
            if (!$event->dateBegin) {
                unset($events[$key]);
            }
        }
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
     * @param string $class
     * @param string|null $cacheKeyPlus
     * @return ResultOfferDetail|Offre|null
     * @throws InvalidArgumentException
     */
    public function fetchOffreByCgt(
        string $codeCgt,
        string $class = ResultOfferDetail::class,
        ?string $cacheKeyPlus = null
    ): ResultOfferDetail|Offre|null {

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
                    if ($object) {
                        $object->dataRaw = $dataString;
                    }

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

    /**
     * @param string $codeCgt
     * @param string $class
     * @param string|null $cacheKeyPlus
     * @return Offre|null
     * @throws InvalidArgumentException
     */
    public function fetchOffreByCgtAndParse(string $codeCgt, string $class, ?string $cacheKeyPlus = null): ?Offre
    {
        $offre = $this->fetchOffreByCgt($codeCgt, $class, $cacheKeyPlus);
        if ($offre) {
            $this->offreParser->launchParse($offre);
        }

        return $offre;
    }

    /**
     * @param Offre $referringOffer
     * @return Offre[]
     * @throws InvalidArgumentException
     */
    public function fetchSameOffres(Offre $referringOffer): array
    {
        $urn = 'urn:typ:'.$referringOffer->typeOffre->idTypeOffre;

        $filtres = [$this->typeOffreRepository->findOneByUrn($urn)];

        $urns = [];
        foreach ($referringOffer->categories as $category) {
            $urns[] = $category->urn;
        }
        foreach ($this->typeOffreRepository->findByUrns($urns) as $typeOffre) {
            $filtres[] = $typeOffre;
        }
        if (count($filtres) === 0) {
            return [];
        }

        $offres = $this->fetchOffres($filtres);
        $data = [];

        foreach ($offres as $offre) {
            if ($referringOffer->codeCgt != $offre->codeCgt) {
                $data[] = $offre;
            }
        }

        return $data;
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

    public function urnDefinition(string $urnName): ?UrnDefinition
    {
        return $this->cache->get('urnDefinition-'.$urnName, function () use ($urnName) {
            if ($data = $this->pivotRemoteRepository->thesaurusUrn($urnName)) {
                return $this->pivotSerializer->deserializeToClass($data, UrnDefinition::class);
            }

            return null;
        });
    }

    /**
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/9/urn:fld:catevt;fmt=json
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/261/urn:fld:cat;fmt=json
     * @return Family[]
     * @throws \Exception
     */
    public function thesaurusChildren(int $typeOffre, string $urn): array
    {
        $familiesObject = json_decode($this->pivotRemoteRepository->thesaurus('typeofr/'.$typeOffre.'/'.$urn));
        if (!isset($familiesObject->spec[0]->spec)) {
            return [];
        }

        return $this->pivotSerializer->deserializeToClass(
            json_encode($familiesObject->spec[0]->spec),
            'AcMarche\Pivot\Entities\Family\Family[]',
        );
    }

    /**
     * Retourne le json (string) complet du query
     * @return ResponseQuery|null
     * @throws \Psr\Cache\InvalidArgumentException|\Exception
     */
    public function getAllDataFromRemote(): ?ResponseQuery
    {
        return $this->cache->get('pivotAllData', function () {
            if ($dataString = $this->pivotRemoteRepository->query()) {
                return $this->pivotSerializer->deserializeToClass($dataString, ResponseQuery::class);
            }

            return null;
        });
    }
}
