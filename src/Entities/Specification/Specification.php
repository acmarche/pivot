<?php

namespace AcMarche\Pivot\Entities\Specification;

use AcMarche\Pivot\Entity\UrnDefinitionEntity;

/**
 * Class contenant la définition de la spécification
 * et la valeur de la spécification associé à l'objet
 */
class Specification
{
    public ?SpecData $data = null;
    public ?UrnDefinitionEntity $urnDefinition = null;
    public ?UrnDefinitionEntity $urnCatDefinition = null;

    public function __construct(
        SpecData $specData,
        UrnDefinitionEntity $urnDefinition,
        ?UrnDefinitionEntity $urnCatDefinition
    ) {
        $this->data = $specData;
        $this->urnDefinition = $urnDefinition;
        $this->urnCatDefinition = $urnCatDefinition;
    }
}