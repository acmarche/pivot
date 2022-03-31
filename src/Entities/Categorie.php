<?php

namespace AcMarche\Pivot\Entities;

class Categorie
{
    public ?string $id = null;

    public ?Libelle $lib = null;

    public ?string $value = null;

    public ?string $tri = null;

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
