<?php

namespace AcMarche\Pivot\Entities\Pivot;

class TypeOffre
{
    public string $urn;
    public string $code;
    public int $order;
    public bool $deprecated;
    public string $type;
    /**
     * @var Label[] $label
     */
    public array $label;
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