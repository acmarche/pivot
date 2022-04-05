<?php

namespace AcMarche\Pivot\Entities\Pivot;

class Adresse
{
    public int $idIns;
    public string $ins;
    public string $cp;
    /**
     * @var Label[] $localite
     */
    public array $localite;
    /**
     * @var Label[] $commune
     */
    public array $commune;
    public string $province;
    public UrnLabel $provinceUrn;
    public string $pays;
    public UrnLabel $paysUrn;
    public float $lambertX;
    public float $lambertY;
    public float $latitude;
    public float $longitude;
    public float $altitude;
    public bool $noaddress;
    /**
     * var IdPnClass[] $parcNaturel
     */
    public array $parcNaturel;
    /**
     * var Organisme[]
     */
    public array $organisme;

    public function localiteByLanguage(string $language = Label::FR): string
    {
        foreach ($this->localite as $label) {
            if ($label->get($language)) {
                return $label->get($language);
            }
        }

        return 'localite title found';
    }

    public function communeByLanguage(string $language = Label::FR): string
    {
        foreach ($this->commune as $label) {
            if ($label->get($language)) {
                return $label->get($language);
            }
        }

        return 'commune title found';
    }

}
