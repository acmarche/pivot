<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\Entity\Filtre;

class PivotFilter
{
    /**
     * @param ResponseQuery $data
     * @param int[] $filtres
     * @return array
     */
    public static function filterByTypes(ResponseQuery $data, array $filtres): array
    {
        $offres = [];
        $count = count($filtres);
        foreach ($data->offre as $row) {
            if ($count > 0) {
                if (in_array($row->typeOffre->idTypeOffre, $filtres)) {
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
     * @param Filtre[] $pivotTypes
     * @return array
     */
    public static function filterByReferencesOrUrns(array $data, array $filtres): array
    {
        $offres = [];
        if (count($filtres) < 1) {
            return $data;
        }
        $references = array_column($filtres, 'reference');
        $urns = array_column($filtres, 'urn');
        foreach ($data as $offre) {
            $specs = array_column($offre->spec, 'urn');
            if (in_array($offre->typeOffre->idTypeOffre, $references) || count(array_intersect($urns, $specs)) > 0
            ) {
                $offres[] = $offre;
            }
        }

        return $offres;
    }
}