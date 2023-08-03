<?php

namespace AcMarche\Pivot\Entities\Family;

//!!Laisser

use AcMarche\Pivot\Entities\LabelTrait;

class Family
{
    use LabelTrait;

    public string $urn;
    public int $order = 0;
    public bool $deprecated;
    public string $type;
    public ?string $value = null;
    public bool $dynamic;
    public int $visibilite;
    /**
     * @var Family[] $spec
     */
    public array $spec;
    public ?string $dateCreation = null;
    public ?string $dateModification = null;
}
