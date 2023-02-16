<?php

namespace AcMarche\Pivot\Event;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Offre\Offre;
use DateTime;
use DateTimeInterface;

class EventUtils
{
    private static ?DateTimeInterface $today = null;

    /**
     * @param Offre[] $events
     *
     * @return  Offre[]
     */
    public static function removeObsolete(array $events): array
    {
        $data = [];
        foreach ($events as $event) {
            if ($eventOk = self::isEventObsolete($event)) {
                $data[] = $eventOk;
            }
        }

        return $data;
    }

    public static function isEventObsolete(Offre $event): ?Offre
    {
        self::$today = new DateTime();
        $dates = [];
        foreach ($event->dates as $dateBeginEnd) {
            if ($dateBeginEnd->date_end->format('Y-m-d') >= self::$today->format('Y-m-d')) {
                $dates[] = $dateBeginEnd;
            }
        }
        if (count($dates) === 0) {
            return null;
        }

        $event->dates = $dates;

        return $event;
    }

    public static function isDateBeginEndObsolete(DateBeginEnd $dateBeginEnd): bool
    {
        self::$today = new DateTime();

        return $dateBeginEnd->date_end < self::$today;
    }
}
