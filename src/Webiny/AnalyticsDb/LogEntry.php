<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

class LogEntry
{
    private $name;
    private $ref;
    private $dimensions;
    private $increment;

    public function __construct($name)
    {
        $this->name = $name;
        $this->dimensions = [];
        $this->increment = 1;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setIncrement($increment)
    {
        $this->increment = $increment;
    }

    public function getIncrement()
    {
        return $this->increment;
    }

    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function addDimension($name, $value, $increment = 1)
    {
        $dimension = new LogDimension($name, $value);
        $dimension->setIncrement($increment);

        $this->dimensions[] = $dimension;

        return $this;
    }

    public function getDimensions()
    {
        return $this->dimensions;
    }
}