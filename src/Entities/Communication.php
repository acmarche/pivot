<?php

namespace AcMarche\Pivot\Entities;

class Communication
{
    public ?string $val = null;

    public ?string $typ = null;

    public ?string $tri = null;

    public ?Libelle $lib = null;

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
