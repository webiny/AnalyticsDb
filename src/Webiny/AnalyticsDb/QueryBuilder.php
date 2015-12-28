<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

use Webiny\Component\Mongo\Mongo;

/**
 * Class QueryBuilder
 * @package Webiny\AnalyticsDb
 */
class QueryBuilder
{
    const DIM_GROUPBY_VALUE = 'value';
    const DIM_GROUPBY_NAME = 'name';
    const DIM_GROUPBY_TS = 'ts';

    const STATS_GROUPBY_DAY = 'days';
    const STATS_GROUPBY_MONTH = 'month';

    /**
     * @var Mongo
     */
    private $mongo;

    /**
     * @var string Entity name.
     */
    private $entity;

    /**
     * @var string|int Referral value.
     */
    private $ref;

    /**
     * @var array Date range.
     */
    private $dateRange;

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
    }

    /**
     * Returns the stats for the given query.
     * By default stats are grouped by day, but you can change the grouping option to "month".
     *
     * @param null|string $groupBy Can be either "day", or "month".
     *
     * @return array
     * @throws AnalyticsDbException
     */
    public function getStats($groupBy = null)
    {
        if (empty($groupBy)) {
            $groupBy = self::STATS_GROUPBY_DAY;
        } elseif ($groupBy != self::STATS_GROUPBY_DAY && $groupBy != self::STATS_GROUPBY_MONTH) {
            throw new AnalyticsDbException('Invalid group by value. You can group by "day" or "month".');
        }

        // $match
        $match['name'] = $this->entity;
        $match['ref'] = $this->ref;
        $match['ts'] = ['$gte' => $this->dateRange[0], '$lte' => $this->dateRange[1]];

        // $project
        $project = ['count' => 1, 'ref' => 1];
        $project['ts'] = 1;

        // $group
        $group = ['_id' => '$ts', 'totalCount' => ['$sum' => '$count']];

        // $sort
        $sort = ['ts' => 1];

        $pipeline = [
            ['$match' => $match],
            ['$project' => $project],
            ['$group' => $group],
            ['$sort' => $sort],
        ];


        // collection
        $collection = AnalyticsDb::ADB_STATS_DAILY;
        if ($groupBy != self::STATS_GROUPBY_DAY) {
            $collection = AnalyticsDb::ADB_STATS_MONTHLY;
        }

        $result = $this->mongo->aggregate($collection, $pipeline);

        if (is_array($result) && isset($result['ok']) && $result['ok'] == 1) {
            return $result['result'];
        }

        return false;
    }

    /**
     * Returns a dimension analytics for the current query.
     *
     * @param null   $dimension
     * @param null   $value
     * @param string $groupBy Possible grouping options: "ts", "value" or "name"
     *
     * @return bool
     * @throws AnalyticsDbException
     */
    public function getDimension($dimension = null, $value = null, $groupBy = null)
    {
        if (empty($groupBy)) {
            $groupBy = self::DIM_GROUPBY_TS;
        } else if ($groupBy != self::DIM_GROUPBY_TS && $groupBy != self::DIM_GROUPBY_VALUE && $groupBy != self::DIM_GROUPBY_NAME) {
            throw new AnalyticsDbException('Invalid group by value. You can group by "ts", "value" or "name".');
        }

        // $match
        $match['entity'] = $this->entity;
        if (!empty($dimension)) {
            $match['name'] = $dimension;
        }
        $match['ts'] = ['$gte' => $this->dateRange[0], '$lte' => $this->dateRange[1]];
        if (!empty($value)) {
            $match['value'] = $value;
        }

        // $project
        $project = ['total' => 1, 'ts' => 1];
        if ($groupBy != 'total' && $groupBy != 'ts') {
            $project[$groupBy] = 1;
        }

        // $group
        $group = ['_id' => '$' . $groupBy, 'totalCount' => ['$sum' => '$total']];

        // $sort
        $sort = ['ts' => 1];

        $pipeline = [
            ['$match' => $match],
            ['$project' => $project],
            ['$group' => $group],
            ['$sort' => $sort],
        ];

        $result = $this->mongo->aggregate(AnalyticsDb::ADB_DIMS, $pipeline);

        if (is_array($result) && isset($result['ok']) && $result['ok'] == 1) {
            return $result['result'];
        }

        return false;
    }

}