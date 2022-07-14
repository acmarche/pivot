<?php

namespace AcMarche\Pivot\TypeOffre;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entity\TypeOffre;

class PivotType
{
    /**
     * @param ResponseQuery $data
     * @param int[] $typesOffre
     * @return array
     */
    public static function filterByTypes(ResponseQuery $data, array $typesOffre): array
    {
        $offres = [];
        $count = count($typesOffre);
        foreach ($data->offre as $row) {
            if ($count > 0) {
                if (in_array($row->typeOffre->idTypeOffre, $typesOffre)) {
                    $offres[] = $row;
                }
            } else {
                $offres[] = $row;
            }
        }

        return $offres;
    }

    /**
     * @param Offre[] $data
     * @param TypeOffre[] $pivotTypes
     * @return array
     */
    public static function filterByTypeIdsOrUrns(array $data, array $typesOffre): array
    {
        $offres = [];
        if (count($typesOffre) < 1) {
            return $data;
        }
        $typeIds = array_column($typesOffre, 'typeId');
        $urns = array_column($typesOffre, 'urn');
        foreach ($data as $offre) {
            $specs = array_column($offre->spec, 'urn');
            if (in_array($offre->typeOffre->idTypeOffre, $typeIds) || count(array_intersect($urns, $specs)) > 0
            ) {
                $offres[] = $offre;
            }
        }

        return $offres;
    }
}