<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\Spec;
use AcMarche\Pivot\Utils\DateUtils;

class SpecEvent
{
    use SpecTrait;

    /**
     * @param Spec[] $specs
     */
    public function __construct(array $specs)
    {
        $this->specs = $specs;
    }

    public function dateBeginAndEnd(): array
    {
        $dates = [];
        $format = "d/m/Y";
        $dateDebut = $this->getByUrn(UrnEnum::datedebvalid, true);
        if ($dateDebut) {
            $dates[] = DateUtils::convertStringToDateTime($dateDebut, $format);
        }

        $dateFin = $this->getByUrn(UrnEnum::datefinvalid, true);
        if ($dateFin) {
            $dates[] = DateUtils::convertStringToDateTime($dateFin, $format);
        }

        return $dates;
    }

    public function getHomePage(): ?string
    {
        $spec = $this->getByUrn(UrnEnum::HOMEPAGE);
        if ($spec) {
            return $spec->value;
        }

        return null;
    }

    public function isActive(): bool
    {
        $spec = $this->getByUrn(UrnEnum::ACTIVE);
        if ($spec) {
            return (bool)$spec->value;
        }

        return false;
    }
}
