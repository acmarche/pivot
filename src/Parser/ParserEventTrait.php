<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Utils\DateUtils;
use AcMarche\Pivot\Utils\SortUtils;

trait ParserEventTrait
{
    //urn:cat:accueil:datemanif todo

    /**
     * Complète la class Event
     * Date de début, date de fin,...
     * @param Offre $offre
     * @return void
     */
    public function parseDatesEvent(Offre $offre): void
    {
        if ($offre->typeOffre->idTypeOffre !== UrnTypeList::evenement()->typeId) {
            return;
        }

        $allDates = [];
        $specs = $this->findByUrn($offre, UrnList::DATE_OBJECT->value, returnData: true);
        foreach ($specs as $spec) {
            $dateBegin = null;
            $dateEnd = null;
            foreach ($spec->spec as $data) {
                if (is_array($data)) {
                    if ($data['urn'] == UrnList::DATE_DEB->value) {
                        $dateBegin = $data['value'];
                    }
                    if ($data['urn'] == UrnList::DATE_END->value) {
                        $dateEnd = $data['value'];
                    }
                } elseif ($data instanceof SpecData) {
                    if ($data->urn == UrnList::DATE_DEB->value) {
                        $dateBegin = $data->value;
                    }
                    if ($data->urn == UrnList::DATE_END->value) {
                        $dateEnd = $data->value;
                    }
                }
            }
            if ($dateBegin && $dateEnd) {
                if ($dateBegin === $dateEnd) {
                    $allDates[] = DateUtils::convertStringToDateTime($dateBegin);
                } else {
                    foreach (DateUtils::getPeriodBetweenDates($dateBegin, $dateEnd) as $date) {
                        $allDates[] = $date;
                    }
                }
            }
        }

        $allDates = SortUtils::sortDatesEvent($allDates);
        $offre->datesEvent = $allDates;
    }

    public function parseDatesValidation(Offre $offre): void
    {
        $format = "d/m/Y";
        $specData = $this->findByUrn($offre, UrnList::DATE_DEB_VALID->value, returnData: true);
        $offre->datedebvalid = DateUtils::convertStringToDateTime($specData[0]->value, $format);

        $specData = $this->findByUrn($offre, UrnList::DATE_FIN_VALID->value, returnData: true);
        $offre->datefinvalid = DateUtils::convertStringToDateTime($specData[0]->value, $format);
    }
}
