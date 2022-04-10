<?php

namespace AcMarche\Pivot\Event;

use AcMarche\Pivot\Utils\DateUtils;

class DateBeginEnd
{
    public \DateTimeInterface|null $date_begin = null;
    public \DateTimeInterface|null $date_end = null;

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
}