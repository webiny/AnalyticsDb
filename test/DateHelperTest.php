<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb\Test;

use Webiny\AnalyticsDb\DateHelper;

class DateHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testToday()
    {
        $result = DateHelper::today();
        $this->assertTrue(is_array($result));

        $date = strtotime(date('Y-m-d'));
        $this->assertSame($date, $result[0]);
        $this->assertSame($date, $result[1]);
    }

    public function testRangeThisWeek()
    {
        $result = DateHelper::rangeThisWeek();
        $this->assertTrue(is_array($result));

        $this->assertSame('1', date('N', $result[0]));
        $this->assertSame('7', date('N', $result[1]));
        $this->assertSame(6, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] >= strtotime(date('Y-m-d')));
    }

    public function testRangeLastWeek()
    {
        $result = DateHelper::rangeLastWeek();
        $this->assertTrue(is_array($result));

        $this->assertSame('1', date('N', $result[0]));
        $this->assertSame('7', date('N', $result[1]));
        $this->assertSame(6, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] <= strtotime(date('Y-m-d')));
        $this->assertTrue(((strtotime(date('Y-m-d')) - $result[0]) / 86400) <= 14);
        $this->assertTrue(((strtotime(date('Y-m-d')) - $result[0]) / 86400) >= 7);
    }

    public function testRangeLastTwoWeeks()
    {
        $result = DateHelper::rangeLastTwoWeeks();
        $this->assertTrue(is_array($result));

        $this->assertSame('1', date('N', $result[0]));
        $this->assertSame('7', date('N', $result[1]));
        $this->assertSame(13, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] <= strtotime(date('Y-m-d')));
        $this->assertTrue(((strtotime(date('Y-m-d')) - $result[0]) / 86400) <= 21);
        $this->assertTrue(((strtotime(date('Y-m-d')) - $result[0]) / 86400) >= 14);
    }

    public function testRangeThisMonth()
    {
        $result = DateHelper::rangeThisMonth();
        $this->assertTrue(is_array($result));

        $this->assertSame('1', date('j', $result[0]));
        $this->assertSame(date('t'), date('d', $result[1]));
        $this->assertSame(date('t') - 1, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] >= strtotime(date('Y-m-d')));
    }

    public function testRangeLastMonth()
    {
        $result = DateHelper::rangeLastMonth();
        $this->assertTrue(is_array($result));

        $lastMonth = date('m') - 1;
        if ($lastMonth <= 0) {
            $lastMonth = 12;
        }
        $lastMonthTs = strtotime(date('Y') . '-' . $lastMonth . '-01');

        $this->assertSame('1', date('j', $result[0]));
        $this->assertSame(date('t', $lastMonthTs), date('d', $result[1]));
        $this->assertSame(date('t', $lastMonthTs) - 1, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d', $lastMonthTs)));
        $this->assertTrue($result[1] >= strtotime(date('Y-m-d', $lastMonthTs)));
    }

    public function testRangeLast7Days()
    {
        $result = DateHelper::rangeLast7Days();
        $this->assertTrue(is_array($result));

        $this->assertSame(7, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] == strtotime(date('Y-m-d')));
    }

    public function testRangeLast14Days()
    {
        $result = DateHelper::rangeLast14Days();
        $this->assertTrue(is_array($result));

        $this->assertSame(14, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] == strtotime(date('Y-m-d')));
    }

    public function testRangeLast30Days()
    {
        $result = DateHelper::rangeLast30Days();
        $this->assertTrue(is_array($result));

        $this->assertSame(30, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] == strtotime(date('Y-m-d')));
    }

    public function testRangeLast60Days()
    {
        $result = DateHelper::rangeLast60Days();
        $this->assertTrue(is_array($result));

        $this->assertSame(60, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] == strtotime(date('Y-m-d')));
    }

    public function testRangeLast90Days()
    {
        $result = DateHelper::rangeLast90Days();
        $this->assertTrue(is_array($result));

        $this->assertSame(90, ($result[1] - $result[0]) / 86400);
        $this->assertTrue($result[0] <= strtotime(date('Y-m-d')));
        $this->assertTrue($result[1] == strtotime(date('Y-m-d')));
    }

    public function testRangeQ1()
    {
        $result = DateHelper::rangeQ1();
        $this->assertTrue(is_array($result));

        $this->assertSame('01-01', date('m-d', $result[0]));
        $this->assertSame('03-31', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertLessThanOrEqual(90, (($result[1] - $result[0]) / 86400));
        $this->assertGreaterThanOrEqual(89, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeQ1(2014);
        $this->assertSame('01-01', date('m-d', $result[0]));
        $this->assertSame('03-31', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertLessThanOrEqual(90, (($result[1] - $result[0]) / 86400));
        $this->assertGreaterThanOrEqual(89, (($result[1] - $result[0]) / 86400));
    }

    public function testRangeQ2()
    {
        $result = DateHelper::rangeQ2();
        $this->assertTrue(is_array($result));

        $this->assertSame('04-01', date('m-d', $result[0]));
        $this->assertSame('06-30', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertSame(90, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeQ2(2014);
        $this->assertSame('04-01', date('m-d', $result[0]));
        $this->assertSame('06-30', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertSame(90, (($result[1] - $result[0]) / 86400));
    }

    public function testRangeQ3()
    {
        $result = DateHelper::rangeQ3();
        $this->assertTrue(is_array($result));

        $this->assertSame('07-01', date('m-d', $result[0]));
        $this->assertSame('09-30', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertSame(91, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeQ3(2014);
        $this->assertSame('07-01', date('m-d', $result[0]));
        $this->assertSame('09-30', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertSame(91, (($result[1] - $result[0]) / 86400));
    }

    public function testRangeQ4()
    {
        $result = DateHelper::rangeQ4();
        $this->assertTrue(is_array($result));

        $this->assertSame('10-01', date('m-d', $result[0]));
        $this->assertSame('12-31', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertSame(91, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeQ4(2014);
        $this->assertSame('10-01', date('m-d', $result[0]));
        $this->assertSame('12-31', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertSame(91, (($result[1] - $result[0]) / 86400));
    }

    public function testRangeH1()
    {
        $result = DateHelper::rangeH1();
        $this->assertTrue(is_array($result));

        $this->assertSame('01-01', date('m-d', $result[0]));
        $this->assertSame('06-30', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertLessThanOrEqual(181, (($result[1] - $result[0]) / 86400));
        $this->assertGreaterThanOrEqual(179, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeH1(2014);
        $this->assertSame('01-01', date('m-d', $result[0]));
        $this->assertSame('06-30', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertLessThanOrEqual(181, (($result[1] - $result[0]) / 86400));
        $this->assertGreaterThanOrEqual(179, (($result[1] - $result[0]) / 86400));
    }

    public function testRangeH2()
    {
        $result = DateHelper::rangeH2();
        $this->assertTrue(is_array($result));

        $this->assertSame('07-01', date('m-d', $result[0]));
        $this->assertSame('12-31', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertSame(183, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeH2(2014);
        $this->assertSame('07-01', date('m-d', $result[0]));
        $this->assertSame('12-31', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertSame(183, (($result[1] - $result[0]) / 86400));
    }

    public function testRangeYear()
    {
        $result = DateHelper::rangeYear();
        $this->assertTrue(is_array($result));

        $this->assertSame('01-01', date('m-d', $result[0]));
        $this->assertSame('12-31', date('m-d', $result[1]));
        $this->assertSame(date('Y'), date('Y', $result[1]));
        $this->assertLessThanOrEqual(365, (($result[1] - $result[0]) / 86400));
        $this->assertGreaterThanOrEqual(364, (($result[1] - $result[0]) / 86400));

        $result = DateHelper::rangeYear(2014);
        $this->assertSame('01-01', date('m-d', $result[0]));
        $this->assertSame('12-31', date('m-d', $result[1]));
        $this->assertSame('2014', date('Y', $result[1]));
        $this->assertLessThanOrEqual(365, (($result[1] - $result[0]) / 86400));
        $this->assertGreaterThanOrEqual(364, (($result[1] - $result[0]) / 86400));
    }
}