<?php

namespace AcMarche\Pivot\Entities\Pivot;

class Offer
{
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
    public Urn $estActiveUrn;
    /**
     * @deprecated $visibilite
     */
    public int $visibilite;
    public Urn $visibiliteUrn;
    public TypeOffre $typeOffre;
    public Adresse $adresse1;
    /**
     * @var Spec[]
     */
    public $spec;
    /**
     * @var Spec[]
     */
    public $relOffre;
}