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
        if (count($typesOffre) === 0) {
            return $data->offre;
        }
        $offres = [];
        foreach ($data->offre as $row) {
            if (in_array($row->typeOffre->idTypeOffre, $typesOffre)) {
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
            if (in_array($offre->typeOffre->idTypeOffre, $typeIds)) {
                $offres[] = $offre;
                continue;
            }
            foreach ($urns as $urn) {
                if (str_contains($offre->dataRaw, $urn)) {
                    $offres[] = $offre;
                    break;
                }
            }
          /*  $offreUrns = array_column($offre->spec, 'urn');
            $offreUrnValues = array_filter(
                array_column($offre->spec, 'value'),
                fn($value) => str_contains($value, 'urn')
            );
            $offreUrns = array_merge($offreUrns, $offreUrnValues);
            if (count(array_intersect($urns, $offreUrns)) > 0
            ) {
                $offres[] = $offre;
            }*/
        }

        return $offres;
    }
}
