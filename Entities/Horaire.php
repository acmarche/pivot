<?php


namespace AcMarche\Pivot\Entities;


class Horaire
{
    public string $year;

    public Libelle $lib;

    public Libelle $texte;
    /**
     * @var Horline[]
     */
    public array $horlines = [];

    public function getLib(?string $language = 'fr'): string
    {
        if ($this->lib->get($language) && $this->lib->get($language)) {
            return $this->lib->get($language);
        }
        //try in french
        if ($titre = $this->getLib()) {
            return $titre;
        }

        return '';
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

        return '';
    }
}
