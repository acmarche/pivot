<?php

namespace AcMarche\PivotAi\Entity\Traits;

trait AddressTrait
{
    public function locality(): ?string
    {
        return $this->adresse1?->getLocaliteByLang('fr');
    }

    public function latitude(): ?float
    {
        return $this->adresse1?->latitude;

    }

    public function longitude(): ?float
    {
        return $this->adresse1?->longitude;
    }
}
