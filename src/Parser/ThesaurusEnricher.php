<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Parser;

use AcMarche\PivotAi\Api\ThesaurusClient;
use AcMarche\PivotAi\Entity\Pivot\ClassificationLabel;
use AcMarche\PivotAi\Entity\Pivot\Offer;
use AcMarche\PivotAi\Entity\Pivot\Specification;
use AcMarche\PivotAi\Enums\TypeOffreEnum;
use AcMarche\PivotAi\Enums\UrnEnum;

readonly class ThesaurusEnricher
{
    public function __construct(
        private ThesaurusClient $thesaurusClient,
    ) {
    }

    public function initialize(): void
    {
        Specification::setThesaurusClient($this->thesaurusClient);
    }

    public function enrichOffer(Offer $offer): void
    {
        $this->populateClassificationLabels($offer);
    }

    private function populateClassificationLabels(Offer $offer): void
    {
        foreach ($offer->spec as $spec) {
            if ($spec->urnCat !== UrnEnum::CAT_CLASSLAB->value) {
                continue;
            }

            if ($offer->typeOffre->idTypeOffre === TypeOffreEnum::HOTEL->value) {
                continue;
            }

            if (in_array($spec->urn, ['rn:fld:typeheb', 'urn:fld:typeheb', 'urn:fld:class', 'urn:fld:class:title'])) {
                continue;
            }

            $label = $spec->getLabelByLang('fr');
            if ($label === null) {
                continue;
            }

            if ($spec->isBoolean() && $spec->getBooleanValue() === true) {
                $offer->addClassificationLabel(new ClassificationLabel($spec->urn, $label));
            } elseif ($spec->isValueUrn()) {
                if (str_ends_with($spec->value, ':nc')) {
                    continue;
                }
                $valueLabel = $spec->getValueLabelByLang('fr');
                $offer->addClassificationLabel(new ClassificationLabel($spec->urn, $label, $valueLabel));
            }
        }
    }
}
