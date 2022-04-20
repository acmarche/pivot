<?php

namespace AcMarche\Pivot\Entities\Specification;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Spec\SpecTrait;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Utils\DateUtils;
use AcMarche\Pivot\Utils\SortUtils;

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
        $dates     = [];
        $format    = "d/m/Y";
        $dateDebut = $this->findByUrn(UrnList::DATE_DEB_VALID);
        if (count($dateDebut) > 0) {
            $dates[] = DateUtils::convertStringToDateTime($dateDebut[0]->value, $format);
        }

        $dateFin = $this->findByUrn(UrnList::DATE_FIN_VALID);
        if (count($dateFin) > 0) {
            $dates[] = DateUtils::convertStringToDateTime($dateFin[0]->value, $format);
        }

        return $dates;
    }

    public function getHomePage(): ?string
    {
        $specs = $this->findByUrn(UrnList::HOMEPAGE);
        if (count($specs) > 0) {
            return $specs[0]->value;
        }

        return null;
    }

    public function isActive(): bool
    {
        $specs = $this->findByUrn(UrnList::ACTIVE);
        if (count($specs) > 0) {
            return (bool)$specs[0]->value;
        }

        return false;
    }

    /**
     * @return DateBeginEnd[]
     */
    public function getDates(): array
    {
        $dates = [];
        $specs = $this->findByUrn(UrnList::DATE_OBJECT);
        foreach ($specs as $spec) {
            foreach ($spec->spec as $data) {
                if ($data->urn == UrnList::DATE_DEB->value) {
                    $dateBegin = $data->value;
                }
                if ($data->urn == UrnList::DATE_DEB->value) {
                    $dateEnd = $data->value;
                }
            }
            $dates[] = new DateBeginEnd($dateBegin, $dateEnd);
        }
        $dates = SortUtils::sortDatesEvent($dates);

        return $dates;
    }
}
