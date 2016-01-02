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

class DimensionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Query\Dimensions
     */
    protected $instance;

    public function setUp()
    {
        \Webiny\Component\Mongo\Mongo::setConfig(new ConfigObject(['Driver' => 'Webiny\AnalyticsDb\Test\MongoDriverMock']));
        $this->instance = new Query\Dimensions(new Mongo('localhost', 'testDb'), 'browser', 100, DateHelper::today());
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Webiny\AnalyticsDb\Query\Dimensions', $this->instance);

        $pipeline = $this->instance->getPipeline();

        // test $match
        $ts = DateHelper::today();
        $defaultMatch = [
            'entity' => 'browser',
            'ts'     => ['$gte' => $ts[0], '$lte' => $ts[1]]
        ];
        $this->assertSame($defaultMatch, $pipeline[0]['$match']);

        // test $project
        $defaultProject = [
            'ts'        => 1,
            'entity'    => 1,
            'month'     => 1,
            'year'      => 1,
            'name'      => 1,
            'value'     => 1,
            'total'     => 1,
            'count.100' => 1
        ];
        $this->assertSame($defaultProject, $pipeline[1]['$project']);
    }

    public function testGetSetCollectionName()
    {
        $this->assertSame(AnalyticsDb::ADB_DIMS, $this->instance->getCollectionName());
    }

    public function testSetDimension()
    {
        $pipeline = $this->instance->getPipeline();
        $this->assertTrue(!isset($pipeline[0]['$match']['name']));
        $this->assertTrue(!isset($pipeline[0]['$match']['value']));

        $this->instance->setDimension('browser');
        $pipeline = $this->instance->getPipeline();
        $this->assertSame('browser', $pipeline[0]['$match']['name']);
        $this->assertTrue(!isset($pipeline[0]['$match']['value']));

        $this->instance->setDimension('browser', 'fireWolf');
        $pipeline = $this->instance->getPipeline();
        $this->assertSame('browser', $pipeline[0]['$match']['name']);
        $this->assertSame('fireWolf', $pipeline[0]['$match']['value']);
    }

    public function testGroups()
    {
        $defaultGroup = ['_id' => '$ts', 'totalCount' => ['$sum' => '$count.100']];
        $pipeline = $this->instance->getPipeline();
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);

        $this->instance->groupByDimensionName();
        $pipeline = $this->instance->getPipeline();
        $defaultGroup['_id'] = '$name';
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);

        $this->instance->groupByDimensionValue();
        $pipeline = $this->instance->getPipeline();
        $defaultGroup['_id'] = ['name' => '$name', 'value' => '$value'];
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);

        $this->instance->groupByEntityName();
        $pipeline = $this->instance->getPipeline();
        $defaultGroup['_id'] = '$entity';
        $this->assertSame($defaultGroup, $pipeline[2]['$group']);
    }
}