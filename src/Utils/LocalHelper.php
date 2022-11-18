<?php

namespace AcMarche\Pivot\Utils;

use Symfony\Component\Translation\LocaleSwitcher;

class LocalHelper
{
    public function __construct(public LocaleSwitcher $localeSwitcher)
    {
    }

    public function getLocale(): string
    {
        return $this->localeSwitcher->getLocale();
    }
}