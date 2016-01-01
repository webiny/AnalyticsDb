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
class Query
{
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
     * Query daily or monthly statistics.
     *
     * @return Query\Stats
     */
    public function stats()
    {
        return new Query\Stats($this->mongo, $this->entity, $this->ref, $this->dateRange);
    }

    /**
     * Query the dimensions.
     *
     * @param string $name  Dimension name
     * @param string $value Dimension value
     *
     * @return Query\Dimensions
     */
    public function dimension($name = null, $value = null)
    {
        $query = new Query\Dimensions($this->mongo, $this->entity, $this->ref, $this->dateRange);
        if (!empty($name)) {
            if (!empty($value)) {
                $query->setDimension($name, $value);
            } else {
                $query->setDimension($name);
            }
        }

        return $query;
    }

}