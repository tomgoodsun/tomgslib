<?php
/**
 * Date Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\librariesTest\Utility;

use \com\tom_gs\www\libraries\Utility\DateUtility;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../include.php';

class DateUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Convert to number format like 'YYYYMMDD'
     * 
     * @param $date
     * @return String
     */
    public function testConvertToNumberFormat()
    {
        $test_data = array(
            array('2013-12-23 23:59:59',  '20131223235959'),
            array('2013.12.23 23"59\'59', '20131223235959'),
            array('2013/12/23 23:59:59',  '20131223235959'),
        );
        foreach ($test_data as $data) {
            $result = DateUtility::convertToNumberFormat($data[0]);
            $this->assertEquals($data[1], $result);
        }
    }

    /**
     * Get days of month
     * 
     * @param $year
     * @param $month
     * @return Integer
     */
    public function testGetDayOfMonth()
    {
        $test_data = array(
            array(1985, 1, 31),
            array(1985, 2, 28),
            array(2000, 2, 29),
            array(2012, 2, 29),
            array(1985, 3, 31),
            array(1985, 4, 30),
            array(1985, 5, 31),
            array(1985, 6, 30),
            array(1985, 7, 31),
            array(1985, 8, 31),
            array(1985, 9, 30),
            array(1985, 10, 31), 
            array(1985, 11, 30),
            array(1985, 12, 31),
        );
        foreach ($test_data as $data) {
            $result = DateUtility::getDayOfMonth($data[0], $data[1]);
            $this->assertEquals($data[2], $result);
        }
    }

    /**
     * Get the next date of given date
     * 
     * @param $basedate
     * @return String
     */
    public function testGetNextDate()
    {
        $test_data = array(
            array('20130101', array('2013', '01', '02')),
            array('20130102', array('2013', '01', '03')),
            array('20130103', array('2013', '01', '04')),
            array('20130104', array('2013', '01', '05')),
            array('20130131', array('2013', '02', '01')),
            array('20130228', array('2013', '03', '01')),
            array('20130331', array('2013', '04', '01')),
            array('20130430', array('2013', '05', '01')),
            array('20130531', array('2013', '06', '01')),
            array('20130630', array('2013', '07', '01')),
            array('20130731', array('2013', '08', '01')),
            array('20130831', array('2013', '09', '01')),
            array('20130930', array('2013', '10', '01')),
            array('20131031', array('2013', '11', '01')),
            array('20131130', array('2013', '12', '01')),
            array('20131231', array('2014', '01', '01')),
        );
        foreach ($test_data as $data) {
            $result = DateUtility::getNextDate($data[0]);
            $this->assertEquals($data[1], $result);
        }
    }

    /**
     * Judge leap year
     * 
     * @param $year
     * @return Integer 0 or 1
     */
    public function testIsLeap()
    {
        $test_data = array(
            array(2000, true), 
            array(2001, false), 
            array(2002, false), 
            array(2003, false), 
            array(2004, true), 
            array(2005, false), 
            array(2006, false), 
            array(2007, false), 
            array(2008, true), 
            array(2009, false), 
            array(2010, false), 
            array(2011, false), 
            array(2012, true), 
            array(2013, false), 
            array(2014, false), 
            array(2015, false), 
            array(2016, true), 
            array(2017, false), 
            array(2018, false), 
            array(2019, false), 
            array(2020, true), 
            array(2021, false), 
            array(2022, false), 
        );
        foreach ($test_data as $data) {
            $result = DateUtility::isLeap($data[0]);
            if ($data[1]) {
                //$this->assertTrue($result);
                $this->assertEquals(1, $result);
            } else {
                //$this->assertFalse($result);
                $this->assertEquals(0, $result);
            }
        }
    }

    /**
     * Split 8-digit date format to 4-digit year, 2-digit month nad day
     * 
     * @param $date
     * @return mixed array($year, $month, $day)
     */
    public function testSplitDateFormat()
    {
        $test_data = array(
            array('20131215', array('2013', '12', '15')),
        );
        foreach ($test_data as $data) {
            $result = DateUtility::splitDateFormat($data[0]);
            $this->assertEquals($data[1], $result);
        }
    }

    /**
     * Create dates from start_date to end_date
     * 
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function testCreateDatesOfTerm()
    {
        $test_data = array(
            array(
                '20130128',
                '20130203',
                array(
                    '20130128',
                    '20130129',
                    '20130130',
                    '20130131',
                    '20130201',
                    '20130202',
                    '20130203',
                )
            ),
            array(
                '20131228',
                '20140103',
                array(
                    '20131228',
                    '20131229',
                    '20131230',
                    '20131231',
                    '20140101',
                    '20140102',
                    '20140103',
                )
            ),
        );
        foreach ($test_data as $data) {
            $result = DateUtility::createDatesOfTerm($data[0], $data[1]);
            $this->assertEquals($data[2], $result);
        }
    }
}
