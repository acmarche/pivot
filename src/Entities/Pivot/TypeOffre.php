<?php

namespace AcMarche\Pivot\Entities\Pivot;

class TypeOffre
{
    use LabelTrait;

    public string $urn;
    public string $code;
    public int $order;
    public bool $deprecated;
    public string $type;
    public bool $root;
    public string $dateModification;
    public string $dateCreation;

    public function __construct()
    {
        $this->label = [];
    }

    public function addLabel(Label $label)
    {
        $this->label[] = $label;
    }
}
