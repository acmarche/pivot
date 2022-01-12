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
            function ($descriptionA, $descriptionB) {
                {
                    if ($descriptionA->tri === $descriptionB->tri) {
                        return 0;
                    }

                    return ($descriptionA->tri < $descriptionB->tri) ? -1 : 1;
                }
            }
        );

        return $descriptions;
    }
}
