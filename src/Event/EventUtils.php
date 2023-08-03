<?php

namespace AcMarche\Pivot\Event;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Utils\SortUtils;
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
            if (($eventOk = self::isEventObsolete($event)) instanceof Offre) {
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
        if ($dates === []) {
            return null;
        }

        $event->dates = $dates;
        self::setDateBeginAndDateEnd($event);
        self::sortDatesEvent($event);

        return $event;
    }

    private static function setDateBeginAndDateEnd(Offre $offre): void
    {
        $firstDate = $offre->firstDate();
        if ($firstDate instanceof DateBeginEnd) {
            $offre->dateBegin = $firstDate->date_begin;
            $offre->dateEnd = $firstDate->date_end;
        }
    }

    private static function sortDatesEvent(Offre $offre)
    {
        $offre->dates = SortUtils::sortDatesEvent($offre->dates);
    }
}
