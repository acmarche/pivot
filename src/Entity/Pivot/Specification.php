<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

use AcMarche\PivotAi\Api\ThesaurusClient;

class Specification
{
    private static ?ThesaurusClient $thesaurusClient = null;

    /** @var Label[] */
    public array $label = [];

    /** @var Label[] */
    public array $valueLabel = [];

    /** @var Label[] */
    public array $urnCatLabel = [];

    /** @var Label[] */
    public array $urnSubCatLabel = [];

    /** @var Specification[] */
    public array $spec = [];

    private bool $labelResolved = false;
    private bool $valueLabelResolved = false;
    private bool $urnCatLabelResolved = false;
    private bool $urnSubCatLabelResolved = false;

    public function __construct(
        public ?string $urn = null,
        public ?string $urnCat = null,
        public ?string $urnSubCat = null,
        public ?int $order = null,
        public ?string $type = null,
        public ?string $value = null,
        public ?string $codeCgt = null,
        public ?\DateTimeInterface $dateCreation = null,
        public ?\DateTimeInterface $dateModification = null,
    ) {}

    public static function setThesaurusClient(ThesaurusClient $client): void
    {
        self::$thesaurusClient = $client;
    }

    public function addLabel(Label $label): void
    {
        $this->label[] = $label;
        $this->labelResolved = true;
    }

    public function addValueLabel(Label $label): void
    {
        $this->valueLabel[] = $label;
        $this->valueLabelResolved = true;
    }

    public function addUrnCatLabel(Label $label): void
    {
        $this->urnCatLabel[] = $label;
        $this->urnCatLabelResolved = true;
    }

    public function addUrnSubCatLabel(Label $label): void
    {
        $this->urnSubCatLabel[] = $label;
        $this->urnSubCatLabelResolved = true;
    }

    public function addSpec(Specification $spec): void
    {
        $this->spec[] = $spec;
    }

    private function resolveLabel(): void
    {
        if ($this->labelResolved) {
            return;
        }
        $this->labelResolved = true;

        if ($this->urn !== null && $this->label === [] && self::$thesaurusClient !== null) {
            $this->label = self::$thesaurusClient->getLabelsForUrn($this->getBaseUrn());
        }
    }

    private function resolveValueLabel(): void
    {
        if ($this->valueLabelResolved) {
            return;
        }
        $this->valueLabelResolved = true;

        if ($this->isValueUrn() && $this->valueLabel === [] && self::$thesaurusClient !== null) {
            $this->valueLabel = self::$thesaurusClient->getLabelsForUrn($this->value);
        }
    }

    private function resolveUrnCatLabel(): void
    {
        if ($this->urnCatLabelResolved) {
            return;
        }
        $this->urnCatLabelResolved = true;

        if ($this->urnCat !== null && $this->urnCatLabel === [] && self::$thesaurusClient !== null) {
            $this->urnCatLabel = self::$thesaurusClient->getLabelsForUrn($this->urnCat);
        }
    }

    private function resolveUrnSubCatLabel(): void
    {
        if ($this->urnSubCatLabelResolved) {
            return;
        }
        $this->urnSubCatLabelResolved = true;

        if ($this->urnSubCat !== null && $this->urnSubCatLabel === [] && self::$thesaurusClient !== null) {
            $this->urnSubCatLabel = self::$thesaurusClient->getLabelsForUrn($this->urnSubCat);
        }
    }

    public function getLabelByLang(string $lang): ?string
    {
        $this->resolveLabel();

        foreach ($this->label as $label) {
            if ($label->lang === $lang) {
                return $label->value;
            }
        }

        return null;
    }

    public function getValueLabelByLang(string $lang): ?string
    {
        $this->resolveValueLabel();

        foreach ($this->valueLabel as $label) {
            if ($label->lang === $lang) {
                return $label->value;
            }
        }

        return null;
    }

    public function isBoolean(): bool
    {
        return $this->type === 'Boolean';
    }

    public function isChoice(): bool
    {
        return $this->type === 'Choice';
    }

    public function isCurrency(): bool
    {
        return $this->type === 'Currency';
    }

    public function isText(): bool
    {
        return in_array($this->type, ['String', 'TextML', 'FirstUpperStringML'], true);
    }

    public function getBooleanValue(): ?bool
    {
        if (!$this->isBoolean()) {
            return null;
        }

        return $this->value === 'true';
    }

    public function isValueUrn(): bool
    {
        return $this->value !== null && str_starts_with($this->value, 'urn:');
    }

    public function getCurrencyValue(): ?float
    {
        if (!$this->isCurrency()) {
            return null;
        }

        return (float) $this->value;
    }

    public function getLanguagePrefix(): ?string
    {
        if ($this->urn && preg_match('/^([a-z]{2}):/', $this->urn, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getBaseUrn(): string
    {
        if ($prefix = $this->getLanguagePrefix()) {
            return substr($this->urn, strlen($prefix) + 1);
        }

        return $this->urn ?? '';
    }
}
