<?php

namespace AcMarche\Pivot\Utils;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTimeInterface;

class DateUtils
{
    public static function convertStringToDateTime(string $dateString, string $format = "d/m/Y"): DateTimeInterface
    {
        return Carbon::createFromFormat($format, $dateString)->toDateTime();
    }

    /**
     * @return array<int, DateTimeInterface>
     */
    public static function getPeriodBetweenDates(
        DateTimeInterface $dateBegin,
        DateTimeInterface $dateEnd,
    ): array {
        $period = CarbonPeriod::create($dateBegin, $dateEnd);

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->toDateTime();
        }

        return $dates;
    }
}
