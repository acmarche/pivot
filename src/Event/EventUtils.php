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
            if (!self::isEventObsolete($event)) {
                $data[] = $event;
            }
        }

        return $data;
    }

    public static function isEventObsolete(Offre $event): bool
    {
        self::$today = new DateTime();
        $datesOk = 0;
        foreach ($event->dates as $dateBeginEnd) {
            if ($dateBeginEnd->date_end >= self::$today) {
                $datesOk++;
            }
        }

        return 0 === $datesOk;
    }

    public static function isDateBeginEndObsolete(DateBeginEnd $dateBeginEnd): bool
    {
        self::$today = new DateTime();

        return $dateBeginEnd->date_end < self::$today;
    }
}
