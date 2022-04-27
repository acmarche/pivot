<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Category;
use AcMarche\Pivot\Entities\Event\Event;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecInfo;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\SpecTypeEnum;
use AcMarche\Pivot\Spec\UrnCatList;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnUtils;

class PivotParser
{
    use SpecTrait, ParserEventTrait;

    public function __construct(private UrnUtils $urnUtils)
    {
    }

    public function parseOffre(Offre $offre)
    {
        $this->specs = $offre->spec;
        foreach ($offre->spec as $spec) {
            $offre->specsDetailed[] = new SpecInfo($this->urnUtils->getInfosUrn($spec->urn), $spec);
        }
        $offre->homepage = $this->findByUrnReturnValue(UrnList::HOMEPAGE);
        $offre->active = $this->findByUrnReturnValue(UrnList::ACTIVE);

        foreach ($this->findByType(SpecTypeEnum::EMAIL) as $spec) {
            $offre->emails[] = $spec->value;
        }
        foreach ($this->findByType(SpecTypeEnum::TEL) as $spec) {
            $offre->tels[] = $spec->value;
        }

        $offre->descriptions = $this->findByUrnCat(UrnCatList::DESCRIPTION);

        $offre->tarifs = $this->findByUrn(UrnList::TARIF);
        $offre->webs = $this->findByUrn(UrnList::WEB);
        $offre->hades_ids = $this->findByUrn(UrnList::HADES_ID);

        $offre->communications = $this->findByUrnCat(UrnCatList::COMMUNICATION);
        $offre->adresse_rue = $this->findByUrn(UrnList::ADRESSE_RUE);
        $offre->equipements = $this->findByUrnCat(UrnCatList::EQUIPEMENTS);
        $offre->accueils = $this->findByUrnCat(UrnCatList::ACCUEIL);

        $cats = $this->findByUrnCat(UrnCatList::CATEGORIE);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $order = $cat->order;
                $labels = $info->label;
                $offre->categories[] = new Category($order, $labels);
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
        $this->parseOffre($event);

        $event->dates = $this->getDates();
        $fistDate = $event->firstDate();
        if ($fistDate) {
            $event->dateBegin = $fistDate->date_begin;
            $event->dateEnd = $fistDate->date_end;
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
                $event->dateEnd = $fistDate->date_end;
            }
        }

        $cats = $this->findByUrn(UrnList::CATEGORIE_EVENT, true);
        foreach ($cats as $cat) {
            $info = $this->urnUtils->getInfosUrn($cat->urn);
            if ($info) {
                $order = $cat->order;
                $labels = $info->label;
                $event->categories[] = new Category($order, $labels);
            }
        }
    }
}
