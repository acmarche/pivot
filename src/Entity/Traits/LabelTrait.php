<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Specification;

trait LabelTrait
{
    /** @var array<string, Specification> Quality labels keyed by short name */
    public array $labels = [];

    public ?Specification $signage = null;

    public function hasLabel(string $name): bool
    {
        return isset($this->labels[$name]);
    }
}
