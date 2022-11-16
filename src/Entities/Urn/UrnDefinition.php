<?php

namespace AcMarche\Pivot\Entities\Urn;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\LabelTrait;

class UrnDefinition
{
    use LabelTrait;

    public ?string $urn = null;
    public ?string $code = null;
    public bool $deprecated = false;
    public ?string $type = null;
    public ?string $dateModification = null;
    public ?string $dateCreation = null;
    public ?string $xpathPivotWebWs = null;
    public ?bool $dynamic = null;
    public ?int $visibilite = null;
    public ?int $userGlobalId = null;
    public ?string $userGlobalName = null;
    public ?string $userLocalLogin = null;
    public ?string $userLocalName = null;
    public ?string $userLocalSurname = null;
    public bool|null $collapsed = null;
    /**
     * @var array|Label
     */
    public array $abstract = [];
}