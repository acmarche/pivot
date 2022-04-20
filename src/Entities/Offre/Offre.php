<?php

namespace AcMarche\Pivot\Entities\Offre;


use AcMarche\Pivot\Entities\Communication\Adresse;
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
    /**
     * L'offre au format original
     * @var string $data
     */
    public string $data;
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
    public array $images = [];

    /**
     * Alias
     */
    public function getAdresse(): ?Adresse
    {
        return $this->adresse1;
    }

    //todo
    public function getNom2(string $language)
    {

    }
}