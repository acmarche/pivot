<?php

namespace AcMarche\Pivot\Entities\Event;

use DateTimeInterface;

trait EventTrait
{
    public ?DateTimeInterface $dateBegin = null;
    public ?DateTimeInterface $dateEnd = null;
    /**
     * ShortCut dateEndEvent ['year','month','day']
     * @var array
     */
    public array $dateEvent = [];

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