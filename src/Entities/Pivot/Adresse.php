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
     * @var Label[]
     */
    public array $commune;
    public string $province;
    public Urn $provinceUrn;
    public string $pays;
    public Urn $paysUrn;
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
    public array $organisme22;
}