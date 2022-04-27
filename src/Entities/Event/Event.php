<?php

namespace AcMarche\Pivot\Entities\Event;

use AcMarche\Pivot\Entities\Offre\Offre;
use DateTimeInterface;

class Event extends Offre
{
    public DateTimeInterface $dateBegin;
    public DateTimeInterface $dateEnd;

    /**
     * @var DateBeginEnd[]
     */
    public array $dates = [];

    public function firstDate(): ?DateBeginEnd
    {
        if (count($this->dates) > 0) {
            return $this->dates[array_key_first($this->dates)];
        }

        return null;
    }
}
