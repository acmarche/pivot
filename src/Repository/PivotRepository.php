<?php

namespace AcMarche\Pivot\Repository;

use AcMarche\Pivot\Api\QueryDetailEnum;
use AcMarche\Pivot\Entities\Family\Family;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entities\Urn\UrnDefinition;
use AcMarche\Pivot\Entity\TypeOffre;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Parser\OffreParser;
use AcMarche\Pivot\Serializer\PivotSerializer;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\TypeOffre\FilterUtils;
use AcMarche\Pivot\Utils\CacheUtils;
use AcMarche\Pivot\Utils\SortUtils;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class PivotRepository
{
    public function __construct(
        private readonly PivotRemoteRepository $pivotRemoteRepository,
        private readonly TypeOffreRepository $typeOffreRepository,
        private readonly OffreParser $offreParser,
        private readonly PivotSerializer $pivotSerializer,
        private readonly CacheInterface $cache,
        private readonly CacheUtils $cacheUtils
    ) {
    }

    /**
     * @param TypeOffre[] $typesOffre
     * @return Offre[]
     * @throws InvalidArgumentException
     */
    public function fetchOffres(array $typesOffre, bool $parse = true, int $max = 500, bool $dd = false): array
    {
        if ($typesOffre === []) {
            return [];
        }
        $cacheKeyPlus = '';
        foreach ($typesOffre as $typeOffre) {
            $cacheKeyPlus .= $typeOffre->id.'-';
        }

        $cacheKey = $this->cacheUtils->generateKey(CacheUtils::FETCH_OFFRES.'-'.$cacheKeyPlus.$parse);

        //pour un pretri
        $families = $this->typeOffreRepository->findFamiliesByUrns($typesOffre);

        return $this->cache->get($cacheKey, function () use ($typesOffre, $parse, $max, $families, $dd) {
            $responseQuery = $this->getAllDataFromRemote();
            $offres = [];
            $i = 0;
            foreach ($responseQuery->offre as $offreShort) {
                if (!in_array($offreShort->typeOffre->idTypeOffre, $families)) {
                    continue;
                }
                try {
                    $offre = $this->fetchOffreByCgt($offreShort->codeCgt, $offreShort->dateModification);
                    if ($offre instanceof Offre) {
                        $offres[] = $offre;
                        $i++;
                        if ($i > $max) {
                            break;
                        }
                    }
                } catch (Exception $exception) {
                    //todo add logger
                    continue;
                }
            }

            $typeIds = FilterUtils::extractTypesId($typesOffre);
            $urns = array_column($typesOffre, 'urn');
            $offres = FilterUtils::filterByTypeIdsOrUrns($offres, $typeIds, $urns);

            if ($parse) {
                array_map(function ($offre) {
                    $this->offreParser->launchParse($offre);
                }, $offres);
            }

            return SortUtils::sortOffres($offres);
        });
    }

    /**
     * Retourne la liste des events
     * @param TypeOffre[] $typeOffres
     * @return Offre[]
     * @throws InvalidArgumentException
     */
    public function fetchEvents(array $typeOffres = []): array
    {
        if ($typeOffres === []) {
            $typeOffres = $this->typeOffreRepository->findByUrn(UrnList::EVENTS->value);
        }

        $data = $this->fetchOffres($typeOffres);
        $events = EventUtils::removeObsolete($data);

        return SortUtils::sortEvents($events);
    }

    /***
     * Retourne une offre
     * Si une classe est donnée au paramètre $class,
     * une instance de cette classe est retournée
     *
     * @throws InvalidArgumentException
     */
    public function fetchOffreByCgt(
        string $codeCgt,
        string $updatedAt = '',
    ): Offre|null {
        if (is_numeric(substr($codeCgt, 0, 1))) {
            return null;
        }

        $cacheKey = $codeCgt.$updatedAt;
        $key = $this->cacheUtils->generateKey($cacheKey);

        return $this->cache->get(
            'offre-'.$key,
            function () use ($codeCgt) {
                try {
                    $dataString = $this->pivotRemoteRepository->offreByCgt($codeCgt);
                } catch (Exception $exception) {
                    return null;
                }
                $tmp = json_decode($dataString, null, 512, JSON_THROW_ON_ERROR);
                $dataStringOffre = json_encode($tmp->offre[0], JSON_THROW_ON_ERROR);

                if ($object = $this->pivotSerializer->deserializeToClass($dataStringOffre, Offre::class)) {
                    $object->dataRaw = $dataString;
                }

                return $object;
            }
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function fetchOffreByCgtAndParse(string $codeCgt): ?Offre
    {
        $offre = $this->fetchOffreByCgt($codeCgt);
        if ($offre instanceof Offre) {
            $this->offreParser->launchParse($offre);
        }

        return $offre;
    }

    /**
     * @return Offre[]
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function fetchSameOffres(Offre $referringOffer, int $max = 20): array
    {
        $urn = 'urn:typ:'.$referringOffer->typeOffre->idTypeOffre;

        $filtres = [$this->typeOffreRepository->findOneByUrn($urn)];

        $urns = [];
        foreach ($referringOffer->tags as $category) {
            $urns[] = $category->urn;
        }
        foreach ($this->typeOffreRepository->findByUrns($urns) as $typeOffre) {
            $filtres[] = $typeOffre;
        }
        if ($filtres === []) {
            return [];
        }
        $offres = $this->fetchOffres($filtres, parse: false, max: $max, dd: true);
        $data = [];
        foreach ($offres as $offre) {
            if ($referringOffer->codeCgt !== $offre->codeCgt) {
                $data[] = $offre;
            }
        }
        foreach ($data as $offre) {
            $this->offreParser->parseImages($offre);
            $this->offreParser->specitificationsByOffre($offre);
            $this->offreParser->setCategories($offre);
        }

        return $data;
    }

    /**
     * @return Family[]
     * @throws Exception
     */
    public function thesaurusFamilies(): array
    {
        $familiesObject = json_decode($this->pivotRemoteRepository->thesaurusFamily(), null, 512, JSON_THROW_ON_ERROR);

        return $this->pivotSerializer->deserializeToClass(
            json_encode($familiesObject->spec, JSON_THROW_ON_ERROR),
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
     * @throws Exception
     */
    public function thesaurusChildren(int $typeOffre, string $urn): array
    {
        $familiesObject = json_decode(
            $this->pivotRemoteRepository->thesaurus('typeofr/'.$typeOffre.'/'.$urn),
            null,
            512,
            JSON_THROW_ON_ERROR
        );
        if (!isset($familiesObject->spec[0]->spec)) {
            return [];
        }

        return $this->pivotSerializer->deserializeToClass(
            json_encode($familiesObject->spec[0]->spec, JSON_THROW_ON_ERROR),
            'AcMarche\Pivot\Entities\Family\Family[]',
        );
    }

    /**
     * Retourne le json (string) complet du query
     * @param bool $returnDataString
     * @param QueryDetailEnum $contentDetail
     * @return ResponseQuery|string|null
     * @throws InvalidArgumentException
     */
    public function getAllDataFromRemote(
        bool $returnDataString = false,
        QueryDetailEnum $contentDetail = QueryDetailEnum::QUERY_DETAIL_LVL_SHORT
    ): ResponseQuery|string|null {
        return $this->cache->get('pivotAllData-'.$returnDataString.'-'.$contentDetail->value, function () use (
            $contentDetail,
            $returnDataString
        ) {
            if ($dataString = $this->pivotRemoteRepository->query(null, $contentDetail)) {
                if ($returnDataString) {
                    return $dataString;
                }

                return $this->pivotSerializer->deserializeToClass($dataString, ResponseQuery::class);
            }

            return null;
        });
    }
}
