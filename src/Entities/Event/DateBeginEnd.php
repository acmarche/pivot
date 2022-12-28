<?php

namespace AcMarche\Pivot\Entities\Event;

use AcMarche\Pivot\Utils\DateUtils;

class DateBeginEnd
{
    public \DateTimeInterface|null $date_begin = null;
    public \DateTimeInterface|null $date_end = null;
    public string $format = 'Y-m-d';

    public function __construct(string $date_begin, string $date_end)
    {
        $format = "d/m/Y";
        $this->date_begin = DateUtils::convertStringToDateTime($date_begin, $format);
        $this->date_end = DateUtils::convertStringToDateTime($date_end, $format);
    }

    public function __toString(): string
    {
        if ($this->date_begin) {
            return $this->date_begin->format('d--m-Y');
        }

        return "";
    }

    public function isPeriod(): bool
    {
        if ($this->date_begin->format($this->format) == $this->date_end->format($this->format)) {
            return false;
        }

        return true;
    }
}