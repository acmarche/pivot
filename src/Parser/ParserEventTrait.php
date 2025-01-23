<?php

namespace AcMarche\Pivot\Parser;

use AcMarche\Pivot\Entities\Event\DateBeginEnd;
use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Spec\UrnList;
use AcMarche\Pivot\Spec\UrnTypeList;

trait ParserEventTrait
{
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

        $dates = [];
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
                $dates[] = new DateBeginEnd($dateBegin, $dateEnd);
            }
        }

        $offre->dates = $dates;
    }
}
