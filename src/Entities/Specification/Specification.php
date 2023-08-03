<?php

namespace AcMarche\Pivot\Entities\Specification;

use AcMarche\Pivot\Entity\UrnDefinitionEntity;

/**
 * Class contenant la définition de la spécification
 * et la valeur de la spécification associé à l'objet
 */
class Specification
{
    public function __construct(public ?SpecData $data, public ?UrnDefinitionEntity $urnDefinition, public ?UrnDefinitionEntity $urnCatDefinition)
    {
    }
}