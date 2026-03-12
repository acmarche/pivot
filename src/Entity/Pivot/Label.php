<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class Label
{
    public function __construct(
        public ?string $lang = null,
        public ?string $value = null,
    ) {}
}
