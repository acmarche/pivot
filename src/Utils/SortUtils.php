<?php

namespace AcMarche\Pivot\Utils;


use AcMarche\Pivot\Entities\Offre\OffreInterface;

class SortUtils
{
    /**
     */
    public static function sortDescriptions(array $descriptions): array
    {
        usort(
            $descriptions,
            fn($descriptionA, $descriptionB) => $descriptionA->tri <=> $descriptionB->tri
        );

        return $descriptions;
    }

    /**
     * @return OffreInterface[]
     */
    public static function sortEvents(array $events): array
    {
        usort(
            $events,
            function ($eventA, $eventB) {
                $horlineA = $eventA->firstHorline();
                $horlineB = $eventB->firstHorline();

                $debut1 = $horlineA->year.'-'.$horlineA->month.'-'.$horlineA->day;
                $debut2 = $horlineB->year.'-'.$horlineB->month.'-'.$horlineB->day;

                return $debut1 <=> $debut2;
            }
        );

        return $events;
    }

    public static function sortDates(array $dates): void
    {
        usort(
            $dates,
            function ($a, $b) {
                $debut1 = $a->year.'-'.$a->month.'-'.$a->day;
                $debut2 = $b->year.'-'.$b->month.'-'.$b->day;

                return $debut1 <=> $debut2;
            }
        );
        usort(
            $dates,
            function ($a, $b) {
                return $a <=> $b;
            }
        );
    }
}
