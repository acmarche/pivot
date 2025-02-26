<?php

namespace AcMarche\Pivot\Utils;

use DateTimeInterface;
use DateTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DateUtils
{
    public static function convertStringToDateTime(string $dateString, string $format = "d/m/Y"): DateTimeInterface|bool
    {
        return Carbon::createFromFormat($format, $dateString);
    }

    /**
     * @return array<int, DateTimeInterface>
     */
    public static function getPeriodBetweenDates(string $dateBegin, string $dateEnd, string $format = 'd/m/Y'): array
    {
        $startDate = self::convertStringToDateTime($format, $dateBegin);
        $endDate =  self::convertStringToDateTime($format, $dateEnd);

        $period = CarbonPeriod::create($startDate, $endDate);

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->toDateTime();
        }

        return $dates;
    }
}
