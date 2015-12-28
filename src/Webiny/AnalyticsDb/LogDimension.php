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
 * Class LogDimension
 * @package Webiny\AnalyticsDb
 */
class LogDimension
{
    /**
     * @var string Dimension name
     */
    private $name;

    /**
     * @var string|int Dimension value
     */
    private $value;

    /**
     * @var int|float For how much to increment the dimension count
     */
    private $increment;


    /**
     * Base constructor.
     *
     * @param $name
     * @param $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
        $this->increment = 1;
    }

    /**
     * For how much to increment the dimension count.
     *
     * @param int|float $increment
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;
    }

    /**
     * Returns the current increment value.
     *
     * @return float|int
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Returns dimension name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns dimension value.
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }
}