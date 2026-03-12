<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

use AcMarche\PivotAi\Entity\Traits\AccommodationTrait;
use AcMarche\PivotAi\Entity\Traits\AddressTrait;
use AcMarche\PivotAi\Entity\Traits\CommunicationTrait;
use AcMarche\PivotAi\Entity\Traits\DateTrait;
use AcMarche\PivotAi\Entity\Traits\DescriptionTrait;
use AcMarche\PivotAi\Entity\Traits\EquipmentTrait;
use AcMarche\PivotAi\Entity\Traits\EventTrait;
use AcMarche\PivotAi\Entity\Traits\IdentityTrait;
use AcMarche\PivotAi\Entity\Traits\ImageTrait;
use AcMarche\PivotAi\Entity\Traits\LabelTrait;
use AcMarche\PivotAi\Entity\Traits\ProxyTrait;
use AcMarche\PivotAi\Entity\Traits\RestaurantTrait;
use AcMarche\PivotAi\Entity\Traits\RouteTrait;
use AcMarche\PivotAi\Enums\UrnEnum;
use AcMarche\PivotAi\Parser\ThesaurusEnricher;

class Offer
{
    use AddressTrait, CommunicationTrait, DateTrait, DescriptionTrait, ImageTrait, ProxyTrait;
    use EquipmentTrait, LabelTrait, IdentityTrait;
    use AccommodationTrait, RestaurantTrait, EventTrait, RouteTrait;

    public ?string $url = null;

    /** @var Specification[] */
    public array $spec = [];

    /** @var array<string, Specification>|null */
    private ?array $specIndex = null;

    /** @var ClassificationLabel[]|null */
    private ?array $classificationLabels = null;

    private ?ThesaurusEnricher $thesaurusEnricher = null;
    private bool $thesaurusEnriched = false;

    /** @var RelatedOffer[] */
    public array $relOffre = [];

    /** @var RelatedOffer[] */
    public array $relOffreTgt = [];

    public function __construct(
        public ?string $codeCgt = null,
        public ?\DateTimeInterface $dateCreation = null,
        public ?\DateTimeInterface $dateModification = null,
        public ?string $nom = null,
        public ?int $estActive = null,
        public ?int $visibilite = null,
        public ?TypeOffre $typeOffre = null,
        public ?Address $adresse1 = null,
        public ?Address $adresse2 = null,
        public ?int $validationScore = null,
    ) {
    }

    public function setThesaurusEnricher(ThesaurusEnricher $enricher): void
    {
        $this->thesaurusEnricher = $enricher;
    }

    private function ensureEnriched(): void
    {
        if ($this->thesaurusEnriched || $this->thesaurusEnricher === null) {
            return;
        }

        $this->thesaurusEnriched = true;
        $this->thesaurusEnricher->enrichOffer($this);
    }

    /**
     * @return ClassificationLabel[]
     */
    public function getClassificationLabels(): array
    {
        $this->ensureEnriched();

        return $this->classificationLabels ?? [];
    }

    /**
     * @param ClassificationLabel[] $labels
     */
    public function setClassificationLabels(array $labels): void
    {
        $this->classificationLabels = $labels;
    }

    public function addClassificationLabel(ClassificationLabel $label): void
    {
        $this->classificationLabels ??= [];
        $this->classificationLabels[] = $label;
    }

    public function addSpec(Specification $spec): void
    {
        $this->spec[] = $spec;
        $this->specIndex = null;
    }

    public function addRelOffre(RelatedOffer $relOffre): void
    {
        $this->relOffre[] = $relOffre;
    }

    public function addRelOffreTgt(RelatedOffer $relOffreTgt): void
    {
        $this->relOffreTgt[] = $relOffreTgt;
    }

    public function getSpecByUrn(string $urn): ?Specification
    {
        return $this->getSpecIndex()[$urn] ?? null;
    }

    /**
     * @return array<string, Specification>
     */
    private function getSpecIndex(): array
    {
        if ($this->specIndex !== null) {
            return $this->specIndex;
        }

        $this->specIndex = [];
        foreach ($this->spec as $spec) {
            if ($spec->urn !== null && !isset($this->specIndex[$spec->urn])) {
                $this->specIndex[$spec->urn] = $spec;
            }
        }

        return $this->specIndex;
    }

    /**
     * @return Specification[]
     */
    public function getSpecsByUrn(string $urn): array
    {
        return array_filter($this->spec, fn(Specification $spec) => $spec->urn === $urn);
    }

    /**
     * @return Specification[]
     */
    public function getSpecsByCategory(string $urnCat): array
    {
        return array_filter($this->spec, fn(Specification $spec) => $spec->urnCat === $urnCat);
    }

    /**
     * @return Specification[]
     */
    public function getSpecsBySubCategory(string $urnSubCat): array
    {
        return array_filter($this->spec, fn(Specification $spec) => $spec->urnSubCat === $urnSubCat);
    }

    /**
     * @return Specification[]
     */
    public function getSpecsByType(string $type): array
    {
        return array_filter($this->spec, fn(Specification $spec) => $spec->type === $type);
    }

    public function getSpecValue(string $urn): ?string
    {
        $spec = $this->getSpecByUrn($urn);

        return $spec?->value;
    }

    /**
     * @return RelatedOffer[]
     */
    public function getMediaRelations(): array
    {
        return array_filter($this->relOffre, fn(RelatedOffer $rel) => $rel->isMediaRelation());
    }

    /**
     * @return RelatedOffer[]
     */
    public function getContactRelations(): array
    {
        return array_filter($this->relOffre, fn(RelatedOffer $rel) => $rel->isContactRelation());
    }

    /**
     * @return RelatedOffer[]
     */
    public function getOfferRelations(): array
    {
        return array_filter($this->relOffre, fn(RelatedOffer $rel) => $rel->isOfferRelation());
    }

    public function getDefaultMedia(): ?RelatedOffer
    {
        foreach ($this->relOffre as $rel) {
            if ($rel->urn === UrnEnum::LINK_MEDIA_DEFAULT->value) {
                return $rel;
            }
        }

        return null;
    }

    public function getPhone(): ?string
    {
        return $this->getSpecValue(UrnEnum::PHONE1->value);
    }

    public function getEmail(): ?string
    {
        return $this->getSpecValue(UrnEnum::MAIL1->value);
    }

    public function getWebsite(): ?string
    {
        return $this->getSpecValue(UrnEnum::URL_WEB->value);
    }

    public function getFacebookUrl(): ?string
    {
        return $this->getSpecValue(UrnEnum::URL_FACEBOOK->value);
    }

    public function getDescription(string $lang = 'fr'): ?string
    {
        $urn = $lang === 'fr' ? UrnEnum::DESC_MARKET->value : $lang.':'.UrnEnum::DESC_MARKET->value;

        return $this->getSpecValue($urn);
    }

    public function getShortDescription(string $lang = 'fr'): ?string
    {
        $urn = $lang === 'fr' ? UrnEnum::DESC_MARKET_SHORT->value : $lang.':'.UrnEnum::DESC_MARKET_SHORT->value;

        $description = $this->getSpecValue($urn);
        if (!empty($description)) {
            return $description;
        }

        return $this->getDescription($lang);
    }

    public function isActive(): bool
    {
        return $this->estActive === 30;
    }

    public function isVisible(): bool
    {
        return $this->visibilite === 30;
    }
}
