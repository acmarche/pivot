<?php


namespace AcMarche\Pivot\Entities;

class Contact
{
    public string $id;

    public Libelle $lib;

    public string $civilite;

    public ?string $noms = null;

    public string $prenoms;

    public string $societe;

    public string $adresse;

    public string $numero;

    public ?string $boite = null;

    public string $postal;

    public string $pays;

    public string $l_nom;

    public string $remarque;
    /**
     * @var Communication[]
     */
    public array $communications;
    /**
     * @var array
     */
    public array $lgs;

    public string $tri;

    public function __construct()
    {
        $this->communications = [];
    }

    public function localite(): string
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
