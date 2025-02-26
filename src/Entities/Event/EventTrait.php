<?php

namespace AcMarche\Pivot\Entities\Event;

use DateTimeInterface;

trait EventTrait
{
    /**
     * ShortCut dateEndEvent ['year','month','day']
     * @var array<int,string>
     */
    public array $shortCutDateEvent = [];

    /**
     * @var array<int, DateTimeInterface>
     */
    public array $datesEvent = [];

    /**
     * @var array<int, DateEvent>
     */
    public array $datesDetails = [];

    public function firstDate(): ?DateTimeInterface
    {
        if (count($this->datesEvent) > 0) {
            return $this->datesEvent[0];
        }

        return null;
    }
}
