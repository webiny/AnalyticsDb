<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb\Query;

use Webiny\AnalyticsDb\AnalyticsDb;
use Webiny\AnalyticsDb\AnalyticsDbException;
use Webiny\Component\Mongo\Mongo;

/**
 * Class QueryBuilder
 * @package Webiny\AnalyticsDb
 */
abstract class AbstractQuery
{
    /**
     * @var Mongo
     */
    protected $mongo;

    /**
     * @var string Entity name.
     */
    protected $entity;

    /**
     * @var string|int Referral value.
     */
    protected $ref;

    /**
     * @var array Date range.
     */
    protected $dateRange;

    /**
     * @var array Mongo pipeline array.
     */
    protected $pipeline;

    /**
     * @var string Name of the mongo collection.
     */
    protected $collection;

    /**
     * @var string Name of the current id field.
     */
    protected $id;


    /**
     * This is the callback method that is triggered after the constructor call.
     *
     * @return void
     */
    abstract protected function populate();

    /**
     * Base constructor.
     *
     * @param Mongo      $mongo
     * @param string     $entity
     * @param string|int $ref
     * @param array      $dateRange
     */
    public function __construct(Mongo $mongo, $entity, $ref, array $dateRange)
    {
        $this->mongo = $mongo;
        $this->entity = $entity;
        $this->ref = $ref;
        $this->dateRange = $dateRange;

        $this->populate();
    }

    /**
     * Set the result limit and offset.
     *
     * @param int      $limit How many results to return.
     * @param int|null $skip  How many results to skip.
     *
     * @return $this
     */
    public function limit($limit, $skip = null)
    {
        $this->pipeline['$limit'] = $limit;
        if (!empty($skip)) {
            $this->pipeline['$skip'] = $skip;
        }

        return $this;
    }

    /**
     * Returns the query for mongo shell. Use to debug your queries.
     *
     * @return string
     */
    public function getMongoQuery()
    {
        return 'db.' . $this->getCollectionName() . '.aggregate(' . json_encode($this->getPipeline()) . ');';
    }

    /**
     * This method does your query lookup and returns the result in form of an array.
     * In case if there are no records to return, false is returned.
     *
     * @return bool
     */
    public function getResult()
    {
        $result = $this->mongo->aggregate($this->getCollectionName(), $this->getPipeline());

        if (is_array($result) && isset($result['ok']) && $result['ok'] == 1) {
            return $result['result'];
        }

        return false;
    }

    /**
     * Sorts the result by timestamp.
     *
     * @param string $direction Mongo sort direction: 1 => ascending; -1 => descending
     *
     * @return $this
     * @throws AnalyticsDbException
     */
    public function sortByTimestamp($direction)
    {
        if ($this->id != '$ts') {
            throw new AnalyticsDbException('In order to sort by timestamp, you need to first group by timestamp.');
        }

        $direction = (int)$direction;
        $this->pipeline['$sort'] = ['_id' => $direction];

        return $this;
    }

    /**
     * Sorts the result by count.
     *
     * @param string $direction Mongo sort direction: 1 => ascending; -1 => descending
     *
     * @return $this
     */
    public function sortByCount($direction)
    {
        $direction = (int)$direction;
        $this->pipeline['$sort'] = ['totalCount' => $direction];

        return $this;
    }

    /**
     * Sorts the result by entity name.
     *
     * @param string $direction Mongo sort direction: 1 => ascending; -1 => descending
     *
     * @return $this
     * @throws AnalyticsDbException
     */
    public function sortByEntityName($direction)
    {
        if ($this->id != '$entity') {
            throw new AnalyticsDbException('In order to sort by entity name, you need to first group by entity name.');
        }

        $direction = (int)$direction;
        $this->pipeline['$sort'] = ['_id' => $direction];

        return $this;
    }

    /**
     * Sorts the result by referrer value.
     *
     * @param string $direction Mongo sort direction: 1 => ascending; -1 => descending
     *
     * @return $this
     * @throws AnalyticsDbException
     */
    public function sortByRef($direction)
    {
        if ($this->id != '$ref') {
            throw new AnalyticsDbException('In order to sort by ref, you need to first group by ref.');
        }

        $direction = (int)$direction;
        $this->pipeline['$sort'] = ['_id' => $direction];

        return $this;
    }

    /**
     * Groups the records by timestamp.
     *
     * @return $this
     */
    public function groupByTimestamp()
    {
        $this->pipeline['$group'] = ['_id' => '$ts', 'totalCount' => ['$sum' => '$count']];

        $this->id = '$ts';

        return $this;
    }

    /**
     * Groups the records by month.
     *
     * @return $this
     * @throws AnalyticsDbException
     */
    public function groupByMonth()
    {
        if ($this->getCollectionName() != AnalyticsDb::ADB_STATS_MONTHLY) {
            throw new AnalyticsDbException('You have to use monthly stats in order to group by month. To use the monthly stats, call the "monthly()" method prior to the groupByMonth method.');
        }

        $this->groupByTimestamp();

        return $this;
    }

    /**
     * Groups the records by entity name.
     *
     * @return $this
     */
    public function groupByEntityName()
    {
        $this->pipeline['$group'] = ['_id' => '$entity', 'totalCount' => ['$sum' => '$count']];

        $this->id = '$entity';

        return $this;
    }

    /**
     * Group stat records by ref id.
     *
     * @return $this
     */
    public function groupByRef()
    {
        $this->pipeline['$group'] = ['_id' => '$ref', 'totalCount' => ['$sum' => '$count']];

        $this->id = '$ref';

        return $this;
    }

    /**
     * Returns the pipeline array.
     *
     * @return array
     */
    public function getPipeline()
    {
        $pipeline = [];
        foreach ($this->pipeline as $k => $v) {
            $pipeline[] = [$k => $v];
        }

        return $pipeline;
    }

    /**
     * Returns the collection name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collection;
    }
}