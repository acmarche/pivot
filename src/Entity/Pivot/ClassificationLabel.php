<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

readonly class ClassificationLabel
{
    public function __construct(
        public string $urn,
        public string $label,
        public ?string $valueLabel = null,
    ) {
    }

    public function name(): string
    {
        return $this->label;
    }

    public function __toString(): string
    {
        if ($this->valueLabel !== null) {
            return sprintf('%s: %s', $this->label, $this->valueLabel);
        }

        return $this->label;
    }
}
