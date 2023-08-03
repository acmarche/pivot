<?php

namespace AcMarche\Pivot\Entities;

class Label
{
    final public const FR = 'fr';
    final public const NL = 'nl';
    final public const EN = 'en';
    final public const DE = 'de';

    public string $lang;
    public ?string $value = null;

    public function get(string $language): ?string
    {
        if ($this->lang === $language) {
            return $this->value;
        }

        return null;
    }
}
