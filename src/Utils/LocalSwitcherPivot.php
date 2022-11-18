<?php

namespace AcMarche\Pivot\Utils;

use Symfony\Component\Translation\LocaleSwitcher;

class LocalSwitcherPivot
{
    public function __construct(public LocaleSwitcher $localeSwitcher)
    {
    }

    public function getLocale(): string
    {
        return $this->localeSwitcher->getLocale();
    }

    public function setLocale(string $locale): void
    {
        $this->localeSwitcher->setLocale($locale);
    }
}