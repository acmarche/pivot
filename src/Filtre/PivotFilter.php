<?php

namespace AcMarche\Pivot\Filtre;

use AcMarche\Pivot\Entities\Response\ResponseQuery;
use AcMarche\Pivot\PivotTypeEnum;

class PivotFilter
{
    /**
     * @param ResponseQuery $data
     * @param PivotTypeEnum[] $pivotTypes
     * @return array
     */
    public static function filterByTypes(ResponseQuery $data, array $pivotTypes): array
    {
        $offres = [];

        foreach ($data->offre as $row) {
            if (in_array($row->typeOffre->idTypeOffre, $pivotTypes)) {
                $offres[] = $row;
            }
        }

        return $offres;
    }
}