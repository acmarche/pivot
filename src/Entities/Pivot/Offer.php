<?php

namespace AcMarche\Pivot\Entities\Pivot;

use AcMarche\Pivot\Spec\SpecTrait;

class Offer
{
    use SpecTrait;

    public string $codeCgt;
    public string $dateCreation;
    public string $dateModification;
    public User $userCreation;
    public UserGlobalCreation $userGlobalCreation;
    public UserGlobalModification $userModification;
    public UserGlobalModification $userGlobalModification;
    public string $nom;
    /**
     * @deprecated $estActive
     */
    public int $estActive;
    public UrnLabel $estActiveUrn;
    /**
     * @deprecated $visibilite
     */
    public int $visibilite;
    public UrnLabel $visibiliteUrn;
    public TypeOffre $typeOffre;
    public Adresse $adresse1;
    /**
     * @var SpecData[] $spec
     */
    public $spec;
    /**
     * @var RelOffre[] $relOffre
     */
    public $relOffre;
}