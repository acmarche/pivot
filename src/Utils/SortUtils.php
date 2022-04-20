<?php

namespace AcMarche\Pivot\Utils;


use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Event\Event;

class SortUtils
{
    /**
     * @param DateBeginEnd[] $dateBeginEnds
     *
     * @return DateBeginEnd[]
     */
    public static function sortDatesEvent(array $dateBeginEnds, string $order = 'ASC'): array
    {
        usort(
            $dateBeginEnds,
            function ($dateBeginEndA, $dateBeginEndB) use ($order) {
                $dateA = $dateBeginEndA->date_begin;
                $dateB = $dateBeginEndB->date_begin;

                if ($order == 'ASC') {
                    return $dateA <=> $dateB;
                } else {
                    return $dateB <=> $dateA;
                }
            }
        );

        return $dateBeginEnds;
    }

    /**
     * @param Event[] $events
     *
     * @return Event[]
     */
    public static function sortEvents(array $events, string $order = 'ASC'): array
    {
        usort(
            $events,
            function ($eventA, $eventB) use ($order) {
                $dateA = $eventA->dateBegin;
                $dateB = $eventB->dateBegin;
                if ($order == 'ASC') {
                    return $dateA <=> $dateB;
                } else {
                    return $dateB <=> $dateA;
                }

            }
        );

        return $events;
    }
}
