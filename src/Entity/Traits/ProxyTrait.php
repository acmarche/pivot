<?php

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Address;

trait ProxyTrait
{
    public function name(): string
    {
        return $this->nom ?? 'nom indéterminé';
    }

    public function address(): ?Address
    {
        if ($this->adresse1) {
            return $this->adresse1;
        }

        return $this->adresse2;
    }
}
