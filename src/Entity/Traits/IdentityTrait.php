<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

trait IdentityTrait
{
    public ?string $homepage = null;
    public ?string $nameNl = null;
    public ?string $nameEn = null;
    public ?string $nameDe = null;
    public ?string $openingIndications = null;
    public ?string $addressSummary = null;

    public function getTranslatedName(string $lang = 'fr'): ?string
    {
        return match ($lang) {
            'nl' => $this->nameNl,
            'en' => $this->nameEn,
            'de' => $this->nameDe,
            default => null,
        };
    }
}
