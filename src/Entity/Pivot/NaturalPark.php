<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class NaturalPark
{
    public function __construct(
        public ?int $idPn = null,
        public ?string $label = null,
    ) {}
}
