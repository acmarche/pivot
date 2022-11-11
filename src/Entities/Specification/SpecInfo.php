<?php

namespace AcMarche\Pivot\Entities\Specification;


use AcMarche\Pivot\Entities\Urn\UrnDefinition;

/**
 * Class contenant la définition de la spécification
 * et la valeur de la spécification associé à l'objet
 */
class SpecInfo
{
    public ?UrnDefinition $urnDefinition;
    public ?SpecData $specData;

    public function __construct($specDefinition, $spacData)
    {
        $this->urnDefinition = $specDefinition;
        $this->specData = $spacData;
    }
}