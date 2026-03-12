<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class Description
{
    public function __construct(
        public ?string $fr = null,
        public ?string $nl = null,
        public ?string $en = null,
        public ?string $de = null,
        public ?string $shortFr = null,
        public ?string $shortNl = null,
        public ?string $shortEn = null,
        public ?string $shortDe = null,
    ) {}

    public function get(string $lang = 'fr'): ?string
    {
        $value = match ($lang) {
            'nl' => $this->nl,
            'en' => $this->en,
            'de' => $this->de,
            default => $this->fr,
        };

        if ($value !== null) {
            $value = str_replace('<p><br></p>', '', $value);
        }

        return $value;
    }

    public function getShort(string $lang = 'fr'): ?string
    {
        return match ($lang) {
            'nl' => $this->shortNl,
            'en' => $this->shortEn,
            'de' => $this->shortDe,
            default => $this->shortFr,
        };
    }

    /**
     * @return array<string, string>
     */
    public function getAllTranslations(): array
    {
        return array_filter([
            'fr' => $this->fr,
            'nl' => $this->nl,
            'en' => $this->en,
            'de' => $this->de,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function getAllShortTranslations(): array
    {
        return array_filter([
            'fr' => $this->shortFr,
            'nl' => $this->shortNl,
            'en' => $this->shortEn,
            'de' => $this->shortDe,
        ]);
    }
}
