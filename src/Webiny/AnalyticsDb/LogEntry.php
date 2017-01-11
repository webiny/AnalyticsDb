<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

/**
 * Class LogEntry
 * @package Webiny\AnalyticsDb
 */
class LogEntry
{
    /**
     * @var string Entry name. (entity)
     */
    private $name;

    /**
     * @var string|int Entry referral value.
     */
    private $ref;

    /**
     * @var array List of dimensions associated to the entry.
     */
    private $dimensions;

    /**
     * @var int For how much to increase the entry count.
     */
    private $increment;

    /**
     * @var array List of attributes attached to the entity that you can then filter on
     */
    private $attributes;


    /**
     * Base constructor.
     *
     * @param string $name Entity name.
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->dimensions = [];
        $this->attributes = [];
        $this->increment = 1;
    }

    /**
     * Returns entry name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * For how much to increase the entry count.
     *
     * @param $increment
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;
    }

    /**
     * Returns the increment value.
     *
     * @return int
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Set the referral value.
     *
     * @param string|int $ref
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    /**
     * Returns referral value.
     *
     * @return int|string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Add a dimension to the entry.
     *
     * @param string $name
     * @param string $value
     * @param int    $increment
     *
     * @return $this
     */
    public function addDimension($name, $value, $increment = 1)
    {
        $dimension = new LogDimension($name, $value);
        $dimension->setIncrement($increment);

        $this->dimensions[] = $dimension;

        return $this;
    }

    /**
     * Returns a list of associated dimensions.
     *
     * @return array
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * Adds an attribute to your entity so you can filter on it.
     *
     * @param string      $name
     * @param null|string $value
     */
    public function addAttribute($name, $value = null)
    {
        if (!empty($value)) {
            $this->attributes[] = [$name => $value];
        } else {
            $this->attributes[] = [$name];
        }
    }

    /**
     * Returns a list of associated attributes.
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}