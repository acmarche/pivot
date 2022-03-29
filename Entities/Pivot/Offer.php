<?php

namespace AcMarche\Pivot\Entities\Pivot;

class Offer
{
    public string $codeCgt;
    public string $dateCreation;
    public string $dateModification;
    public User $userCreation;
    public userGlobalCreation $userGlobalCreation;
    public userGlobalModification $userModification;
    public userGlobalModification $userGlobalModification;
    public string $nom;
    /**
     * @deprecated $estActive
     */
    public int $estActive;
    public $estActiveUrn;
    /**
     * @deprecated $visibilite
     */
    public int $visibilite;
    public $visibiliteUrn;
    public $typeOffre;
    public $adresse1;
    public $spec;
    public $relOffre;
}