<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Pivot\Event;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\SpecEnum;
use AcMarche\Pivot\Spec\SpecEvent;
use AcMarche\Pivot\Spec\UrnEnum;

class PivotParser
{
    public function __construct(private PivotRepository $pivotRepository)
    {
    }

    /**
     * @param array $events
     * @return Event[]
     */
    public function parseEvents(array $events)
    {
        array_map(function ($event) {
            $this->parseEvent($event);
        }, $events);
    }

    public function parseEvent(Event $offre)
    {
        $eventSpec = new SpecEvent($offre->spec);
        $dates = $eventSpec->dateBeginAndEnd();
        $offre->dateBegin = $dates[0];
        $offre->dateEnd = $dates[1];
        $offre->homepage = $eventSpec->getHomePage();
        $offre->active = $eventSpec->isActive();
        foreach ($eventSpec->getByType(SpecEnum::EMAIL) as $spec) {
            $offre->email = $spec->value;
        }
        foreach ($eventSpec->getByType(SpecEnum::TEL) as $spec) {
            $offre->tel = $spec->value;
        }
        $offre->description = $eventSpec->getByUrn(UrnEnum::DESCRIPTION, true);
        //  $this->io->writeln($eventSpec->getByUrn(UrnEnum::NOMO, true));
        $offre->tarif = $eventSpec->getByUrn(UrnEnum::TARIF, true);
        if (is_array($offre->relOffre)) {
            foreach ($offre->relOffre as $relation) {
                //dump($relation);
                $item = $relation->offre;
                $code = $item['codeCgt'];
                $idType = $item['typeOffre']['idTypeOffre'];
                dump($code);
                $sOffre = $this->pivotRepository->offreByCgt($code, $item['dateModification']);
                dump($sOffre);
                if ($sOffre) {
                    $itemSpec = new SpecEvent($sOffre->getOffre()->spec);
                    if ($image = $itemSpec->getByUrn(UrnEnum::URL)) {
                        $offre->image = $image->value;
                    }
                }
            }
        }
    }

}