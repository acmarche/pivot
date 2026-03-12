<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Entity\Pivot\Address;
use AcMarche\PivotAi\Entity\Pivot\Label;
use AcMarche\PivotAi\Entity\Pivot\NaturalPark;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Entity\Pivot\OfferResponse;
use AcMarche\PivotAi\Entity\Pivot\Organisation;
use AcMarche\PivotAi\Entity\Pivot\RelatedOffer;
use AcMarche\PivotAi\Entity\Pivot\Specification;
use AcMarche\PivotAi\Entity\Pivot\TypeOffre;

class PivotSerializer
{
    private const DATE_FORMAT = 'd-m-Y H:i:s';

    public function __construct(
        private readonly CommunicationParser $communicationParser,
        private readonly DateParser $dateParser,
        private readonly DescriptionParser $descriptionParser,
        private readonly ImageParser $imageParser,
        private readonly SpecificationParser $specificationParser,
        private readonly ThesaurusEnricher $thesaurusEnricher,
    ) {
        $this->thesaurusEnricher->initialize();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function deserializeOfferResponse(array $data): OfferResponse
    {
        $response = new OfferResponse(
            count: $data['count'] ?? 0,
            realCount: $data['realCount'] ?? null,
            itemsPerPage: $data['itemsPerPage'] ?? null,
            pagesCount: $data['pagesCount'] ?? null,
            token: $data['token'] ?? null,
        );

        if (isset($data['offre']) && is_array($data['offre'])) {
            foreach ($data['offre'] as $offerData) {
                $response->addOffre($this->deserializeOffer($offerData));
            }
        }

        return $response;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function deserializeOffer(array $data): Offer
    {
        $offer = new Offer(
            codeCgt: $data['codeCgt'] ?? null,
            dateCreation: $this->parseDate($data['dateCreation'] ?? null),
            dateModification: $this->parseDate($data['dateModification'] ?? null),
            nom: $data['nom'] ?? null,
            estActive: $data['estActive'] ?? null,
            visibilite: $data['visibilite'] ?? null,
            validationScore: $data['validationScore'] ?? null,
        );

        if (isset($data['typeOffre'])) {
            $offer->typeOffre = $this->deserializeTypeOffre($data['typeOffre']);
        }

        if (isset($data['adresse1'])) {
            $offer->adresse1 = $this->deserializeAddress($data['adresse1']);
        }

        if (isset($data['adresse2'])) {
            $offer->adresse2 = $this->deserializeAddress($data['adresse2']);
        }

        if (isset($data['spec']) && is_array($data['spec'])) {
            foreach ($data['spec'] as $specData) {
                $offer->addSpec($this->deserializeSpecification($specData));
            }
        }

        if (isset($data['relOffre']) && is_array($data['relOffre'])) {
            foreach ($data['relOffre'] as $relData) {
                $offer->addRelOffre($this->deserializeRelatedOffer($relData));
            }
        }

        if (isset($data['relOffreTgt']) && is_array($data['relOffreTgt'])) {
            foreach ($data['relOffreTgt'] as $relData) {
                $offer->addRelOffreTgt($this->deserializeRelatedOffer($relData));
            }
        }

        $offer->setThesaurusEnricher($this->thesaurusEnricher);
        $this->communicationParser->parseForOffer($offer);
        $this->dateParser->parseForOffer($offer);
        $this->descriptionParser->parseForOffer($offer);
        $this->imageParser->parseForOffer($offer);
        $this->specificationParser->parseForOffer($offer);

        return $offer;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeTypeOffre(array $data): TypeOffre
    {
        $typeOffre = new TypeOffre(
            idTypeOffre: $data['idTypeOffre'] ?? null,
        );

        if (isset($data['label']) && is_array($data['label'])) {
            foreach ($data['label'] as $labelData) {
                $typeOffre->addLabel($this->deserializeLabel($labelData));
            }
        }

        return $typeOffre;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeAddress(array $data): Address
    {
        $address = new Address(
            rue: $data['rue'] ?? null,
            numero: $data['numero'] ?? null,
            boite: $data['boite'] ?? null,
            idIns: $data['idIns'] ?? null,
            ins: $data['ins'] ?? null,
            cp: $data['cp'] ?? null,
            lieuDit: $data['lieuDit'] ?? null,
            lieuPrecis: $data['lieuPrecis'] ?? null,
            province: $data['province'] ?? null,
            pays: $data['pays'] ?? null,
            lambertX: isset($data['lambertX']) ? (float)$data['lambertX'] : null,
            lambertY: isset($data['lambertY']) ? (float)$data['lambertY'] : null,
            latitude: isset($data['latitude']) ? (float)$data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float)$data['longitude'] : null,
            altitude: isset($data['altitude']) ? (float)$data['altitude'] : null,
            noaddress: $data['noaddress'] ?? null,
        );

        if (isset($data['localite']) && is_array($data['localite'])) {
            foreach ($data['localite'] as $labelData) {
                $address->addLocalite($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['commune']) && is_array($data['commune'])) {
            foreach ($data['commune'] as $labelData) {
                $address->addCommune($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['parcNaturel'])) {
            $address->parcNaturel = $this->deserializeNaturalPark($data['parcNaturel']);
        }

        if (isset($data['organisme'])) {
            $address->organisme = $this->deserializeOrganisation($data['organisme']);
        }

        return $address;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeNaturalPark(array $data): NaturalPark
    {
        return new NaturalPark(
            idPn: $data['idPn'] ?? null,
            label: $data['label'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeOrganisation(array $data): Organisation
    {
        return new Organisation(
            idMdt: $data['idMdt'] ?? null,
            label: $data['label'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeSpecification(array $data): Specification
    {
        $spec = new Specification(
            urn: $data['urn'] ?? null,
            urnCat: $data['urnCat'] ?? null,
            urnSubCat: $data['urnSubCat'] ?? null,
            order: $data['order'] ?? null,
            type: $data['type'] ?? null,
            value: $data['value'] ?? null,
            codeCgt: $data['codeCgt'] ?? null,
            dateCreation: $this->parseDate($data['dateCreation'] ?? null),
            dateModification: $this->parseDate($data['dateModification'] ?? null),
        );

        if (isset($data['label']) && is_array($data['label'])) {
            foreach ($data['label'] as $labelData) {
                $spec->addLabel($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['valueLabel']) && is_array($data['valueLabel'])) {
            foreach ($data['valueLabel'] as $labelData) {
                $spec->addValueLabel($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['urnCatLabel']) && is_array($data['urnCatLabel'])) {
            foreach ($data['urnCatLabel'] as $labelData) {
                $spec->addUrnCatLabel($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['urnSubCatLabel']) && is_array($data['urnSubCatLabel'])) {
            foreach ($data['urnSubCatLabel'] as $labelData) {
                $spec->addUrnSubCatLabel($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['spec']) && is_array($data['spec'])) {
            foreach ($data['spec'] as $nestedSpecData) {
                $spec->addSpec($this->deserializeSpecification($nestedSpecData));
            }
        }

        return $spec;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeRelatedOffer(array $data): RelatedOffer
    {
        $relOffer = new RelatedOffer(
            urn: $data['urn'] ?? null,
            codeCgt: $data['codeCgt'] ?? null,
        );

        if (isset($data['label']) && is_array($data['label'])) {
            foreach ($data['label'] as $labelData) {
                $relOffer->addLabel($this->deserializeLabel($labelData));
            }
        }

        if (isset($data['offre'])) {
            $relOffer->offre = $this->deserializeOffer($data['offre']);
        }

        return $relOffer;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function deserializeLabel(array $data): Label
    {
        return new Label(
            lang: $data['lang'] ?? null,
            value: $data['value'] ?? null,
        );
    }

    private function parseDate(?string $dateString): ?\DateTimeInterface
    {
        if ($dateString === null) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $dateString);

        if ($date === false) {
            // Try ISO format as fallback
            $date = \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $dateString);
        }

        return $date ?: null;
    }
}
