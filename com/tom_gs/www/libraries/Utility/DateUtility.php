<?php
/**
 * Date Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility;

final class DateUtility
{
    private static $DAY_OF_MONTH = array(
        1 => 31,
        2 => 28,
        3 => 31,
        4 => 30,
        5 => 31,
        6 => 30,
        7 => 31,
        8 => 31,
        9 => 30,
        10 => 31,
        11 => 30,
        12 => 31,
    );

    /**
     * Convert to number format like 'YYYYMMDD'
     * 
     * @param $date
     * @return String
     */
    public static function convertToNumberFormat($date)
    {
        return preg_replace('/[^\d]/i', '', $date);
    }

    /**
     * Get days of month
     * 
     * @param $year
     * @param $month
     * @return Integer
     */
    public static function getDayOfMonth($year, $month)
    {
        $day_of_month = $leap_offset = 0;
        if ($month == 2) {
            $leap_offset = self::isLeap($year);
        }
        if (isset(self::$DAY_OF_MONTH[$month])) {
            $day_of_month = self::$DAY_OF_MONTH[$month];
        }
        return $day_of_month + $leap_offset;
    }

    /**
     * Get the next date of given date
     * 
     * @param $basedate
     * @return String
     */
    public static function getNextDate($basedate)
    {
        //list($year, $month, $day) = self::splitDateFormat($basedate);
        list($year, $month) = self::splitDateFormat($basedate);
        $nextdate = '';
        if (preg_match('/1231$/', $basedate)) {
            $nextdate = (intval($year) + 1) . '0101';
        } elseif (preg_match('/' . self::getDayOfMonth($year, $month) . '$/', $basedate)) {
            $nextdate = $year . sprintf('%02d', (intval($month) + 1)) . '01';
        } else {
            $nextdate = ++$basedate;
        }
        return self::splitDateFormat('' . $nextdate);
    }

    /**
     * Judge leap year
     * 
     * @param $year
     * @return Integer 0 or 1
     */
    public static function isLeap($year)
    {
        // Use date() function
        // $timestamp = strtotime($year . '-01-01 00:00:00');
        // return date('L', $timestamp);

        // Use algorithm
        return ($year % 400 == 0 || $year % 4 == 0 && $year % 100 != 0) ? 1 : 0;
    }

    /**
     * Split 8-digit date format to 4-digit year, 2-digit month nad day
     * 
     * @param $date
     * @return mixed array($year, $month, $day)
     */
    public static function splitDateFormat($date)
    {
        $date = self::convertToNumberFormat($date);
        $year  = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day   = substr($date, 6, 2);
        return array($year, $month, $day);
    }

    /**
     * Create dates from start_date to end_date
     * 
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public static function createDatesOfTerm($start_date, $end_date)
    {
        $start_date = self::convertToNumberFormat($start_date);
        $end_date   = self::convertToNumberFormat($end_date);

        $result = array();
        $idx = intval($start_date);
        while (intval($idx) <= intval($end_date)) {
            $result[] = '' . $idx;
            list($year, $month, $day) = self::getNextDate($idx);
            $idx = $year . $month . $day;
        }
        return $result;
    }
}
