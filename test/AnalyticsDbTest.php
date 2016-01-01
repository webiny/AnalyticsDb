<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb\Test;

use Webiny\AnalyticsDb\AnalyticsDb;
use Webiny\AnalyticsDb\DateHelper;
use Webiny\Component\Config\ConfigObject;
use Webiny\Component\Mongo\Mongo;

require_once __DIR__ . '/MongoDriverMock.php';

class AnalyticsDbTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AnalyticsDb
     */
    protected $instance;

    public function setUp()
    {
        \Webiny\Component\Mongo\Mongo::setConfig(new ConfigObject(['Driver' => 'Webiny\AnalyticsDb\Test\MongoDriverMock']));
        $this->instance = new AnalyticsDb(new Mongo('localhost', 'testDb'));
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\AnalyticsDb', $this->instance);
    }

    public function testSetGetTimestamp()
    {
        $this->assertSame(strtotime(date('Y-m-d')), $this->instance->getTimestamp()['ts']);
        $this->assertSame(date('n'), $this->instance->getTimestamp()['month']);
        $this->assertSame(date('Y'), $this->instance->getTimestamp()['year']);
        $this->assertSame(strtotime(date('Y-m-01')), $this->instance->getTimestamp()['monthTs']);

        $ts = time() - (86400 * 90);
        $this->instance->setTimestamp($ts);

        $this->assertSame(strtotime(date('Y-m-d', $ts)), $this->instance->getTimestamp()['ts']);
        $this->assertSame(date('n', $ts), $this->instance->getTimestamp()['month']);
        $this->assertSame(date('Y', $ts), $this->instance->getTimestamp()['year']);
        $this->assertSame(strtotime(date('Y-m-01', $ts)), $this->instance->getTimestamp()['monthTs']);
    }

    public function testLog()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\LogBuffer', $this->instance->getLogBuffer());
        $this->assertSame([], $this->instance->getLogBuffer()->getEntries());

        $this->instance->log('browser', 100, 10);
        $this->assertSame(1, count($this->instance->getLogBuffer()->getEntries()));

        $this->instance->log('browser', 101);
        $this->assertSame(2, count($this->instance->getLogBuffer()->getEntries()));

        $log = $this->instance->getLogBuffer()->getEntries()[0];
        $this->assertInstanceOf('Webiny\AnalyticsDb\LogEntry', $log);

        $this->assertSame('browser', $log->getName());
        $this->assertSame(100, $log->getRef());
        $this->assertSame(10, $log->getIncrement());

        $this->instance->save();
        $this->assertSame([], $this->instance->getLogBuffer()->getEntries());
    }

    public function testQuery()
    {
        $query = $this->instance->query('browser', 10, DateHelper::today());
        $this->assertInstanceOf('Webiny\AnalyticsDb\Query', $query);
    }
}