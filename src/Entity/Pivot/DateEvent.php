<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class DateEvent
{
    public function __construct(
        public ?\DateTimeInterface $startDate = null,
        public ?\DateTimeInterface $endDate = null,
        public ?string $openingHour1 = null,
        public ?string $closingHour1 = null,
        public ?string $openingHour2 = null,
        public ?string $closingHour2 = null,
        public ?string $dateRange = null,
        public ?string $openingDetails = null,
        public ?string $openingDetailsNl = null,
        public ?string $openingDetailsEn = null,
        public ?string $openingDetailsDe = null,
    ) {}

    public function isSingleDay(): bool
    {
        if ($this->startDate === null || $this->endDate === null) {
            return false;
        }

        return $this->startDate->format('Y-m-d') === $this->endDate->format('Y-m-d');
    }

    public function hasOpeningHours(): bool
    {
        return $this->openingHour1 !== null || $this->openingHour2 !== null;
    }

    public function getOpeningDetail(string $lang = 'fr'): ?string
    {
        return match ($lang) {
            'nl' => $this->openingDetailsNl ?? $this->openingDetails,
            'en' => $this->openingDetailsEn ?? $this->openingDetails,
            'de' => $this->openingDetailsDe ?? $this->openingDetails,
            default => $this->openingDetails,
        };
    }
}
