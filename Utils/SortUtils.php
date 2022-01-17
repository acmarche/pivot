<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Description;

class SortUtils
{
    /**
     * @param Description[] $descriptions
     *
     * @return Description[]
     */
    public static function sortDescriptions(array $descriptions): array
    {
        usort(
            $descriptions,
            fn ($descriptionA, $descriptionB) => $descriptionA->tri <=> $descriptionB->tri
        );

        return $descriptions;
    }
}
