<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Event\DateEvent;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Utils\DateUtils;
use AcMarche\Pivot\Utils\SortUtils;

trait ParserEventTrait
{
    /**
     * Va chercher toutes les dates
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
            $dateEvent = new DateEvent();
            foreach ($spec->spec as $specData) {
                if ($data = $this->getData($specData, UrnList::DATE_DEB->value)) {
                    $dateEvent->dateBegin = DateUtils::convertStringToDateTime($data);
                }
                if ($data = $this->getData($specData, UrnList::DATE_END->value)) {
                    $dateEvent->dateEnd = DateUtils::convertStringToDateTime($data);
                }
                if ($data = $this->getData($specData, UrnList::DATE_OUVERTURE_HEURE_1->value)) {
                    $dateEvent->ouvertureHeure1 = $data;
                }
                if ($data = $this->getData($specData, UrnList::DATE_FERMETURE_HEURE_1->value)) {
                    $dateEvent->fermetureHeure1 = $data;
                }
                if ($data = $this->getData($specData, UrnList::DATE_OUVERTURE_HEURE_2->value)) {
                    $dateEvent->ouvertureHeure2 = $data;
                }
                if ($data = $this->getData($specData, UrnList::DATE_FERMETURE_HEURE_2->value)) {
                    $dateEvent->fermetureHeure2 = $data;
                }
                if ($data = $this->getData($specData, UrnList::DATE_DETAIL_OUVERTURE->value)) {
                    $dateEvent->ouvertureDetails = $data;
                }
            }

            if ($dateEvent->dateBegin instanceof \DateTimeInterface && $dateEvent->dateEnd instanceof \DateTimeInterface) {
                if ($dateEvent->dateBegin->format('Y-m-d') === $dateEvent->dateEnd->format('Y-m-d')) {
                    $allDates[] = $dateEvent->dateBegin;
                } else {
                    foreach (DateUtils::getPeriodBetweenDates($dateEvent->dateBegin, $dateEvent->dateEnd) as $date) {
                        $allDates[] = $date;
                    }
                }
                $offre->datesDetails[] = $dateEvent;
            }
        }

        $allDates = SortUtils::sortDatesEvent($allDates);
        $offre->datesEvent = $allDates;
    }

    public function parseDatesValidation(Offre $offre): void
    {
        $format = "d/m/Y";
        $specData = $this->findByUrn($offre, UrnList::DATE_DEB_VALID->value, returnData: true);
        if (count($specData) > 0) {
            $offre->datedebvalid = DateUtils::convertStringToDateTime($specData[0]->value, $format);
        }
        $specData = $this->findByUrn($offre, UrnList::DATE_FIN_VALID->value, returnData: true);
        if (count($specData) > 0) {
            $offre->datefinvalid = DateUtils::convertStringToDateTime($specData[0]->value, $format);
        }
    }

    /**
     * Bug server www
     * @param array|SpecData $data
     * @param string $urn
     * @return string|null
     */
    private function getData(array|SpecData $data, string $urn): ?string
    {
        if (is_array($data)) {
            if ($data['urn'] === $urn) {
                return $data['value'];
            }
        } elseif ($data instanceof SpecData) {
            if ($data->urn === $urn) {
                return $data->value;
            }
        }

        return null;
    }
}
