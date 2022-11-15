<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\Specification;
use AcMarche\Pivot\Entities\Specification\SpecInfo;
use AcMarche\Pivot\Repository\PivotRepository;

trait ParseSpecificationsTrait
{
    /**
     * @required
     */
    public PivotRepository $pivotRepository;

    /**
     * @param Offre $offre
     * @return array|Specification[]
     * @throws \Exception
     */
    public function specitificationsByOffre(Offre $offre): array
    {
        /**
         * @var array|SpecInfo[] $specifications
         */
        $specifications = [];
        foreach ($offre->spec as $spec) {
            $urnDefinition = $this->pivotRepository->thesaurusUrn($spec->urn);
            $urnCatDefinition = null;
            if ($spec->urnCat) {
                $urnCatDefinition = $this->pivotRepository->thesaurusUrn($spec->urnCat);
            }
            $specifications[] = new Specification($spec, $urnDefinition, $urnCatDefinition);
        }

        $offre->specifications = $specifications;

        return $specifications;
    }

}