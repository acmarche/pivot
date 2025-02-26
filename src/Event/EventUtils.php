<?php

namespace AcMarche\Pivot\Event;

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
        foreach ($event->datesEvent as $date) {
            if ($date->format('Y-m-d') >= self::$today->format('Y-m-d')) {
                $dates[] = $date;
            }
        }
        if ($dates === []) {
            return null;
        }

        $event->datesEvent = $dates;

        return $event;
    }

}
