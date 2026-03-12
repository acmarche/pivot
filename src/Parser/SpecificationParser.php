<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Entity\Pivot\Specification;
use AcMarche\PivotAi\Enums\TypeOffreEnum;
use AcMarche\PivotAi\Enums\UrnEnum;

class SpecificationParser
{
    public function parseForOffer(Offer $offer): void
    {
        // EquipmentTrait
        $offer->equipments = $this->parseBooleanFlags($offer, UrnEnum::equipmentMap());
        $offer->equipmentOther = $offer->getSpecValue(UrnEnum::EQP_OTHER->value);
        $offer->equipmentOtherNl = $offer->getSpecValue('nl:'.UrnEnum::EQP_OTHER->value);
        $offer->equipmentOtherEn = $offer->getSpecValue('en:'.UrnEnum::EQP_OTHER->value);
        $offer->equipmentOtherDe = $offer->getSpecValue('de:'.UrnEnum::EQP_OTHER->value);

        // LabelTrait
        $offer->labels = $this->parseLabels($offer);
        $offer->signage = $offer->getSpecByUrn(UrnEnum::SIGNAGE->value);

        // IdentityTrait
        $offer->homepage = $offer->getSpecValue(UrnEnum::HOMEPAGE->value);
        $offer->nameNl = $offer->getSpecValue('nl:'.UrnEnum::NAME_OFFER->value);
        $offer->nameEn = $offer->getSpecValue('en:'.UrnEnum::NAME_OFFER->value);
        $offer->nameDe = $offer->getSpecValue('de:'.UrnEnum::NAME_OFFER->value);
        $offer->openingIndications = $offer->getSpecValue(UrnEnum::OPENING_INDICATIONS->value);
        $offer->addressSummary = $offer->getSpecValue(UrnEnum::ADDRESS_SUMMARY->value);

        $typeOffre = $this->resolveTypeOffre($offer);

        if ($typeOffre !== null) {
            if (in_array($typeOffre, TypeOffreEnum::accommodations(), true)) {
                $this->parseAccommodation($offer);
            }

            if ($typeOffre === TypeOffreEnum::RESTAURANT) {
                $this->parseRestaurant($offer);
            }

            if ($typeOffre === TypeOffreEnum::EVENT) {
                $this->parseEvent($offer);
            }

            if ($typeOffre === TypeOffreEnum::ROUTE) {
                $this->parseRoute($offer);
            }
        }
    }

    private function parseAccommodation(Offer $offer): void
    {
        $offer->classificationValue = $this->parseIntSpec($offer, UrnEnum::CLASS_VALUE->value);
        $offer->classificationTitle = $this->parseChoiceLabel($offer, UrnEnum::CLASS_TITLE->value);
        $offer->isSuperior = $this->parseBoolSpec($offer, UrnEnum::CLASS_SUPERIOR->value);
        $offer->michelinStars = $this->parseIntSpec($offer, UrnEnum::MICHELIN_STARS->value);
        $offer->michelinTitle = $this->parseChoiceLabel($offer, UrnEnum::MICHELIN_TITLE->value);
        $offer->isBibGourmand = $this->parseBoolSpec($offer, UrnEnum::BIB_GOURMAND->value);
        $offer->gaultMillauToques = $this->parseIntSpec($offer, UrnEnum::GAULT_MILLAU_TOQUES->value);
        $offer->gaultMillauTitle = $this->parseChoiceLabel($offer, UrnEnum::GAULT_MILLAU_TITLE->value);
    }

    private function parseRestaurant(Offer $offer): void
    {
        $offer->culinarySpecialties = $this->parsePrefixBooleanFlags($offer, UrnEnum::CULINARY_SPECIALTY_PREFIX->value);
        $offer->restaurantMichelinStars = $this->parseIntSpec($offer, UrnEnum::MICHELIN_STARS->value);
        $offer->restaurantMichelinTitle = $this->parseChoiceLabel($offer, UrnEnum::MICHELIN_TITLE->value);
        $offer->restaurantIsBibGourmand = $this->parseBoolSpec($offer, UrnEnum::BIB_GOURMAND->value);
        $offer->restaurantGaultMillauToques = $this->parseIntSpec($offer, UrnEnum::GAULT_MILLAU_TOQUES->value);
        $offer->restaurantGaultMillauTitle = $this->parseChoiceLabel($offer, UrnEnum::GAULT_MILLAU_TITLE->value);
    }

    private function parseEvent(Offer $offer): void
    {
        $offer->eventCategories = $this->parsePrefixBooleanFlags($offer, UrnEnum::EVENT_CATEGORY_PREFIX->value);
        $offer->eventType = $this->parseChoiceLabel($offer, UrnEnum::EVT_TYPE->value);
    }

    private function parseRoute(Offer $offer): void
    {
        $offer->distance = $this->parseFloatSpec($offer, UrnEnum::DISTANCE->value);
        $offer->circuitType = $this->parseChoiceLabel($offer, UrnEnum::CIRCUIT_TYPE->value);
        $offer->recommendation = $this->parseChoiceLabel($offer, UrnEnum::RECOMMENDATION->value);
        $offer->welcomeFlags = $this->parseBooleanFlags($offer, UrnEnum::welcomeMap());
    }

    /**
     * @param array<string, string> $urnMap
     * @return array<string, Specification>
     */
    private function parseBooleanFlags(Offer $offer, array $urnMap): array
    {
        $flags = [];

        foreach ($urnMap as $urn => $name) {
            $spec = $offer->getSpecByUrn($urn);
            if ($spec) {
                $flags[] = $spec;
            }
        }

        return $flags;
    }

    /**
     * @return array<string, Specification>
     */
    private function parsePrefixBooleanFlags(Offer $offer, string $prefix): array
    {
        $flags = [];

        foreach ($offer->spec as $spec) {
            if ($spec->urn === null || !str_starts_with($spec->urn, $prefix)) {
                continue;
            }

            if ($spec->value === 'true') {
                $shortName = substr($spec->urn, strlen($prefix));
                $flags[$shortName] = $spec;
            }
        }

        return $flags;
    }

    private function parseBoolSpec(Offer $offer, string $urn): bool
    {
        return $offer->getSpecValue($urn) === 'true';
    }

    private function parseIntSpec(Offer $offer, string $urn): ?int
    {
        $value = $offer->getSpecValue($urn);

        return $value !== null ? (int)$value : null;
    }

    private function parseFloatSpec(Offer $offer, string $urn): ?float
    {
        $value = $offer->getSpecValue($urn);

        return $value !== null ? (float)$value : null;
    }

    private function parseChoiceLabel(Offer $offer, string $urn, string $lang = 'fr'): ?string
    {
        $spec = $offer->getSpecByUrn($urn);

        if ($spec === null) {
            return null;
        }

        return $spec->getValueLabelByLang($lang) ?? $spec->value;
    }

    /**
     * @return array<string, Specification>
     */
    private function parseLabels(Offer $offer): array
    {
        $labels = [];

        $bike = $offer->getSpecByUrn(UrnEnum::LABEL_BIKE_FRIENDLY->value);
        if ($bike) {
            $labels[] = $bike ;
        }

        return $labels;
    }

    private function resolveTypeOffre(Offer $offer): ?TypeOffreEnum
    {
        if ($offer->typeOffre === null || $offer->typeOffre->idTypeOffre === null) {
            return null;
        }

        return TypeOffreEnum::tryFrom($offer->typeOffre->idTypeOffre);
    }
}
