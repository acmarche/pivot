<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class Address
{
    /** @var Label[] */
    public array $localite = [];

    /** @var Label[] */
    public array $commune = [];

    public function __construct(
        public ?string $rue = null,
        public ?string $numero = null,
        public ?string $boite = null,
        public ?int $idIns = null,
        public ?string $ins = null,
        public ?string $cp = null,
        public ?string $lieuDit = null,
        public ?string $lieuPrecis = null,
        public ?string $province = null,
        public ?string $pays = null,
        public ?float $lambertX = null,
        public ?float $lambertY = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?float $altitude = null,
        public ?bool $noaddress = null,
        public ?NaturalPark $parcNaturel = null,
        public ?Organisation $organisme = null,
    ) {}

    public function addLocalite(Label $label): void
    {
        $this->localite[] = $label;
    }

    public function addCommune(Label $label): void
    {
        $this->commune[] = $label;
    }

    public function getLocaliteByLang(string $lang): ?string
    {
        foreach ($this->localite as $label) {
            if ($label->lang === $lang) {
                return $label->value;
            }
        }

        return null;
    }

    public function getCommuneByLang(string $lang): ?string
    {
        foreach ($this->commune as $label) {
            if ($label->lang === $lang) {
                return $label->value;
            }
        }

        return null;
    }

    public function getFullAddress(): string
    {
        $parts = [];

        if ($this->rue) {
            $address = $this->rue;
            if ($this->numero) {
                $address .= ', ' . $this->numero;
            }
            if ($this->boite) {
                $address .= ' bte ' . $this->boite;
            }
            $parts[] = $address;
        }

        if ($this->cp || $this->getLocaliteByLang('fr')) {
            $parts[] = trim($this->cp . ' ' . ($this->getLocaliteByLang('fr') ?? ''));
        }

        return implode(' - ', $parts);
    }
}
