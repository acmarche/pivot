<?php


namespace AcMarche\Pivot\Entities;

use DOMElement;
use DOMDocument;
use AcMarche\Pivot\Parser\OffreParser;

class Offre implements OffreInterface
{
    public ?string $id = null;

    public Libelle $libelle;

    public ?string $reference = null;

    public Geocode $geocode;

    public Localite $localisation;

    public string $url;
    /**
     * @var Contact[]
     */
    public array $contacts;
    /**
     * @var Description[]
     */
    public array $descriptions;
    /**
     * @var Media[]
     */
    public array $medias;
    /**
     * @var Categorie[]
     */
    public array $categories;
    /**
     * @var Selection[]
     */
    public array $selections;
    /**
     * @var Horaire[]
     */
    public array $horaires;

    public ?string $modif_date = null;
    /**
     * @var Horline[]
     */
    public array $datesR;

    public ?string $publiable = null;

    public ?string $image = null;
    /**
     * @var array|int[]
     */
    public array $enfantIds;
    /**
     * @var array|int[]
     */
    public array $parentIds;
    /**
     * @var array|OffreInterface[]
     */
    public array $enfants;
    /**
     * @var array|OffreInterface[]
     */
    public array $parents;

    public function __construct()
    {
        $this->categories = [];
        $this->medias = [];
        $this->contacts = [];
        $this->horaires = [];
        $this->enfantIds = [];
        $this->parentIds = [];
        $this->enfants = [];
        $this->parents = [];
    }

    public static function createFromDom(DOMElement $offreDom, DOMDocument $document): ?Offre
    {
        $parser = new OffreParser($document, $offreDom);
        $offre = new self();
        $offre->id = $parser->offreId();
        $offre->libelle = $parser->getTitre($offreDom);
        $offre->publiable = $parser->getAttributs('publiable');
        $offre->reference = $parser->getAttributs('off_id_ref');
        $offre->modif_date = $parser->getAttributs('modif_date');
        $offre->categories = $parser->categories($offreDom);
        $offre->medias = $parser->medias($offreDom);
        $offre->geocode = $parser->geocodes($offreDom);
        $offre->localisation = $parser->localisation($offreDom);
        $offre->descriptions = $parser->descriptions($offreDom);
        $offre->selections = $parser->selections();
        $offre->contacts = $parser->contacts($offreDom);
        $offre->horaires = $parser->horaires($offreDom);
        $offre->datesR = $offre->dates();
        $offre->image = $offre->firstImage();
        $offre->parentIds = $parser->parents($offreDom);
        $offre->enfantIds = $parser->enfants($offreDom);

        return $offre;
    }

    public function getTitre(?string $language = 'fr'): string
    {
        if ($this->libelle->get($language) && $this->libelle->get($language)) {
            return $this->libelle->get($language);
        }

        if ($titre = $this->libelle->get('fr')) {
            return $titre;
        }

        return 'titre found';
    }

    public function contactPrincipal(): ?Contact
    {
        $contacts = array_filter(
            $this->contacts,
            function ($contact) {
                $default = $contact->lib->get('default');
                if ($default === 'contact') {
                    return $contact;
                }

                return null;
            }
        );
        if ($contacts !== []) {
            return $contacts[array_key_first($contacts)];
        }

        return $this->contacts !== [] ? $this->contacts[0] : null;
    }

    public function communcationPrincipal(): array
    {
        $coms = [];
        $contact = $this->contactPrincipal();
        if ($contact !== null) {
            foreach ($contact->communications as $communication) {
                $coms[$communication->typ][$communication->lib->get('default')] = $communication->val;
            }
        }

        return $coms;
    }

    public function emailPrincipal(): ?string
    {
        $emails = $this->communcationPrincipal()['mail'] ?? [];

        return $emails['mail'] ?? null;
    }

    public function telPrincipal(): ?string
    {
        $telephones = $this->communcationPrincipal()['tel'] ?? [];

        return $telephones['tel'] ?? null;
    }

    public function sitePrincipal(): ?string
    {
        $sites = $this->communcationPrincipal()['url'] ?? [];

        $site = $sites['url'] ?? null;
        if ($site) {
            return $site;
        }

        $site = $sites['url_facebook'] ?? null;
        if ($site) {
            return $site;
        }

        return null;
    }

    /**
     * Utilise dans @return Horline|null
     * @see EventUtils
     */
    public function firstHorline(): ?Horline
    {
        if ($this->horaires !== [] && count($this->horaires[0]->horlines)) {
            return $this->horaires[0]->horlines[0];
        }

        return null;
    }

    /**
     * Raccourcis util a react
     *
     * @return Horline[]
     */
    public function dates(): array
    {
        $dates = [];
        foreach ($this->horaires as $horaire) {
            foreach ($horaire->horlines as $horline) {
                $dates[] = $horline;
            }
        }

        return $dates;
    }

    function firstImage(): ?string
    {
        return $this->medias !== [] ? $this->medias[0]->url : null;
    }

}
