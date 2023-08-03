<?php

namespace AcMarche\Pivot\Utils;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class CacheUtils
{
    final public const MENU_NAME = 'menu-top';
    final public const ICONES_NAME = 'icones-home';
    final public const EVENTS = 'events';
    final public const OFFRES = 'offres';
    final public const OFFRE = 'offre';
    final public const SEE_ALSO_OFFRES = 'see_also_offre';
    final public const FETCH_OFFRES = 'fetch_offres';

    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function generateKey(string $cacheKey): string
    {
        $keyUnicode = new UnicodeString($cacheKey);

        return $this->slugger->slug($keyUnicode->ascii()->toString());
    }

}