<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Utils\DateUtils;
use AcMarche\Pivot\Utils\SortUtils;

trait ParserEventTrait
{
    public function dateBeginAndEnd(): array
    {
        $dates = [];
        $format = "d/m/Y";
        $dateDebut = $this->findByUrn(UrnList::DATE_DEB_VALID->value);
        if (count($dateDebut) > 0) {
            $dates[] = DateUtils::convertStringToDateTime($dateDebut[0]->value, $format);
        }

        $dateFin = $this->findByUrn(UrnList::DATE_FIN_VALID->value);
        if (count($dateFin) > 0) {
            $dates[] = DateUtils::convertStringToDateTime($dateFin[0]->value, $format);
        }

        return $dates;
    }

    /**
     * @return DateBeginEnd[]
     */
    public function getDates(): array
    {
        $dates = [];
        $specs = $this->findByUrn(UrnList::DATE_OBJECT->value);
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
