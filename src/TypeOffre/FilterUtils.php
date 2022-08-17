<?php

namespace AcMarche\Pivot\TypeOffre;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Offre\OffreShort;

class FilterUtils
{
    /**
     * @param Offre[]|OffreShort[] $data
     * @param string[] $typeIds
     * @param string[] $urns
     * @return array
     */
    public static function filterByTypeIdsOrUrns(array $data, array $typeIds, array $urns): array
    {
        $offres = [];
        foreach ($data as $offre) {
            foreach ($typeIds as $typeId) {
                if (str_contains($offre->dataRaw, '"idTypeOffre": '.$typeId)) {
                    $offres[] = $offre;
                }
            }
            foreach ($urns as $urn) {
                if (str_contains($offre->dataRaw, $urn)) {
                    $offres[] = $offre;
                }
            }
        }

        return $offres;
    }
}
