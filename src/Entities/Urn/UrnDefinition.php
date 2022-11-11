<?php

namespace AcMarche\Pivot\Entities\Urn;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\LabelTrait;

class UrnDefinition
{
    use LabelTrait;

    public string $urn;
    public string $code;
    public bool $deprecated = false;
    public string $type;
    public string $dateModification;
    public string $dateCreation;
    public string $xpathPivotWebWs;
    public bool $dynamic;
    public int $visibilite;
    /**
     * @var array|Label
     */
    public array $abstract = [];
}