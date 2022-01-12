<?php


namespace AcMarche\Pivot\Entities;


class Horaire
{
    public ?string $year = null;

    public ?Libelle $lib = null;

    public ?Libelle $texte = null;
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
        if (($titre = $this->getLib()) !== '' && ($titre = $this->getLib()) !== '0') {
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
