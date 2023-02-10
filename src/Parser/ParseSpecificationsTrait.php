<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Entities\Specification\Specification;
use AcMarche\Pivot\Entity\UrnDefinitionEntity;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use AcMarche\Pivot\Utils\UrnToSkip;

trait ParseSpecificationsTrait
{
    /**
     * @required
     */
    public UrnDefinitionRepository $urnDefinitionRepository;

    /**
     * @param Offre $offre
     * @return array|Specification[]
     * @throws \Exception
     */
    public function specitificationsByOffre(Offre $offre): array
    {
        $specifications = [];
        foreach ($offre->spec as $spec) {
            if (in_array($spec->value, UrnToSkip::urns)) {
                continue;
            }
            $urnDefinition = $this->urnDefinitionRepository->findByUrn($spec->urn);
            $urnCatDefinition = null;
            if ($spec->urnCat) {
                $urnCatDefinition = $this->urnDefinitionRepository->findByUrn($spec->urnCat);
            }
            if ($spec instanceof SpecData && $urnDefinition instanceof UrnDefinitionEntity) {
                $specifications[] = new Specification($spec, $urnDefinition, $urnCatDefinition);
            } else {
                $this->logger->error("Error parse specifications offre ".$offre->codeCgt);
            }
        }

        $offre->specifications = $specifications;

        return $specifications;
    }
}