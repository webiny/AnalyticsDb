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
use Webiny\Component\Mongo\Mongo;

/**
 * Class AnalyticsDb
 * @package Webiny\AnalyticsDb
 */
class AnalyticsDb
{
    const ADB_STATS_DAILY = 'AnalyticsDbStatsDaily';
    const ADB_STATS_MONTHLY = 'AnalyticsDbStatsMonthly';
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
    private $month;
    private $year;
    private $monthTs;


    /**
     * Base constructor.
     *
     * @param Mongo $mongo
     */
    public function __construct(Mongo $mongo)
    {
        $this->mongo = $mongo;
        $this->logBuffer = new LogBuffer();

        $this->setTimestamp(time());

        // check if collection exists
        $this->createCollections();
    }

    /**
     * Set the timestamp which will be used to store the data.
     * By default current time is used.
     *
     * @param int $unixTs
     */
    public function setTimestamp($unixTs)
    {
        $this->ts = strtotime(date('Y-m-d', $unixTs));
        $this->month = date('n', $this->ts);
        $this->year = date('Y', $this->ts);
        $this->monthTs = strtotime(date('Y-m-01', $unixTs));
    }

    /**
     * Add a log to the buffer.
     * Note: the data is not saved until you call the save method.
     *
     * @param string $entity
     * @param int    $ref
     * @param int    $increment
     *
     * @return LogEntry
     */
    public function log($entity, $ref = 0, $increment = 1)
    {
        $entry = new LogEntry($entity);
        $entry->setRef($ref);
        $entry->setIncrement($increment);

        $this->logBuffer->addLogEntry($entry);

        return $entry;
    }

    /**
     * Save all the entries from the buffer.
     */
    public function save()
    {
        $entries = $this->logBuffer->getEntries();

        $entrySkeleton = [
            'ts'    => $this->ts,
            'month' => $this->month,
            'year'  => $this->year
        ];

        $dimensionSkeleton = [
            'ts'    => $this->ts,
            'month' => $this->month,
            'year'  => $this->year
        ];

        /**
         * @var $e LogEntry
         */
        foreach ($entries as $e) {
            // build entry
            $entry = $entrySkeleton;
            $entry['name'] = $e->getName();
            $entry['ref'] = $e->getRef();

            // insert or update the DAILY stat
            $this->mongo->update(self::ADB_STATS_DAILY, // match
                [
                    'name' => $e->getName(),
                    'ref'  => $e->getRef(),
                    'ts'   => $this->ts
                ], // update
                [
                    '$inc'         => ['count' => $e->getIncrement()],
                    '$setOnInsert' => $entry
                ], // options
                ['upsert' => 1]);

            // insert or update the MONTHLY stat
            unset($entry['ts']);
            $entry['month'] = $this->month;
            $entry['year'] = $this->year;
            $this->mongo->update(self::ADB_STATS_MONTHLY, // match
                [
                    'name' => $e->getName(),
                    'ref'  => $e->getRef(),
                    'ts'   => $this->monthTs
                ], // update
                [
                    '$inc'         => ['count' => $e->getIncrement()],
                    '$setOnInsert' => $entry
                ], // options
                ['upsert' => 1]);

            // insert dimensions for the entry
            $dimEntry = $dimensionSkeleton;
            $dimEntry['entity'] = $e->getName();

            /**
             * @var $dim LogDimension
             */
            foreach ($e->getDimensions() as $dim) {
                $dimEntry['name'] = $dim->getName();
                $dimEntry['value'] = $dim->getValue();

                $this->mongo->update(self::ADB_DIMS, // match
                    [
                        'name'   => $dim->getName(),
                        'value'  => $dim->getValue(),
                        'entity' => $e->getName(),
                        'ts'     => $this->ts
                    ], // update
                    [
                        '$inc'         => [
                            'count.' . $e->getRef() => $dim->getIncrement(),
                            'total'                 => $dim->getIncrement()
                        ],
                        '$setOnInsert' => $dimEntry
                    ], // options
                    ['upsert' => 1]);
            }
        }

        // clear buffer
        $this->logBuffer = new LogBuffer();
    }

    /**
     * Query the analytics data.
     *
     * @param string     $entity
     * @param string|int $ref
     * @param array      $dateRange [fromTimestamp, toTimestamp]
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder($entity, $ref = 0, array $dateRange)
    {
        return new QueryBuilder($this->mongo, $entity, $ref, $dateRange);
    }

    /**
     * Creates the necessary indexes and collections if they don't exist.
     */
    private function createCollections()
    {
        $collections = $this->mongo->getCollectionNames()->toArray();
        if (!array_search(self::ADB_STATS_DAILY, $collections)) {

            // create collections
            $this->mongo->createCollection(self::ADB_STATS_DAILY);
            $this->mongo->createCollection(self::ADB_STATS_MONTHLY);
            $this->mongo->createCollection(self::ADB_DIMS);

            // ensure indexes
            $this->mongo->createIndex(self::ADB_STATS_DAILY,
                new CompoundIndex('entityTsEntry', ['name', 'ref', 'ts'], true, true));

            $this->mongo->createIndex(self::ADB_STATS_MONTHLY,
                new CompoundIndex('entityMonthEntry', ['name', 'ref', 'ts'], true, true));

            $this->mongo->createIndex(self::ADB_DIMS,
                new CompoundIndex('dimension', ['name', 'value', 'entity', 'ts'], true, true));

            $this->mongo->createIndex(self::ADB_DIMS, new CompoundIndex('dimension_entity', ['entity', 'ts'], true));
        }
    }
}