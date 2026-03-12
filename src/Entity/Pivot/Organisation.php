<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class Organisation
{
    public function __construct(
        public ?int $idMdt = null,
        public ?string $label = null,
    ) {}
}
