<?php

namespace AcMarche\Pivot\Entities;

class Category
{
    use LabelTrait;

    public int $id;//order
    public string $urn;
    /**
     * use for wp
     */
    public string $nom;

    public function __construct(string $urn, int $id, array $label)
    {
        $this->urn = $urn;
        $this->id = $id;
        $this->label = $label;
    }
}