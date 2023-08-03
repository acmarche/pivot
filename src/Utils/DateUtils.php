<?php

namespace AcMarche\Pivot\Utils;

use DateTimeInterface;
use DateTime;
class DateUtils
{
    public static function convertStringToDateTime(string $dateString, string $format): DateTimeInterface|bool
    {
        return DateTime::createFromFormat($format, $dateString);
    }
}
