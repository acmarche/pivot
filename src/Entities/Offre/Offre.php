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
     * @var string $dataRaw
     */
    public string $dataRaw;
    public ?bool $active;
    public string $dateCreation;
    public string $dateModification;
    public User $userCreation;
    public UserGlobalCreation $userGlobalCreation;
    public UserGlobalModification $userModification;
    public UserGlobalModification $userGlobalModification;
    public string $nom;
    public string $email;
    public string $tel;
    public string|SpecData|null $tarif;

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
    public array $spec = [];
    /**
     * @var RelOffre[] $relOffre
     */
    public array $relOffre = [];
    public array $images = [];
    public array $voir_aussis = [];
    public array $hades_ids = [];
    /**
     * @var SpecData[]
     */
    public array $adresse_rue;

    public string|SpecData|null $description;

    public ?string $image = null;

    /**
     * utilise pour wp
     */
    public ?string $url = null;
    public array $tags = [];
    public array $enfants = [];
    public array $parents = [];

    /**
     * Alias
     */
    public function getAdresse(): ?Adresse
    {
        return $this->adresse1;
    }

    public function nomByLanguage(string $language = 'fr'): ?string
    {
        return $this->nom;
    }

    public function firstImage(): ?string
    {
        if (count($this->images) > 0) {
            return $this->images[0];
        }

        return null;
    }
}