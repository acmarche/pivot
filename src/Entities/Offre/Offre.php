<?php

namespace AcMarche\Pivot\Entities\Offre;


use AcMarche\Pivot\Entities\Adresse;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Entities\Specification\SpectFieldsTrait;
use AcMarche\Pivot\Entities\UrnLabel;
use AcMarche\Pivot\Entities\User\User;
use AcMarche\Pivot\Entities\User\UserGlobalCreation;
use AcMarche\Pivot\Entities\User\UserGlobalModification;

class Offre
{
    use SpectFieldsTrait;

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