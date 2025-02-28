<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Event\DateEvent;
use AcMarche\Pivot\Entities\Offre\Offre;

class SortUtils
{
    /**
     * @param DateEvent[]|array $dates
     *
     * @return DateEvent[]
     */
    public static function sortDatesEvent(array $dates, string $order = 'ASC'): array
    {
        usort(
            $dates,
            fn(DateEvent $a, DateEvent $b) => ($order === 'ASC' ? 1 : -1) * $a->dateRealBegin->getTimestamp(
                ) <=> $b->dateRealBegin->getTimestamp(),
        );

        return $dates;
    }

    /**
     * @param Offre[] $events
     *
     * @return Offre[]
     */
    public static function sortEvents(array $events, string $order = 'ASC'): array
    {
        usort($events, fn(Offre $a, Offre $b) => ($order === 'ASC' ? 1 : -1) *
            $a->firstRealDate()->getTimestamp() <=> $b->firstRealDate()->getTimestamp());

        return $events;
    }

    public static function sortOffres(array $offres, string $language = 'fr', string $order = 'ASC'): array
    {
        usort(
            $offres,
            function ($offreA, $offreB) use ($language, $order) {
                if ($order == 'ASC') {
                    return $offreA->labelByLanguage($language) <=> $offreB->labelByLanguage($language);
                } else {
                    return $offreB->labelByLanguage($language) <=> $offreA->labelByLanguage($language);
                }
            },
        );

        return $offres;
    }

    /**
     * @param \stdClass[] $offres
     *
     * @return \stdClass[]
     */
    public static function sortOffresByName(array $offres, string $order = 'ASC'): array
    {
        usort(
            $offres,
            function ($offreA, $offreB) use ($order) {
                if ($order == 'ASC') {
                    return $offreA->name <=> $offreB->name;
                } else {
                    return $offreB->name <=> $offreA->name;
                }
            },
        );

        return $offres;
    }
}
