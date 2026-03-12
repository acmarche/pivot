<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\DateEvent;

trait DateTrait
{
    /** @var DateEvent[] */
    public array $dates = [];

    public function addDate(DateEvent $date): void
    {
        $this->dates[] = $date;
    }

    public function getNextDate(): ?DateEvent
    {
        $now = new \DateTimeImmutable('today');

        foreach ($this->dates as $date) {
            if ($date->endDate !== null && $date->endDate >= $now) {
                return $date;
            }
            if ($date->startDate !== null && $date->startDate >= $now) {
                return $date;
            }
        }

        return null;
    }

    public function getClosestUpcomingDate(): ?DateEvent
    {
        $now = new \DateTimeImmutable('today');
        $closest = null;
        $closestStart = null;

        foreach ($this->dates as $date) {
            $start = $date->startDate;
            if ($start === null || $start < $now) {
                continue;
            }
            if ($closestStart === null || $start < $closestStart) {
                $closest = $date;
                $closestStart = $start;
            }
        }

        /**
         * Event like marche public as only one date, and the start date is not in the future
         * it's force to have a date
         */
        if ($closest === null && count($this->dates) > 0) {
            return $this->dates[0];
        }

        return $closest;
    }

    public function isOnPeriod(): bool
    {
        $now = new \DateTimeImmutable('today');

        foreach ($this->dates as $date) {
            if ($date->startDate !== null && $date->endDate !== null
                && $date->startDate <= $now && $date->endDate >= $now) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return DateEvent[]
     */
    public function getUpcomingDates(): array
    {
        $now = new \DateTimeImmutable('today');

        return array_values(
            array_filter(
                $this->dates,
                fn(DateEvent $date) => ($date->endDate !== null && $date->endDate >= $now)
                    || ($date->startDate !== null && $date->startDate >= $now),
            )
        );
    }

    /**
     * Returns true if the event has multiple date entries or a single date range (not a single day).
     */
    public function hasMultipleDates(): bool
    {
        if (count($this->dates) > 1) {
            return true;
        }

        if (count($this->dates) === 1) {
            return !$this->dates[0]->isSingleDay();
        }

        return false;
    }

    /**
     * @return array{year: ?string, month: ?string, day: ?string}
     */
    public function getNextDateParts(): array
    {
        $startDate = $this->getClosestUpcomingDate()?->startDate;
        if (count($this->dates) === 1) {
            if ($startDate?->format('Y-m-d') < new \DateTimeImmutable('today')->format('Y-m-d')) {
                $startDate = $this->getClosestUpcomingDate()?->endDate;
            }
        }

        return [
            'year' => $startDate?->format('Y'),
            'month' => $startDate?->format('m'),
            'day' => $startDate?->format('d'),
        ];
    }


}
