<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnList;

trait ParseRelationOffresTgtTrait
{
    /**
     * @required
     */
    public PivotRepository $pivotRepository;

    /**
     * @param Offre[] $offres
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function parseRelOffresTgt(Offre $offre): void
    {
        foreach ($offre->relOffreTgt as $relOffreTgt) {
            $item = $relOffreTgt->offre;
            $code = $item['codeCgt'];
            try {
                $offreTgt = $this->pivotRepository->getOffreByCgt($code, Offre::class);
            } catch (\Exception $exception) {
                continue;
            }
            if (!$offreTgt) {
                continue;
            }

            //todo
            //$this->launchParse($offreTgt);
            if ($relOffreTgt->urn == UrnList::VOIR_AUSSI->value) {
                $offre->see_also[] = $offreTgt;
            }
            foreach ($this->findByUrn($offreTgt, UrnList::OFFRE_ENFANT->value) as $enfant) {
                $offre->enfants[] = $enfant;
            }
        }
    }

}