<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class RelatedOffer
{
    /** @var Label[] */
    public array $label = [];

    public function __construct(
        public ?string $urn = null,
        public ?string $codeCgt = null,
        public ?Offer $offre = null,
    ) {}

    public function addLabel(Label $label): void
    {
        $this->label[] = $label;
    }

    public function getLabelByLang(string $lang): ?string
    {
        foreach ($this->label as $label) {
            if ($label->lang === $lang) {
                return $label->value;
            }
        }

        return null;
    }

    public function isMediaRelation(): bool
    {
        return $this->urn && str_starts_with($this->urn, 'urn:lnk:media:');
    }

    public function isContactRelation(): bool
    {
        return $this->urn && str_starts_with($this->urn, 'urn:lnk:contact:');
    }

    public function isOfferRelation(): bool
    {
        return $this->urn && str_starts_with($this->urn, 'urn:lnk:offre:');
    }

    public function getRelationType(): ?string
    {
        if (!$this->urn) {
            return null;
        }

        if (preg_match('/^urn:lnk:([^:]+):(.+)$/', $this->urn, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getRelationSubType(): ?string
    {
        if (!$this->urn) {
            return null;
        }

        if (preg_match('/^urn:lnk:[^:]+:(.+)$/', $this->urn, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
