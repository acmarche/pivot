<?php


namespace AcMarche\Pivot\Entities;


class Description
{
    public string $dat;

    public string $lot;

    public string $typ;

    public Libelle $texte;

    public Libelle $lib;

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

        return 'lib found';
    }

    public function getTexte(?string $language = 'fr'): string
    {
        if ($this->texte->get($language) && $this->texte->get($language)) {
            return $this->texte->get($language);
        }
        //try in french
        if ($titre = $this->texte->get('fr')) {
            return $titre;
        }

        return 'texte found';
    }
}
