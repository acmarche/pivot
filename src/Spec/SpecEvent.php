<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\Spec;
use AcMarche\Pivot\Utils\DateUtils;

class SpecEvent
{
    use SpecTrait;

    public const  datedebvalid = 'urn:fld:datedebvalid';
    public const datefinvalid = "urn:fld:datefinvalid";

    /**
     * @param Spec[] $specs
     */
    public function __construct(array $specs)
    {
    }

    public function dateValidete(): array
    {
        $format = "d/m/Y";
        $dateDebut = $this->getByUrn(self::datedebvalid, true);
        if ($dateDebut) {
            $dateDebut = DateUtils::convertStringToDateTime($dateDebut, $format);
        }

        $dateFin = $this->getByUrn(self::datefinvalid);
        if ($dateFin->value) {
            $dateFin = DateUtils::convertStringToDateTime($dateFin->value, $format);
        }

        return [
            $dateDebut,
            $dateFin,
        ];
    }

    public function getHomePage(): ?string
    {
        $spec = $this->getByUrn("urn:fld:homepage");
        if ($spec) {
            return $spec->value;
        }

        return null;
    }

    public function isActive(): bool
    {
        $spec = $this->getByUrn("urn:fld:typeevt:activrecur");
        if ($spec) {
            return (bool)$spec->value;
        }

        return false;
    }
}
