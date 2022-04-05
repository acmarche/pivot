<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\SpecData;
use AcMarche\Pivot\Utils\DateUtils;

class SpecEvent
{
    use SpecTrait;

    /**
     * @param SpecData[] $specs
     */
    public function __construct(array $specs)
    {
        $this->specs = $specs;
    }

    public function dateBeginAndEnd(): array
    {
        $dates = [];
        $format = "d/m/Y";
        $dateDebut = $this->getByUrn(UrnConst::DATE_DEB_VALID, true);
        if ($dateDebut) {
            $dates[] = DateUtils::convertStringToDateTime($dateDebut, $format);
        }

        $dateFin = $this->getByUrn(UrnConst::DATE_FIN_VALID, true);
        if ($dateFin) {
            $dates[] = DateUtils::convertStringToDateTime($dateFin, $format);
        }

        return $dates;
    }

    public function getHomePage(): ?string
    {
        $spec = $this->getByUrn(UrnConst::HOMEPAGE);
        if ($spec) {
            return $spec->value;
        }

        return null;
    }

    public function isActive(): bool
    {
        $spec = $this->getByUrn(UrnConst::ACTIVE);
        if ($spec) {
            return (bool)$spec->value;
        }

        return false;
    }
}
