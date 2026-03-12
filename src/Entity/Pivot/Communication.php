<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class Communication
{
    public function __construct(
        // Phone & email
        public ?string $phone1 = null,
        public ?string $phone2 = null,
        public ?string $mobile1 = null,
        public ?string $mobile2 = null,
        public ?string $email1 = null,
        public ?string $email2 = null,

        // Websites
        public ?string $website = null,
        public ?string $homepage = null,

        // Social networks
        public ?string $facebook = null,
        public ?string $instagram = null,

        // Reservation platforms
        public ?string $booking = null,
        public ?string $tripadvisor = null,
        public ?string $reservationUrl = null,
        public ?string $defaultReservationUrl = null,
    ) {}

    public function getPhone(): ?string
    {
        return $this->phone1 ?? $this->phone2;
    }

    public function getMobile(): ?string
    {
        return $this->mobile1 ?? $this->mobile2;
    }

    public function getEmail(): ?string
    {
        return $this->email1 ?? $this->email2;
    }

    /**
     * @return string[]
     */
    public function getPhones(): array
    {
        return array_values(array_filter([$this->phone1, $this->phone2,$this->mobile1, $this->mobile2]));
    }

    /**
     * @return string[]
     */
    public function getEmails(): array
    {
        return array_values(array_filter([$this->email1, $this->email2]));
    }

    /**
     * @return array<string, string>
     */
    public function getSocialLinks(): array
    {
        $links = [];

        if ($this->facebook !== null) {
            $links['facebook'] = $this->facebook;
        }
        if ($this->instagram !== null) {
            $links['instagram'] = $this->instagram;
        }

        return $links;
    }

    /**
     * @return array<string, string>
     */
    public function getReservationLinks(): array
    {
        return array_filter([
            'booking' => $this->booking,
            'tripadvisor' => $this->tripadvisor,
            'reservation' => $this->reservationUrl,
            'defaultReservation' => $this->defaultReservationUrl,
        ]);
    }

    public function hasSocialLinks(): bool
    {
        return $this->facebook !== null || $this->instagram !== null;
    }

    public function hasReservationLinks(): bool
    {
        return $this->booking !== null
            || $this->tripadvisor !== null
            || $this->reservationUrl !== null
            || $this->defaultReservationUrl !== null;
    }

    public function getPreferredReservationUrl(): ?string
    {
        return $this->defaultReservationUrl
            ?? $this->reservationUrl
            ?? $this->booking;
    }
}
