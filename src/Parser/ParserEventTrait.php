<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Event\EventUtils;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;
use AcMarche\Pivot\Utils\SortUtils;

trait ParserEventTrait
{
    /**
     * Complète la class Event
     * Date de début, date de fin,...
     * @param Offre $offre
     * @param bool $removeObsolete
     * @return void
     */
    public function parseDatesEvent(Offre $offre, bool $removeObsolete = false): void
    {
        if ($offre->typeOffre->idTypeOffre !== UrnTypeList::evenement()->typeId) {
            return;
        }

        $offre->dates = $this->getDates($offre);

        $firstDate = $offre->firstDate();
        if ($firstDate) {
            $offre->dateBegin = $firstDate->date_begin;
            $offre->dateEnd = $firstDate->date_end;
        }

        if ($removeObsolete) {
            foreach ($offre->dates as $key => $dateBeginEnd) {
                if (EventUtils::isDateBeginEndObsolete($dateBeginEnd)) {
                    unset($offre->dates[$key]);
                }
            }
            $offre->dates = array_values($offre->dates);//reset index
            $firstDate = $offre->firstDate();
            if ($firstDate) {
                $offre->dateBegin = $firstDate->date_begin;
                $offre->dateEnd = $firstDate->date_end;
            }
        }
    }

    public function dateBeginAndEnd(Offre $offre): ?DateBeginEnd
    {
        $dateBegin = $dateEnd = null;
        $dateDebut = $this->findByUrn($offre, UrnList::DATE_DEB_VALID->value, returnData: true);

        if (count($dateDebut) > 0) {
            $dateBegin = $dateDebut[0]->value;
        }

        $dateFin = $this->findByUrn($offre, UrnList::DATE_FIN_VALID->value, returnData: true);
        if (count($dateFin) > 0) {
            $dateEnd = $dateFin[0]->value;
        }

        if ($dateBegin && $dateEnd) {
            return new DateBeginEnd($dateBegin, $dateEnd);
        }

        return null;
    }

    /**
     * @return DateBeginEnd[]
     */
    public function getDates(Offre $offre): array
    {
        $dates = [];
        if ($date = $this->dateBeginAndEnd($offre)) {
            $dates[] = $date;
        }
        $specs = $this->findByUrn($offre, UrnList::DATE_OBJECT->value, returnData: true);
        foreach ($specs as $spec) {
            foreach ($spec->spec as $data) {
                if ($data->urn == UrnList::DATE_DEB->value) {
                    $dateBegin = $data->value;
                }
                if ($data->urn == UrnList::DATE_END->value) {
                    $dateEnd = $data->value;
                }
            }
            $dates[] = new DateBeginEnd($dateBegin, $dateEnd);
        }

        return SortUtils::sortDatesEvent($dates);
    }
}
