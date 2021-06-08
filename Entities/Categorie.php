<?php


namespace AcMarche\Pivot\Entities;


class Categorie
{

    public string $id;

    public Libelle $lib;

    public string $value;

    public string $tri;

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
