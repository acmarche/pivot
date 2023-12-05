<?php

namespace AcMarche\Pivot\Parser;

use Exception;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Repository\PivotRepository;
use AcMarche\Pivot\Spec\UrnList;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Service\Attribute\Required;

trait ParseRelationOffresTgtTrait
{
    #[Required]
    public PivotRepository $pivotRepository;

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function parseRelOffresTgt(Offre $offre): array
    {
        $docs = ['see_also' => [], 'enfants' => []];
        foreach ($offre->relOffreTgt as $relOffreTgt) {
            if ($relOffreTgt->urn != UrnList::VOIR_AUSSI->value) {
                continue;
            }

            $item = $relOffreTgt->offre;
            $code = $item['codeCgt'];

            try {
                $offreTgt = $this->pivotRepository->fetchOffreByCgt($code,$item['dateModification']);
            } catch (Exception) {
                continue;
            }

            if (!$offreTgt instanceof Offre) {
                continue;
            }

            $this->parseImages($offreTgt);
            $this->parsePois($offreTgt);
            $docs['see_also'][] = $offreTgt;
        }

        $offre->see_also = $docs['see_also'];
        $offre->enfants = $docs['enfants'];

        return $docs;
    }

    private function parseExtraTgt(Offre $offreTgt)
    {
        //mettre in array ajouter : , UrnList::OFFRE_ENFANT->value
        foreach ($this->findByUrn($offreTgt, UrnList::OFFRE_ENFANT->value) as $enfant) {
            $docs['enfants'][] = $enfant;
        }
    }
}
