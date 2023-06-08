<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnList;
use Symfony\Contracts\Service\Attribute\Required;

trait ParseRelationTrait
{
    #[Required]
    public PivotRepository $pivotRepository;

    public function parsePois(Offre $offre)
    {
        foreach ($offre->relOffre as $relOffre) {
            if (!in_array(
                $relOffre->urn,
                [UrnList::POIS->value, UrnList::CONTACT_DIRECTION->value]
            )) {
                continue;
            }
            $codeCgt = $relOffre->offre['codeCgt'];
            try {
                $relatedOffer = $this->pivotRepository->fetchOffreByCgt($codeCgt);
            } catch (\Exception $exception) {
                continue;
            }
            if (!$relatedOffer instanceof Offre) {
                continue;
            }
            if ($relOffre->urn == UrnList::POIS->value) {
                $this->specitificationsByOffre($relatedOffer);
                $this->parseOffre($relatedOffer);
                $this->parseImages($relatedOffer);
                $offre->pois[] = $relatedOffer;
            }
            if ($relOffre->urn == UrnList::CONTACT_DIRECTION->value) {
                $offre->contact_direction = $relatedOffer;
            }
        }
    }
}