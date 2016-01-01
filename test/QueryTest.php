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
use Webiny\AnalyticsDb\Query;
use Webiny\Component\Config\ConfigObject;
use Webiny\Component\Mongo\Mongo;

require_once __DIR__ . '/MongoDriverMock.php';

class QueryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Query
     */
    protected $instance;

    public function setUp()
    {
        \Webiny\Component\Mongo\Mongo::setConfig(new ConfigObject(['Driver' => 'Webiny\AnalyticsDb\Test\MongoDriverMock']));
        $this->instance = new Query(new Mongo('localhost', 'testDb'), 'browser', 100, DateHelper::today());
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\Query', $this->instance);
    }

    public function testStats()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\Query\Stats', $this->instance->stats());
    }

    public function testQuery()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\Query\Dimensions', $this->instance->dimension());
    }
}