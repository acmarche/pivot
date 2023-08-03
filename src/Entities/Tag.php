<?php

namespace AcMarche\Pivot\Entities;

class Tag
{
    use LabelTrait;
    public ?string $url = null;
    public ?string $name = null;

    public function __construct(public string $urn, array $labels)
    {
        $this->label = $labels;
    }
}