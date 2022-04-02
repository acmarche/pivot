<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Pivot\Offer;
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
     * @param array|Offer[] $offres
     * @return void
     */
    public function parseEvents(array $events)
    {
        array_map(function ($event) {
            $this->parseEvent($event);
        }, $events);
    }

    public function parseEvent(Offer $offre)
    {
        $eventSpec = new SpecEvent($offre->spec);
        $dates = $eventSpec->dateBeginAndEnd();
        $offre->dates = $dates;
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
                $sOffre = $this->pivotRepository->offreByCgt($code, $item['dateModification']);
                $itemSpec = new SpecEvent($sOffre->getOffre()->spec);
                if ($image = $itemSpec->getByUrn(UrnEnum::URL)) {
                    $offre->image = $image->value;
                }
//            dump($sOffre->getOffre()->nom);
            }
        }
    }

}