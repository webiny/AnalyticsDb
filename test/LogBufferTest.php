<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb\Test;

use Webiny\AnalyticsDb\LogBuffer;
use Webiny\AnalyticsDb\LogEntry;

class LogBufferTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $lb = new LogBuffer();
        $this->assertInstanceOf('Webiny\AnalyticsDb\LogBuffer', $lb);
    }

    public function testGetEntriesAddEntry()
    {
        $lb = new LogBuffer();

        $this->assertTrue(is_array($lb->getEntries()));
        $this->assertSame(0, count($lb->getEntries()));

        $lb->addEntry(new LogEntry('test'));
        $this->assertSame(1, count($lb->getEntries()));
        $this->assertInstanceOf('Webiny\AnalyticsDb\LogEntry', $lb->getEntries()[0]);
    }
}