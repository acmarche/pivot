<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Pivot\Event;
use AcMarche\Pivot\Entities\Pivot\Hotel;
use AcMarche\Pivot\Entities\Pivot\SpecInfo;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecEvent;
use AcMarche\Pivot\Spec\SpecTypeConst;
use AcMarche\Pivot\Spec\UrnConst;
use AcMarche\Pivot\Spec\UrnUtils;

class PivotParser
{
    public function __construct(private PivotRepository $pivotRepository, private UrnUtils $urnUtils)
    {
    }

    /**
     * Complète la class Event
     * Date de début, date de fin,...
     * @param array $events
     */
    public function parseEvents(array $events): void
    {
        array_map(function ($event) {
            $this->parseEvent($event);
        }, $events);
    }

    /**
     * @param Event $event
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function parseEvent(Event $event)
    {
        foreach ($event->spec as $spec) {
            $event->specsDetailed[] = new SpecInfo($this->urnUtils->getInfosUrn($spec->urn), $spec);
        }

        $eventSpec = new SpecEvent($event->spec);
        $dates = $eventSpec->dateBeginAndEnd();
        $event->dateBegin = $dates[0];
        $event->dateEnd = $dates[1];
        $event->homepage = $eventSpec->getHomePage();
        $event->active = $eventSpec->isActive();
        foreach ($eventSpec->getByType(SpecTypeConst::EMAIL) as $spec) {
            $event->emails[] = $spec->value;
        }
        foreach ($eventSpec->getByType(SpecTypeConst::TEL) as $spec) {
            $event->tels[] = $spec->value;
        }
        $event->description = $eventSpec->getByUrn(UrnConst::DESCRIPTION, true);
        //  $this->io->writeln($eventSpec->getByUrn(UrnEnum::NOMO, true));
        $event->tarif = $eventSpec->getByUrn(UrnConst::TARIF, true);
        $cats = $eventSpec->getByUrnCat(UrnConst::CATEGORIE);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $event->categories[] = $info->labelByLanguage('fr');
            }
        }
        $this->parseRelOffre($event);
    }

    public function parseRelOffre(Event $event)
    {
        if (is_array($event->relOffre)) {
            foreach ($event->relOffre as $relation) {
                //dump($relation);
                $item = $relation->offre;
                $code = $item['codeCgt'];
                $idType = $item['typeOffre']['idTypeOffre'];
                $sOffre = $this->pivotRepository->offreByCgt($code, $item['dateModification']);
                if ($sOffre) {
                    $itemSpec = new SpecEvent($sOffre->getOffre()->spec);
                    if ($image = $itemSpec->getByUrn(UrnConst::URL)) {
                        $event->image = $image->value;
                    }
                }
            }
        }
    }

    /**
     * @param Hotel[] $hotels
     * @return void
     */
    public function parseHotels(array $hotels)
    {
        array_map(function ($hotel) {
            $this->parseHotel($hotel);
        }, $hotels);
    }

    /**
     * @param Event $hotel
     * @return void
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function parseHotel(Hotel $hotel)
    {
        foreach ($hotel->spec as $spec) {
            $hotel->specsDetailed[] = new SpecInfo($this->urnUtils->getInfosUrn($spec->urn), $spec);
        }

        $eventSpec = new SpecEvent($hotel->spec);
        $dates = $eventSpec->dateBeginAndEnd();
      //  $hotel->dateBegin = $dates[0];
      //  $hotel->dateEnd = $dates[1];
        $hotel->homepage = $eventSpec->getHomePage();
        $hotel->active = $eventSpec->isActive();
        foreach ($eventSpec->getByType(SpecTypeConst::EMAIL) as $spec) {
            $hotel->emails[] = $spec->value;
        }
        foreach ($eventSpec->getByType(SpecTypeConst::TEL) as $spec) {
            $hotel->tels[] = $spec->value;
        }
        $hotel->description = $eventSpec->getByUrn(UrnConst::DESCRIPTION, true);
        //  $this->io->writeln($eventSpec->getByUrn(UrnEnum::NOMO, true));
        $hotel->tarif = $eventSpec->getByUrn(UrnConst::TARIF, true);
        $cats = $eventSpec->getByUrnCat(UrnConst::CATEGORIE);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $hotel->categories[] = $info->labelByLanguage('fr');
            }
        }
    }

}