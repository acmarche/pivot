<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\PivotTypeEnum;

class PivotFilter
{
    /**
     * @param ResponseQuery $data
     * @param PivotTypeEnum $pivotType
     * @return array
     */
    public static function filterByType(ResponseQuery $data, PivotTypeEnum $pivotType): array
    {
        $offres = [];
        foreach ($data->offre as $row) {
            if ($row->typeOffre->idTypeOffre == $pivotType->value) {
                $offres[] = $row;
            }
        }

        return $offres;
    }
}