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
class Dimensions extends AbstractQuery
{
    /**
     * Filter the dimension records.
     *
     * @param string      $dimensionName  Dimension name.
     * @param string|null $dimensionValue Dimension value.
     *
     * @return $this
     */
    public function setDimension($dimensionName, $dimensionValue = null)
    {
        $this->pipeline['$match']['name'] = $dimensionName;

        if (!empty($dimensionValue)) {
            $this->pipeline['$match']['value'] = $dimensionValue;
        }

        return $this;
    }

    /**
     * Groups the records by dimension name.
     *
     * @return $this
     */
    public function groupByDimensionName()
    {
        $this->pipeline['$group'] = ['_id' => '$name', 'totalCount' => ['$sum' => $this->getSumField()]];

        return $this;
    }

    /**
     * Groups records by dimension value.
     *
     * @return $this
     */
    public function groupByDimensionValue()
    {
        $this->pipeline['$group'] = [
            '_id'        => ['name' => '$name', 'value' => '$value'],
            'totalCount' => ['$sum' => $this->getSumField()]
        ];

        return $this;
    }

    /**
     * Groups the records by entity name.
     *
     * @overwrite
     * @return $this
     */
    public function groupByEntityName()
    {
        $this->pipeline['$group'] = ['_id' => '$entity', 'totalCount' => ['$sum' => $this->getSumField()]];

        return $this;
    }

    /**
     * Groups the records by timestamp.
     *
     * @overwrite
     * @return $this
     */
    public function groupByTimestamp()
    {
        $this->pipeline['$group'] = ['_id' => '$ts', 'totalCount' => ['$sum' => $this->getSumField()]];

        return $this;
    }

    /**
     * This is the callback method that is triggered after the constructor call.
     *
     * @return void
     */
    protected function populate()
    {
        $this->collection = AnalyticsDb::ADB_DIMS;

        // $match
        $this->pipeline['$match']['entity'] = $this->entity;
        $this->pipeline['$match']['ts'] = ['$gte' => $this->dateRange[0], '$lte' => $this->dateRange[1]];

        // $project
        $this->pipeline['$project'] = [
            'ts'     => 1,
            'entity' => 1,
            'month'  => 1,
            'year'   => 1,
            'name'   => 1,
            'value'  => 1,
            'total'  => 1
        ];

        // $group
        $this->pipeline['$group'] = ['_id' => '$ts', 'totalCount' => ['$sum' => $this->getSumField()]];

        // $sort
        $this->pipeline['$sort'] = ['ts' => 1];
    }

    /**
     * Based on the value of the ref field, the count field is set for the $sum function.
     *
     * @return string
     */
    private function getSumField()
    {
        if (!empty($this->ref)) {
            return '$count.' . $this->ref;
        } else {
            return '$total';
        }
    }

}