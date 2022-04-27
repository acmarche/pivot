<?php

namespace AcMarche\Pivot\Entities;

class Category
{
    use LabelTrait;

    public int $id;
    /**
     * use for wp
     */
    public string $nom;

    public function __construct(int $id, array $label)
    {
        $this->id = $id;
        $this->label = $label;
    }
}