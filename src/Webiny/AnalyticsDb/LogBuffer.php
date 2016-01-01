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
 * Class LogBuffer
 * @package Webiny\AnalyticsDb
 */
class LogBuffer
{
    /**
     * @var array List of log entries.
     */
    private $entries;

    public function __construct()
    {
        $this->entries = [];
    }

    /**
     * Returns the list of entries.
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Add a log entry to the list.
     *
     * @param LogEntry $logEntry
     */
    public function addEntry(LogEntry $logEntry)
    {
        $this->entries[] = $logEntry;
    }
}