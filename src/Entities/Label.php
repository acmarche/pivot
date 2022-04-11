<?php

namespace AcMarche\Pivot\Entities;

class Label
{
    public const FR = 'fr';
    public const NL = 'nl';
    public const EN = 'en';
    public const DE = 'de';

    public string $lang;
    public ?string $value;

    public function get(string $language): ?string
    {
        if ($this->lang == $language) {
            return $this->value;
        }

        return null;
    }
}