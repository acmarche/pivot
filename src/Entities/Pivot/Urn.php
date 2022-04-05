<?php

namespace AcMarche\Pivot\Entities\Pivot;

class Urn
{
    use LabelTrait;

    public string $urn;
    public bool $deprecated;
    public int $userGlobalId;
    public string $userGlobalName;
    public string $userLocalLogin;
    public string $userLocalName;
    public string $userLocalSurname;
    public string $type;
    public array $label;
    public bool $dynamic;
    public int $visibilite;
    public bool $collapsed;
    public string $dateModification;
    public string $dateCreation;
}
