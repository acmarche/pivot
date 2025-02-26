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
     * @var array<int, DateEvent>
     */
    public array $datesEvent = [];

    public function firstDate(): ?DateTimeInterface
    {
        if (count($this->datesEvent) > 0) {
            return $this->datesEvent[0]->dateBegin;
        }

        return null;
    }

    public function isEventOnPeriod(): bool
    {
        foreach ($this->datesEvent as $date) {
            if (!$date->isSameDate()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return  array<int, DateTimeInterface>
     */
    public function allDatesAsDateTime(): array
    {
        return array_map(fn(DateEvent $dateEvent) => $dateEvent->dateBegin, $this->datesEvent);
    }
}
