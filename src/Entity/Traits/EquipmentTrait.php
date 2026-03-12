<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Specification;

trait EquipmentTrait
{
    /** @var array<string, Specification> Amenity flags keyed by short name (e.g. 'wifi' => true) */
    public array $equipments = [];

    public ?string $equipmentOther = null;
    public ?string $equipmentOtherNl = null;
    public ?string $equipmentOtherEn = null;
    public ?string $equipmentOtherDe = null;

    public function getEquipmentOther(string $lang = 'fr'): ?string
    {
        return match ($lang) {
            'nl' => $this->equipmentOtherNl ?? $this->equipmentOther,
            'en' => $this->equipmentOtherEn ?? $this->equipmentOther,
            'de' => $this->equipmentOtherDe ?? $this->equipmentOther,
            default => $this->equipmentOther,
        };
    }
}
