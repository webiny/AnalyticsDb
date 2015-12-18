<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

class LogBuffer
{
    private $entries;

    public function __construct()
    {
        $this->entries = [];
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function addLogEntry(LogEntry $logEntry)
    {
        $this->entries[] = $logEntry;
    }
}