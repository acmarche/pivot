<?php

namespace AcMarche\Pivot\Utils;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class CacheUtils
{
    public const MENU_NAME = 'menu-top';
    public const ICONES_NAME = 'icones-home';
    public const EVENTS = 'events';
    public const OFFRES = 'offres';
    public const OFFRE = 'offre';
    public const SEE_ALSO_OFFRES = 'see_also_offre';
    public const FETCH_OFFRES = 'fetch_offres';

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function generateKey(string $cacheKey): string
    {
        $keyUnicode = new UnicodeString($cacheKey);

        return $this->slugger->slug($keyUnicode->ascii()->toString());
    }

}