<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Specification;

trait EventTrait
{
    /** @var array<string, Specification> */
    public array $eventCategories = [];

    public ?string $eventType = null;
    public bool $isRecurring = false;

    public function hasEventCategory(string $name): bool
    {
        return isset($this->eventCategories[$name]);
    }
}
