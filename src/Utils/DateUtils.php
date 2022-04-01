<?php

namespace AcMarche\Pivot\Utils;

class DateUtils
{
    public static function convertStringToDateTime(string $dateString, string $format): \DateTimeInterface|bool
    {
        return \DateTime::createFromFormat($format, $dateString);
    }
}