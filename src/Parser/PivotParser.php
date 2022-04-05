<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Pivot\Event;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecEnum;
use AcMarche\Pivot\Spec\SpecEvent;
use AcMarche\Pivot\Spec\UrnEnum;
use AcMarche\Pivot\Spec\UrnUtils;

class PivotParser
{
    public function __construct(private PivotRepository $pivotRepository, private UrnUtils $urnUtils)
    {
    }

    /**
     * @param array $events
     */
    public function parseEvents(array $events): void
    {
        array_map(function ($event) {
            $this->parseEvent($event);
        }, $events);
    }

    public function parseEvent(Event $event)
    {
        foreach ($event->spec as $spec) {
            $event->urns[] = $this->urnUtils->getInfosUrn($spec->urn);
        }

        $eventSpec = new SpecEvent($event->spec);
        $dates = $eventSpec->dateBeginAndEnd();
        $event->dateBegin = $dates[0];
        $event->dateEnd = $dates[1];
        $event->homepage = $eventSpec->getHomePage();
        $event->active = $eventSpec->isActive();
        foreach ($eventSpec->getByType(SpecEnum::EMAIL) as $spec) {
            $event->emails[] = $spec->value;
        }
        foreach ($eventSpec->getByType(SpecEnum::TEL) as $spec) {
            $event->tels[] = $spec->value;
        }
        $event->description = $eventSpec->getByUrn(UrnEnum::DESCRIPTION, true);
        //  $this->io->writeln($eventSpec->getByUrn(UrnEnum::NOMO, true));
        $event->tarif = $eventSpec->getByUrn(UrnEnum::TARIF, true);
        if (is_array($event->relOffre)) {
            foreach ($event->relOffre as $relation) {
                //dump($relation);
                $item = $relation->offre;
                $code = $item['codeCgt'];
                $idType = $item['typeOffre']['idTypeOffre'];
                $sOffre = $this->pivotRepository->offreByCgt($code, $item['dateModification']);
                if ($sOffre) {
                    $itemSpec = new SpecEvent($sOffre->getOffre()->spec);
                    if ($image = $itemSpec->getByUrn(UrnEnum::URL)) {
                        $event->image = $image->value;
                    }
                }
            }
        }
    }

}