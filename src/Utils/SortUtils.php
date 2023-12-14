<?php

namespace AcMarche\Pivot\Utils;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Offre\Offre;

class SortUtils
{
    /**
     * @param DateBeginEnd[]|array $dateBeginEnds
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
     * @param Offre[] $events
     *
     * @return Offre[]
     */
    public static function sortEvents(array $events, string $order = 'ASC'): array
    {
        usort(
            $events,
            function ($eventA, $eventB) use ($order) {
                $dateA = $eventA->dateEnd;
                $dateB = $eventB->dateEnd;
                if ($order == 'ASC') {
                    return $dateA <=> $dateB;
                } else {
                    return $dateB <=> $dateA;
                }
            }
        );

        return $events;
    }

    /**
     * @param Offre[] $offres
     *
     * @return Offre[]
     */
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
            }
        );

        return $offres;
    }

    /**
     * @param Offre[] $offres
     *
     * @return Offre[]
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
            }
        );

        return $offres;
    }
}
