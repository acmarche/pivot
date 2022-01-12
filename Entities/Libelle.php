<?php


namespace AcMarche\Pivot\Entities;

use Stringable;
class Libelle implements Stringable
{
    public const FR = 'fr';
    public const NL = 'nl';
    public const EN = 'en';
    public const DE = 'de';
    public const DEFAULT = 'default';
    public const COURT = 'lib_court';
    public const ENFANT = 'libelle_e';
    public const PARENT = 'libelle_p';

    /**
     * @var array
     */
    public array $languages;

    public function __construct()
    {
        $this->languages = [];
    }

    public function __toString(): string
    {
        return $this->libelle(self::FR);
    }

    public function add(?string $language, ?string $value): void
    {
        $language = $language == '' ? self::DEFAULT : $language;
        $this->languages[$language] = $value;
    }

    public function get(string $language): ?string
    {
        return $this->languages[$language] ?? null;
    }

    private function libelle(string $language): string
    {
        $languages = [];
        if (isset($languages[$language])) {
            return $this->languages[$language];
        }
        if (isset($languages[self::DEFAULT])) {
            return $this->languages[self::DEFAULT];
        }

        return '';
    }
}
