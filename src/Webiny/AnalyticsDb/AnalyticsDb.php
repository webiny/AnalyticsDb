<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

use Webiny\Component\Mongo\Index\CompoundIndex;
use Webiny\Component\Mongo\Index\SingleIndex;
use Webiny\Component\Mongo\Mongo;

class AnalyticsDb
{
    const ADB_STATS = 'AnalyticsDbStatsFoo';
    const ADB_DIMS = 'AnalyticsDbDimensions';

    /**
     * @var Mongo
     */
    private $mongo;

    /**
     * @var LogBuffer
     */
    private $logBuffer;

    // time vars
    private $ts;
    private $dayOfWeek;
    private $weekOfYear;
    private $dayOfYear;
    private $month;
    private $year;

    public function __construct(Mongo $mongo)
    {
        $this->mongo = $mongo;
        $this->logBuffer = new LogBuffer();

        // populate time vars
        $this->ts = strtotime(date('Y-m-d'));
        $this->dayOfWeek = date('N', $this->ts);
        $this->weekOfYear = date('W', $this->ts);
        $this->dayOfYear = date('z', $this->ts);
        $this->month = date('n', $this->ts);
        $this->year = date('Y', $this->ts);

        // check if collection exists
        $this->createCollections();
    }

    public function log($name, $ref = 0, array $dimensions = null)
    {
        $entry = new LogEntry($name);
        $entry->setRef($ref);

        if (is_array($dimensions)) {
            $entry->setDimensions($dimensions);
        }

        $this->logBuffer->addLogEntry($entry);

        return $this;
    }

    public function save()
    {
        $entries = $this->logBuffer->getEntries();

        $entrySkeleton = [
            'ts'         => $this->ts,
            'dayOfWeek'  => $this->dayOfWeek,
            'weekOfYear' => $this->weekOfYear,
            'dayOfYear'  => $this->dayOfYear,
            'month'      => $this->month,
            'year'       => $this->year
        ];

        /**
         * @var $e LogEntry
         */
        foreach ($entries as $e) {
            // build entry
            $entry = $entrySkeleton;
            $entry['name'] = $e->getName();
            $entry['ref'] = $e->getRef();

            // insert or update the stat
            $this->mongo->update(self::ADB_STATS, // match
                [
                    'name' => $e->getName(),
                    'ref'  => $e->getRef(),
                    'ts'   => $this->ts
                ], // update
                [
                    '$inc'         => ['count' => 1],
                    '$setOnInsert' => $entry
                ], // options
                ['upsert' => 1]);

            // insert dimensions for the entry
            foreach ($e->getDimensions() as $dk => $dv) {

            }
        }
    }

    private function createCollections()
    {
        $collections = $this->mongo->getCollectionNames()->toArray();
        if (!array_search(self::ADB_STATS, $collections)) {
            // create collections
            $this->mongo->createCollection(self::ADB_STATS);
            $this->mongo->createCollection(self::ADB_DIMS);

            // ensure stats indexes
            $this->mongo->createIndex(self::ADB_STATS,
                new CompoundIndex('entityTsEntry', ['name', 'ref', 'ts'], true, true));
            $this->mongo->createIndex(self::ADB_STATS, new CompoundIndex('entityRef', ['name', 'ref'], true));
            $this->mongo->createIndex(self::ADB_STATS, new CompoundIndex('month', ['month', 'year'], true));
            $this->mongo->createIndex(self::ADB_STATS, new CompoundIndex('EntityTs', ['ts', 'name'], true));
            $this->mongo->createIndex(self::ADB_STATS, new SingleIndex('entityName', 'name', true));
            $this->mongo->createIndex(self::ADB_STATS, new SingleIndex('year', 'year', true));
        }
    }
}