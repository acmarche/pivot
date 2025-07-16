<?php

namespace AcMarche\Pivot\Entities\Offre;

class OffreShort
{
    public string $codeCgt;
    public string $dateCreation;
    public string $dateModification;
    public ?TypeOffreShort $typeOffre = null;
    public $spec;
    public $relOffre;
    public $relOffreTgt;
}
