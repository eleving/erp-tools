<?php

declare(strict_types=1);

namespace Common\Tool;

class DateTool
{

    public static function convertToDate(\DateTimeInterface $d): \DateTime
    {
        return (new \DateTime)->createFromFormat('Ymd', $d->format('Ymd'))->setTime(0, 0, 0);
    }

    public static function greaterDate(\DateTimeInterface $d1, \DateTimeInterface $d2): bool
    {
        return self::convertToDate($d1) > self::convertToDate($d2);
    }

    public static function smallerDate(\DateTimeInterface $d1, \DateTimeInterface $d2): bool
    {
        return self::convertToDate($d1) < self::convertToDate($d2);
    }

    public static function sameDate(\DateTimeInterface $d1, \DateTimeInterface $d2): bool
    {
        return self::convertToDate($d1) == self::convertToDate($d2);
    }

    public static function greaterOrSameDate(\DateTimeInterface $d1, \DateTimeInterface $d2): bool
    {
        return self::convertToDate($d1) >= self::convertToDate($d2);
    }

    public static function smallerOrSameDate(?\DateTimeInterface $d1, ?\DateTimeInterface $d2): bool
    {
        if(null === $d1) {
            return true;
        }

        if(null === $d2) {
            return false;
        }

        return self::convertToDate($d1) <= self::convertToDate($d2);
    }

    public static function inDateRange(\DateTimeInterface $date, \DateTimeInterface $periodFrom, \DateTimeInterface $periodTo): bool
    {
        return self::greaterOrSameDate($date, $periodFrom) && self::smallerOrSameDate($date, $periodTo);
    }

    /**
     * http://stackoverflow.com/questions/14202687/how-can-i-find-overlapping-dateperiods-date-ranges-in-php
     * @return int
     */
    public static function datesOverlap($start_one, $end_one, $start_two, $end_two): int
    {
        if ($start_one <= $end_two && $end_one >= $start_two) { //If the dates overlap
            return min($end_one, $end_two)->diff(max($start_two, $start_one))->days + 1; //return how many days overlap
        }

        return 0; //Return 0 if there is no overlap
    }

    /**
     * Add months _without overlapping_
     * 31/01 + 1 month = 28/02
     *
     * @param \DateTimeInterface $date
     * @param int $months
     * @return \DateTimeInterface
     */
    public static function addMonths(\DateTime $date, int $months): \DateTime
    {
        $startDay = $date->format('j');

        $date->modify("+{$months} month");

        $endDay = $date->format('j');

        if ($startDay != $endDay) {
            $date->modify('last day of last month');
        }

        return $date;
    }

    public static function addDays(\DateTime $date, $days): \DateTime
    {
        $date->modify(sprintf('+%d day%s', $days, $days > 1 ? 's' : ''));

        return $date;
    }

    public static function removeDays(\DateTime $date, $days): \DateTime
    {
        $date->modify(sprintf('-%d day%s', $days, $days > 1 ? 's' : ''));

        return $date;
    }

    public static function currentDate(): \DateTime
    {
        return new \DateTime('today');
    }

    public static function getDaysBetweenDates(\DateTimeInterface $d1, \DateTimeInterface $d2)
    {
        $d1 = self::convertToDate($d1);
        $d2 = self::convertToDate($d2);

        return $d1->diff($d2)->days;
    }

    public static function toText(\DateTimeInterface $date, ?string $country = null): string
    {
        return $date->format('F jS Y');
    }

    public static function valid($data): bool
    {
        return $data instanceof \DateTimeInterface;
    }

    public static function yesterdayDate(): \DateTime
    {
        $yesterday = (new \DateTime)->modify('yesterday');

        return self::convertToDate($yesterday);
    }
}
