<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Entity\Pivot\DateEvent;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Entity\Pivot\Specification;

class DateParser
{
    public function parseForOffer(Offer $offer): void
    {
        foreach ($offer->spec as $spec) {
            if ($spec->urn !== 'urn:obj:date') {
                continue;
            }

            $dateEvent = $this->extractDateEvent($spec);
            $offer->addDate($dateEvent);
        }
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

    private function extractDateEvent(Specification $dateSpec): DateEvent
    {
        return new DateEvent(
            startDate: $this->parseDate($this->findNestedSpecValue($dateSpec, 'urn:fld:date:datedeb')),
            endDate: $this->parseDate($this->findNestedSpecValue($dateSpec, 'urn:fld:date:datefin')),
            openingHour1: $this->findNestedSpecValue($dateSpec, 'urn:fld:date:houv1'),
            closingHour1: $this->findNestedSpecValue($dateSpec, 'urn:fld:date:hferm1'),
            openingHour2: $this->findNestedSpecValue($dateSpec, 'urn:fld:date:houv2'),
            closingHour2: $this->findNestedSpecValue($dateSpec, 'urn:fld:date:hferm2'),
            dateRange: $this->findNestedSpecValue($dateSpec, 'urn:fld:date:daterange'),
            openingDetails: $this->findNestedSpecValue($dateSpec, 'urn:fld:date:detailouv'),
            openingDetailsNl: $this->findNestedSpecValue($dateSpec, 'nl:urn:fld:date:detailouv'),
            openingDetailsEn: $this->findNestedSpecValue($dateSpec, 'en:urn:fld:date:detailouv'),
            openingDetailsDe: $this->findNestedSpecValue($dateSpec, 'de:urn:fld:date:detailouv'),
        );
    }

    private function findNestedSpecValue(Specification $parentSpec, string $urn): ?string
    {
        foreach ($parentSpec->spec as $spec) {
            if ($spec->urn === $urn) {
                return $spec->value;
            }
        }

        return null;
    }

    /**
     * Parses a date string in DD/MM/YYYY format.
     */
    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('d/m/Y', $value);

        if ($date === false) {
            return null;
        }

        return $date->setTime(0, 0);
    }
}
