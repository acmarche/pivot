<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Entity\Pivot\Communication;
use AcMarche\PivotAi\Entity\Pivot\Offer;

class CommunicationParser
{
    public function parseForOffer(Offer $offer): void
    {
        $offer->communication = new Communication(
            phone1: $offer->getSpecValue('urn:fld:phone1'),
            phone2: $offer->getSpecValue('urn:fld:phone2'),
            mobile1: $offer->getSpecValue('urn:fld:mobi1'),
            mobile2: $offer->getSpecValue('urn:fld:mobi2'),
            email1: $offer->getSpecValue('urn:fld:mail1'),
            email2: $offer->getSpecValue('urn:fld:mail2'),
            website: $offer->getSpecValue('urn:fld:urlweb'),
            homepage: $offer->getSpecValue('urn:fld:homepage'),
            facebook: $offer->getSpecValue('urn:fld:urlfacebook'),
            instagram: $offer->getSpecValue('urn:fld:urlinstagram'),
            booking: $offer->getSpecValue('urn:fld:urlbooking'),
            tripadvisor: $offer->getSpecValue('urn:fld:tripadvisor'),
            reservationUrl: $offer->getSpecValue('urn:fld:orc'),
            defaultReservationUrl: $offer->getSpecValue('urn:fld:urlresa:default'),
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
