<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class Image
{
    public function __construct(
        public ?string $url = null,
        public ?string $codeCgt = null,
        public ?string $name = null,
        public ?string $copyright = null,
        public bool $isDefault = false,
    ) {}
}
