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

class LogDimensionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ld = new LogDimension('browser', 'fireWolf');
        $this->assertInstanceOf('Webiny\AnalyticsDb\LogDimension', $ld);
    }

    public function testSetGetIncrement()
    {
        $ld = new LogDimension('browser', 'fireWolf');
        $this->assertSame(1, $ld->getIncrement());

        $ld->setIncrement(22.33);
        $this->assertSame(22.33, $ld->getIncrement());
    }

    public function testGetName()
    {
        $ld = new LogDimension('browser', 'fireWolf');
        $this->assertSame('browser', $ld->getName());
    }

    public function testGetValue()
    {
        $ld = new LogDimension('browser', 'fireWolf');
        $this->assertSame('fireWolf', $ld->getValue());
    }
}