<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Pivot;

class TypeOffre
{
    /** @var Label[] */
    public array $label = [];

    public function __construct(
        public ?int $idTypeOffre = null,
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
}
