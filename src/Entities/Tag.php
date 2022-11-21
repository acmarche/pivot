<?php

namespace AcMarche\Pivot\Entities;

class Tag
{
    use LabelTrait;

    public string $urn;
    public ?string $url = null;
    public ?string $name = null;

    public function __construct(string $urn, array $labels)
    {
        $this->urn = $urn;
        $this->label = $labels;
    }
}