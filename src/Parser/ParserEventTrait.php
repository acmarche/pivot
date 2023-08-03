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
     * @param bool $removeObsolete
     * @return void
     */
    public function parseDatesEvent(Offre $offre): void
    {
        if ($offre->typeOffre->idTypeOffre !== UrnTypeList::evenement()->typeId) {
            return;
        }

        $dates = [];
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

        $offre->dates = $dates;
    }

    public function dateBeginAndEnd2222(Offre $offre): ?DateBeginEnd
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
}
