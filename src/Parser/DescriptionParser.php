<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Entity\Pivot\Description;
use AcMarche\PivotAi\Entity\Pivot\Offer;

class DescriptionParser
{
    public function parseForOffer(Offer $offer): void
    {
        $offer->description =new Description(
            fr: $offer->getSpecValue('urn:fld:descmarket'),
            nl: $offer->getSpecValue('nl:urn:fld:descmarket'),
            en: $offer->getSpecValue('en:urn:fld:descmarket'),
            de: $offer->getSpecValue('de:urn:fld:descmarket'),
            shortFr: $offer->getSpecValue('urn:fld:descmarket20'),
            shortNl: $offer->getSpecValue('nl:urn:fld:descmarket20'),
            shortEn: $offer->getSpecValue('en:urn:fld:descmarket20'),
            shortDe: $offer->getSpecValue('de:urn:fld:descmarket20'),
        );
    }

    /**
     * @param Offer[] $offers
     */
    public function parseForOffers(array $offers): void
    {
        foreach ($offers as $offer) {
            $this->parseForOffer($offer);
        }
    }
}
