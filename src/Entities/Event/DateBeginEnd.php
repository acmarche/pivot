<?php

namespace AcMarche\Pivot\Entities\Event;

use AcMarche\Pivot\Utils\DateUtils;

class DateBeginEnd implements \Stringable
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
        if ($this->date_begin instanceof \DateTimeInterface) {
            return $this->date_begin->format('d--m-Y');
        }

        return "";
    }

    public function isPeriod(): bool
    {
        return $this->date_begin instanceof \DateTimeInterface;
    }
}