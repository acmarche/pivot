<?php

namespace AcMarche\Pivot\Entities\Pivot;

/**
 * Class contenant la définition de la spécification
 * et la valeur de la spécification associé à l'objet
 */
class SpecInfo
{
    public ?UrnDefinition $specDefinition;
    public ?SpecData $specData;

    public function __construct($specDefinition, $spacData)
    {
        $this->specDefinition = $specDefinition;
        $this->specData = $spacData;
    }
}