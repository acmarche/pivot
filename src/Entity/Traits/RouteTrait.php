<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Specification;

trait RouteTrait
{
    public ?float $distance = null;
    public ?string $circuitType = null;
    public ?string $recommendation = null;

    /** @var array<string, Specification> */
    public array $welcomeFlags = [];
}
