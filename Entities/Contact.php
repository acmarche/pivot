<?php


namespace AcMarche\Pivot\Entities;

class Contact
{
    public ?string $id = null;

    public ?Libelle $lib = null;

    public ?string $civilite = null;

    public ?string $noms = null;

    public ?string $prenoms = null;

    public ?string $societe = null;

    public ?string $adresse = null;

    public ?string $numero = null;

    public ?string $boite = null;

    public ?string $postal = null;

    public ?string $pays = null;

    public ?string $l_nom = null;

    public ?string $remarque = null;
    /**
     * @var Communication[]
     */
    public array $communications;

    public array $lgs = [];

    public ?string $tri = null;

    public function __construct()
    {
        $this->communications = [];
    }

    public function localite(): ?string
    {
        return $this->l_nom;
    }

    public function getLib(?string $language = 'fr'): string
    {
        if ($this->lib->get($language) && $this->lib->get($language)) {
            return $this->lib->get($language);
        }
        //try in french
        if ($titre = $this->lib->get('fr')) {
            return $titre;
        }

        return '';
    }
}
