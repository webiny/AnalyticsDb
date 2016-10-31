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
     * Sort records by dimension name.
     *
     * @param string $direction Mongo sort direction: 1 => ascending; -1 => descending
     *
     * @return $this
     * @throws AnalyticsDbException
     */
    public function sortByDimensionName($direction)
    {
        if ($this->id != '$name' && $this->id != '$value') {
            throw new AnalyticsDbException('In order to sort by dimension name, you need to first group by dimension name or dimension value.');
        }

        $direction = (int)$direction;

        if ($this->id == '$name') {
            $this->pipeline['$sort'] = ['_id' => $direction];
        } else {
            $this->pipeline['$sort'] = ['_id.name' => $direction];
        }


        return $this;
    }

    /**
     * Sort records by dimension value.
     *
     * @param string $direction Mongo sort direction: 1 => ascending; -1 => descending
     *
     * @return $this
     * @throws AnalyticsDbException
     */
    public function sortByDimensionValue($direction)
    {
        if ($this->id != '$value') {
            throw new AnalyticsDbException('In order to sort by dimension value, you need to first group by dimension value.');
        }

        $direction = (int)$direction;

        $this->pipeline['$sort'] = ['_id.value' => $direction];


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

        $this->id = '$name';

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

        $this->id = '$value';

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

        $this->id = '$entity';

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

        $this->id = '$ts';

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
        $this->pipeline['$match']['count.' . $this->ref] = ['$exists' => true];

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

        if (!empty($this->ref)) {
            $this->pipeline['$project']['count.' . $this->ref] = 1;
        }

        // $group
        $this->pipeline['$group'] = ['_id' => '$ts', 'totalCount' => ['$sum' => $this->getSumField()]];

        // $sort -> by timestamp
        $this->pipeline['$sort'] = ['_id' => 1];
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