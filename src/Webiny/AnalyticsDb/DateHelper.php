<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

/**
 * Class DateHelper
 * @package Webiny\AnalyticsDb
 */
class DateHelper
{
    /**
     * Returns the date range for the current day.
     *
     * @return array
     */
    public static function today()
    {
        $today = strtotime(date('Y-m-d'));

        return [$today, $today];
    }

    /**
     * Returns a date range for the current week.
     *
     * @return array
     */
    public static function rangeThisWeek()
    {
        $ts = strtotime(date('Y-m-d')) + 86400;
        do {
            $ts -= 86400;
            $startDate = date('N', $ts);
        } while ($startDate != '1');

        return [$ts, $ts + (86400 * 6)];
    }

    /**
     * Returns a date range for the previous week.
     *
     * @return array
     */
    public static function rangeLastWeek()
    {
        $thisWeek = self::rangeThisWeek();

        return [$thisWeek[0] - (86400 * 7), $thisWeek[0] - 86400];
    }

    /**
     * Returns a date range for 2 previous weeks.
     *
     * @return array
     */
    public static function rangeLastTwoWeeks()
    {
        $thisWeek = self::rangeThisWeek();

        return [$thisWeek[0] - (86400 * 14), $thisWeek[0] - 86400];
    }

    /**
     * Returns a date range for the current month.
     *
     * @return array
     */
    public static function rangeThisMonth()
    {
        $currentMonth = date('Y-m-01', time());
        $currentMonthTs = self::toTs($currentMonth);

        return [$currentMonthTs, $currentMonthTs + (86400 * (date('t') - 1))];
    }

    /**
     * Returns a date range for the previous month.
     *
     * @return array
     */
    public static function rangeLastMonth()
    {
        $lastMonth = date('m') - 1;
        if ($lastMonth <= 0) {
            $lastMonth = 12;
        }
        $lastMonth = date('Y') . '-' . $lastMonth . '-01';

        return [self::toTs($lastMonth), self::toTs($lastMonth) + (86400 * (date('t', strtotime($lastMonth))-1))];
    }

    /**
     * Returns a date range for the last 7 days.
     *
     * @return array
     */
    public static function rangeLast7Days()
    {
        $today = self::today()[0];
        return [$today - (86400 * 7), $today];
    }

    /**
     * Returns a date range for the last 14 days.
     *
     * @return array
     */
    public static function rangeLast14Days()
    {
        $today = self::today()[0];
        return [$today - (86400 * 14), $today];
    }

    /**
     * Returns a date range for the last 30 days.
     *
     * @return array
     */
    public static function rangeLast30Days()
    {
        $today = self::today()[0];
        return [$today - (86400 * 30), $today];
    }

    /**
     * Returns a date range for the last 60 days.
     *
     * @return array
     */
    public static function rangeLast60Days()
    {
        $today = self::today()[0];
        return [$today - (86400 * 60), $today];
    }

    /**
     * Returns a date range for the last 90 days.
     *
     * @return array
     */
    public static function rangeLast90Days()
    {
        $today = self::today()[0];
        return [$today - (86400 * 90), $today];
    }

    /**
     * Returns a date range for quarter 1 of the given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeQ1($year = null)
    {
        $year = empty($year) ? date('Y') : $year;
        $start = $year . '-01-01';
        $end = $year . '-03-31';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Returns a date range for quarter 2 of the given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeQ2($year = null)
    {
        $year = empty($year) ? date('Y') : $year;
        $start = $year . '-04-01';
        $end = $year . '-06-30';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Returns a date range for quarter 3 of the given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeQ3($year = null)
    {
        $year = empty($year) ? date('Y') : $year;
        $start = $year . '-07-01';
        $end = $year . '-09-30';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Returns a date range for quarter 4 of the given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeQ4($year = null)
    {
        $year = empty($year) ? date('Y') : $year;
        $start = $year . '-10-01';
        $end = $year . '-12-31';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Returns a date range for first half of the given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeH1($year = null)
    {
        $year = empty($year) ? date('Y') : $year;
        $start = $year . '-01-01';
        $end = $year . '-06-30';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Returns a date range for second half of the given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeH2($year = null)
    {
        $year = empty($year) ? date('Y') : $year;
        $start = $year . '-07-01';
        $end = $year . '-12-31';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Returns a date range for a given year.
     * Note: if the year is not give, the current year is used.
     *
     * @param int $year
     *
     * @return array
     */
    public static function rangeYear($year = null)
    {
        $year = empty($year) ? date('Y') : $year;

        $start = $year . '-01-01';
        $end = $year . '-12-31';

        return [self::toTs($start), self::toTs($end)];
    }

    /**
     * Converts the given date to timestamp.
     *
     * @param string|int $date
     *
     * @return int
     */
    private static function toTs($date)
    {
        return strtotime($date);
    }
}