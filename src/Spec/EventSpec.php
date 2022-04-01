<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\Spec;
use AcMarche\Pivot\Utils\DateUtils;

class EventSpec
{
    public const  datedebvalid = 'urn:fld:datedebvalid';
    public const datefinvalid = "urn:fld:datefinvalid";

    /**
     * @param Spec[] $specs
     */
    public function __construct(private array $specs)
    {
    }

    /**
     * @param array $specs
     * @param string $key
     * @return null|Spec
     */
    public function getByUrn(string $key, bool $value = false): Spec|string|null
    {
        foreach ($this->specs as $spec) {
            if ($spec->urn === $key) {
                if ($value) {
                    return $spec->value;
                }

                return $spec;
            }
        }

        return null;
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

    /**
     * @param string $type
     * @return array|Spec[]
     */
    public function getByType(string $type): array
    {
        $values = [];
        foreach ($this->specs as $spec) {
            if ($spec->type === $type) {
                $values[] = $spec;
            }
        }

        return $values;
    }
}
