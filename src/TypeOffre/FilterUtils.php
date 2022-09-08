<?php

namespace AcMarche\Pivot\TypeOffre;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Offre\OffreShort;
use AcMarche\Pivot\Entity\TypeOffre;

class FilterUtils
{
    /**
     * @param Offre[]|OffreShort[] $offresToFilter
     * @param string[] $typeIds
     * @param string[] $urns
     *
     * @return array
     */
    public static function filterByTypeIdsOrUrns(array $offresToFilter, array $typeIds, array $urns): array
    {
        $offres = [];
        foreach ($offresToFilter as $offre) {
            foreach ($typeIds as $typeId) {
                if (preg_match('#"idTypeOffre":'.$typeId.',#', $offre->dataRaw)) {
                    $offres[$offre->codeCgt] = $offre;
                    break;
                }
            }
            foreach ($urns as $urn) {
                if (str_contains($offre->dataRaw, $urn)) {
                    $offres[$offre->codeCgt] = $offre;
                    break;
                }
            }
        }

        return array_values($offres);//reset keys for js
    }

    /**
     * @param OffreShort[] $data
     * @param string[] $typeIds
     *
     * @return array
     */
    public static function filterByTypeIds(array $data, array $typeIds): array
    {
        $offres = [];
        foreach ($data as $row) {
            if (in_array($row->typeOffre->idTypeOffre, $typeIds)) {
                $offres[] = $row;
            }
        }

        return $offres;
    }

    /**
     * @param array|TypeOffre[] $typesOffre
     * @return array|int[]
     */
    public static function extractIds(array $typesOffre): array
    {
        $ids = [];
        foreach ($typesOffre as $typeOffre) {
            if ($typeOffre->typeId > 0) {
                $ids[] = $typeOffre->typeId;
            }
        }

        return $ids;
    }
}
