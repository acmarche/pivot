<?php

namespace AcMarche\Pivot\Event;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Event\Event;
use DateTime;
use DateTimeInterface;

class EventUtils
{
    private static ?DateTimeInterface $today = null;

    /**
     * @param Event[] $events
     *
     * @return  Event[]
     */
    public static function removeObsolete(array $events): array
    {
        $data = [];
        foreach ($events as $event) {
            if ( ! self::isEventObsolete($event)) {
                $data[] = $event;
            }
        }

        return $data;
    }

    public static function isEventObsolete(Event $event): bool
    {
        self::$today = new DateTime();
        $datesOk     = 0;
        foreach ($event->dates as $dateBeginEnd) {
            if ($dateBeginEnd->date_end > self::$today) {
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
