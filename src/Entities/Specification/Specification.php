<?php

namespace AcMarche\Pivot\Entities\Specification;

use AcMarche\Pivot\Entities\Urn\UrnDefinition;

class Specification
{
    public ?SpecData $data = null;
    public ?UrnDefinition $urnDefinition = null;
    public ?UrnDefinition $urnCatDefinition = null;

    public function __construct(SpecData $specData,UrnDefinition $urnDefinition, ?UrnDefinition $urnCatDefinition)
    {
        $this->data = $specData;
        $this->urnDefinition = $urnDefinition;
        $this->urnCatDefinition = $urnCatDefinition;
    }
}