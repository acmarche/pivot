<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Category;
use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Hebergement\Hotel;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecEvent;
use AcMarche\Pivot\Entities\Specification\SpecInfo;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Spec\SpecTypeEnum;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnUtils;

class PivotParser
{
    public function __construct(private UrnUtils $urnUtils)
    {
    }

    public function parse(Offre|Event|Hotel $offre)
    {
        $eventSpec       = new SpecEvent($offre->spec);
        $offre->homepage = $eventSpec->getHomePage();
        $offre->active   = $eventSpec->isActive();
        foreach ($eventSpec->findByType(SpecTypeEnum::EMAIL) as $spec) {
            $offre->emails[] = $spec->value;
        }
        foreach ($eventSpec->findByType(SpecTypeEnum::TEL) as $spec) {
            $offre->tels[] = $spec->value;
        }

        $offre->descriptions = $eventSpec->findByUrn(UrnList::DESCRIPTION_SHORT, true);
        $offre->tarifs       = $eventSpec->findByUrn(UrnList::TARIF);
        $offre->webs         = $eventSpec->findByUrn(UrnList::WEB);
        $offre->hades_ids     = $eventSpec->findByUrn(UrnList::HADES_ID);

        $offre->communications = $eventSpec->findByUrn(UrnList::COMMUNICATION);

        $cats = $eventSpec->findByUrnCat(UrnList::CATEGORIE);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $order  = $cat->order;
                $labels = $info->label;
                //   $offre->categories[] = new Category($order, $labels);
            }
        }
    }

    /**
     * Complète la class Event
     * Date de début, date de fin,...
     *
     * @param array $events
     */
    public function parseEvents(array $events, bool $removeObsolete = false): void
    {
        array_map(function ($event) use ($removeObsolete) {
            $this->parseEvent($event, $removeObsolete);
        }, $events);
    }

    public function parseEvent(Event $event, bool $removeObsolete = false): void
    {
        foreach ($event->spec as $spec) {
            $event->specsDetailed[] = new SpecInfo($this->urnUtils->getInfosUrn($spec->urn), $spec);
        }
        $this->parse($event);
        $eventSpec     = new SpecEvent($event->spec);
        $datesValidite = $eventSpec->dateBeginAndEnd();

        $event->dates = $eventSpec->getDates();
        $fistDate     = $event->firstDate();
        if ($fistDate) {
            $event->dateBegin = $fistDate->date_begin;
            $event->dateEnd   = $fistDate->date_end;
        }

        if ($removeObsolete) {
            foreach ($event->dates as $key => $dateBeginEnd) {
                if (EventUtils::isDateBeginEndObsolete($dateBeginEnd)) {
                    unset($event->dates[$key]);
                }
            }
            $fistDate = $event->firstDate();
            if ($fistDate) {
                $event->dateBegin = $fistDate->date_begin;
                $event->dateEnd   = $fistDate->date_end;
            }
        }

        $cats = $eventSpec->findByUrn(UrnList::CATEGORIE_EVENT, true);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $order               = $cat->order;
                $labels              = $info->label;
                $event->categories[] = new Category($order, $labels);
            }
        }
    }

    /**
     * @param Hotel[] $hotels
     */
    public function parseHotels(array $hotels): void
    {
        array_map(function ($hotel) {
            $this->parseHotel($hotel);
        }, $hotels);
    }

    /**
     * @param Hotel $hotel
     */
    public function parseHotel(Hotel $hotel): void
    {
        foreach ($hotel->spec as $spec) {
            $hotel->specsDetailed[] = new SpecInfo($this->urnUtils->getInfosUrn($spec->urn), $spec);
        }
        $this->parse($hotel);
    }
}
