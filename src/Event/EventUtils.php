<?php

namespace AcMarche\Pivot\Event;

use AcMarche\Pivot\Entities\Event\Event;
use DateTime;
use DateTimeInterface;

class EventUtils
{
    private static ?DateTimeInterface $today = null;

    public static function isEventObsolete(Event $event): bool
    {
        self::$today = new DateTime();
        $datesOk = 0;
        foreach ($event->dates as $dateBeginEnd) {
            if ($dateBeginEnd->date_end > self::$today) {
                $datesOk++;
            }
        }

        return 0 === $datesOk;
    }
}
