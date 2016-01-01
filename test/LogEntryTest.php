<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb\Test;


use Webiny\AnalyticsDb\LogDimension;
use Webiny\AnalyticsDb\LogEntry;

class LogEntryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $le = new LogEntry('page');
        $this->assertInstanceOf('Webiny\AnalyticsDb\LogEntry', $le);
    }

    public function testGetName()
    {
        $le = new LogEntry('page');
        $this->assertSame('page', $le->getName());
    }

    public function testSetGetIncrement()
    {
        $le = new LogEntry('page');
        $this->assertSame(1, $le->getIncrement());

        $le->setIncrement(22.33);
        $this->assertSame(22.33, $le->getIncrement());
    }

    public function testSetGetRef()
    {
        $le = new LogEntry('page');
        $le->setRef('one');
        $this->assertSame('one', $le->getRef());

        $le->setRef(100);
        $this->assertSame(100, $le->getRef());
    }

    public function testAddGetDimension()
    {
        $le = new LogEntry('page');
        $this->assertSame([], $le->getDimensions());

        $le->addDimension('browser', 'fireWolf', 100);

        /**
         * @var $dim LogDimension
         */
        $dim = $le->getDimensions()[0];

        $this->assertInstanceOf('Webiny\AnalyticsDb\LogDimension', $dim);
        $this->assertSame('browser', $dim->getName());
        $this->assertSame('fireWolf', $dim->getValue());
        $this->assertSame(100, $dim->getIncrement());
    }
}