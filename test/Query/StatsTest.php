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
use Webiny\AnalyticsDb\Query;
use Webiny\Component\Config\ConfigObject;
use Webiny\Component\Mongo\Mongo;

require_once __DIR__ . '/../MongoDriverMock.php';

class StatsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Query\Stats
     */
    protected $instance;

    public function setUp()
    {
        \Webiny\Component\Mongo\Mongo::setConfig(new ConfigObject(['Driver' => 'Webiny\AnalyticsDb\Test\MongoDriverMock']));
        $this->instance = new Query\Stats(new Mongo('localhost', 'testDb'), 'browser', 100, DateHelper::today());
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\Query\Stats', $this->instance);

        $pipeline = $this->instance->getPipeline();

        // test $match
        $ts = DateHelper::today();
        $defaultMatch = [
            'entity' => 'browser',
            'ref'    => 100,
            'ts'     => ['$gte' => $ts[0], '$lte' => $ts[1]]
        ];
        $this->assertSame($defaultMatch, $pipeline[0]['$match']);

        // test $project
        $defaultProject = [
            'count'  => 1,
            'ref'    => 1,
            'ts'     => 1,
            'entity' => 1,
            'month'  => 1,
            'year'   => 1
        ];
        $this->assertSame($defaultProject, $pipeline[1]['$project']);
    }

    public function testGetSetCollectionName()
    {
        $this->assertSame(AnalyticsDb::ADB_STATS_DAILY, $this->instance->getCollectionName());

        $this->instance->monthly();
        $this->assertSame(AnalyticsDb::ADB_STATS_MONTHLY, $this->instance->getCollectionName());

        $this->instance->daily();
        $this->assertSame(AnalyticsDb::ADB_STATS_DAILY, $this->instance->getCollectionName());
    }

    public function testLimit()
    {
        $pipeline = $this->instance->getPipeline();
        $this->assertTrue(!isset($pipeline[4]['$limit']));
        $this->assertTrue(!isset($pipeline[5]['$skip']));

        $this->instance->limit(10);
        $pipeline = $this->instance->getPipeline();
        $this->assertSame(10, $pipeline[4]['$limit']);
        $this->assertTrue(!isset($pipeline[5]['$skip']));

        $this->instance->limit(10, 20);
        $pipeline = $this->instance->getPipeline();
        $this->assertSame(10, $pipeline[4]['$limit']);
        $this->assertSame(20, $pipeline[5]['$skip']);
    }

    public function testSorters()
    {
        $pipeline = $this->instance->getPipeline();
        $this->assertSame(1, $pipeline[3]['$sort']['ts']);

        $this->instance->sortByCount(-1);
        $pipeline = $this->instance->getPipeline();
        $this->assertSame(-1, $pipeline[3]['$sort']['totalCount']);

        $this->instance->sortByTimestamp(-1);
        $pipeline = $this->instance->getPipeline();
        $this->assertSame(-1, $pipeline[3]['$sort']['ts']);

        $this->instance->sortByEntityName(1);
        $pipeline = $this->instance->getPipeline();
        $this->assertSame(1, $pipeline[3]['$sort']['entity']);
    }

    public function testGroups()
    {
        $defaultGroup = ['_id' => '$ts', 'totalCount' => ['$sum' => '$count']];
        $pipeline = $this->instance->getPipeline();
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);

        $this->instance->monthly();
        $this->instance->groupByMonth();
        $pipeline = $this->instance->getPipeline();
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);
        $this->assertSame(AnalyticsDb::ADB_STATS_MONTHLY, $this->instance->getCollectionName());

        $this->instance->daily();
        $this->instance->groupByEntityName();
        $pipeline = $this->instance->getPipeline();
        $defaultGroup['_id'] = '$entity';
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);

    }
}