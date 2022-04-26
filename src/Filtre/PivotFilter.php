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
        $types = array_column($pivotTypes, 'value');
        foreach ($data->offre as $row) {
            if (in_array($row->typeOffre->idTypeOffre, $types)) {
                $offres[] = $row;
            }
        }

        return $offres;
    }
}