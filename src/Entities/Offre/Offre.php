<?php

namespace AcMarche\Pivot\Entities\Offre;

use AcMarche\Pivot\Entities\Communication\Adresse;
use AcMarche\Pivot\Entities\Event\EventTrait;
use AcMarche\Pivot\Entities\LabelTrait;
use AcMarche\Pivot\Entities\Specification\Document;
use AcMarche\Pivot\Entities\Specification\Gpx;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Entities\Specification\Specification;
use AcMarche\Pivot\Entities\Specification\SpectFieldsTrait;
use AcMarche\Pivot\Entities\Urn\UrnLabel;
use AcMarche\Pivot\Entities\User\User;
use AcMarche\Pivot\Entities\User\UserGlobalCreation;
use AcMarche\Pivot\Entities\User\UserGlobalModification;

class Offre
{
    use SpectFieldsTrait;
    use EventTrait;
    use LabelTrait;

    public \DateTimeInterface $datedebvalid;
    public \DateTimeInterface $datefinvalid;

    public string $codeCgt;
    public ?string $nom = null;

    /**
     * L'offre au format original
     */
    public string $dataRaw;
    public ?bool $active = null;
    public string $dateCreation;
    public string $dateModification;
    public User $userCreation;
    public UserGlobalCreation $userGlobalCreation;
    public UserGlobalModification $userModification;
    public UserGlobalModification $userGlobalModification;
    public string $email;
    public string $tel;
    public string|SpecData|null $tarif = null;

    /**
     * @deprecated $estActive
     */
    public int $estActive;
    public UrnLabel $estActiveUrn;
    public UrnLabel $visibiliteUrn;
    /**
     * @deprecated $visibilite
     */
    public int $visibilite;
    public TypeOffre $typeOffre2;
    public TypeOffreShort2 $typeOffre;
    public Adresse $adresse1;
    public ?Offre $media_default = null;
    /**
     * @var SpecData[] $spec
     */
    public array $spec = [];
    /**
     * @var RelOffre[] $relOffre
     */
    public array $relOffre = [];
    /**
     * @var RelOffreTgt[] $relOffreTgt
     */
    public array $relOffreTgt = [];
    public array $images = [];
    /**
     * @var Document[]|SpecData[] $documents
     */
    public array $documents = [];
    /**
     * @var array|Gpx[] $gpxs
     */
    public array $gpxs = [];
    /**
     * @var array|Offre[] $pois
     */
    public array $pois = [];
    /**
     * @var array|Offre[] $pois
     */
    public array $autres = [];
    /**
     * @var Offre[] $see_also
     */
    public array $see_also = [];
    public array $hades_ids = [];
    /**
     * @var SpecData[]
     */
    public array $adresse_rue;

    public string|SpecData|null $description = '';

    public Offre|null $contact_direction = null;

    public ?string $image = null;

    /**
     * utilise pour wp
     */
    public ?string $url = null;
    public array $enfants = [];
    public array $parents = [];
    public array $tagsFormatted = [];
    public string $locality = '';
    /**
     * @var Specification[]
     */
    public array $classements = [];
    public string $gpx_distance = '';
    public ?string $idcirkwi = null;
    public ?string $gpx_duree = '';
    public ?string $gpx_difficulte = '';
    /**
     * @var Specification[]|array
     */
    public array $specifications = [];
    public ?Specification $gpx_type_circuit = null;
    public ?Specification $gpx_cat_circuit = null;
    public array $locations=[];
    public ?string $cirkwi_link= null;
    public ?float $longitude= null;
    public ?float $latitude= null;
    public array $recommandations=[];

    /**
     * Alias
     */
    public function getAdresse(): ?Adresse
    {
        return $this->adresse1;
    }

    public function name(): ?string
    {
        return $this->nom;
    }

    public function nameByLanguage(string $language = 'fr'): ?string
    {
        return $this->labelByLanguage($language);
    }

    public function firstImage(): ?string
    {
        if ($this->images !== []) {
            return $this->images[0];
        }

        return null;
    }
}
