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

/**
 * Class QueryBuilder
 * @package Webiny\AnalyticsDb
 */
class Stats extends AbstractQuery
{
    /**
     * Sets the lookup to the daily collection.
     *
     * @return $this
     */
    public function daily()
    {
        $this->collection = AnalyticsDb::ADB_STATS_DAILY;

        return $this;
    }

    /**
     * Sets the lookup to the monthly collection.
     *
     * @return $this
     */
    public function monthly()
    {
        $this->collection = AnalyticsDb::ADB_STATS_MONTHLY;

        return $this;
    }

    public function addAttributeFilter($name, $value = null)
    {
        if (empty($value)) {
            $this->pipeline['$match']['attributes.' . $name] = ['$exists' => true];
        } else {
            $this->pipeline['$match']['attributes.' . $name] = $value;
        }

        return $this;
    }

    /**
     * This is the callback method that is triggered after the constructor call.
     *
     * @return void
     */
    protected function populate()
    {
        $this->collection = AnalyticsDb::ADB_STATS_DAILY;

        // $match
        $this->pipeline['$match']['entity'] = $this->entity;
        if (!empty($this->ref)) {
            $this->pipeline['$match']['ref'] = $this->ref;
        }
        $this->pipeline['$match']['ts'] = ['$gte' => $this->dateRange[0], '$lte' => $this->dateRange[1]];

        // $project
        $this->pipeline['$project'] = [
            'count'  => 1,
            'ref'    => 1,
            'ts'     => 1,
            'entity' => 1,
            'month'  => 1,
            'year'   => 1
        ];

        // $group
        $this->pipeline['$group'] = ['_id' => '$ts', 'totalCount' => ['$sum' => '$count']];

        // $sort -> by timestamp
        $this->pipeline['$sort'] = ['_id' => 1];
    }
}