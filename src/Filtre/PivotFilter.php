<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Entities\Pivot\OffreShort;
use AcMarche\Pivot\Entities\Pivot\Response\ResponseQuery;

class PivotFilter
{
    /**
     * @param ResponseQuery $data
     * @param int $type
     * @return array|OffreShort[]
     */
    public static function filterByType(ResponseQuery $data, int $type): array
    {
        $offres = [];
        foreach ($data->offre as $row) {
            if ($row->typeOffre->idTypeOffre == $type) {
                $offres[] = $row;
            }
        }

        return $offres;
    }
}