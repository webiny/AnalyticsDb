<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb;

use MongoDB\Model\CollectionInfo;
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
     * Returns the current timestamp.
     *
     * @return array Array containing time info.
     */
    public function getTimestamp()
    {
        return [
            'ts'      => $this->ts,
            'month'   => $this->month,
            'year'    => $this->year,
            'monthTs' => $this->monthTs
        ];
    }

    /**
     * Add a log to the buffer.
     * Note: the data is not saved until you call the save method.
     *
     * @param string $entity Entity name.
     * @param int    $ref
     * @param int    $increment
     *
     * @return LogEntry
     * @throws AnalyticsDbException
     */
    public function log($entity, $ref = 0, $increment = 1)
    {
        if (!preg_match('/^([A-z0-9\/\-\_]+)$/', $entity)) {
            throw new AnalyticsDbException('Entity name can only contain ([A-z0-9\/\-]).');
        }

        if (!preg_match('/^([A-z0-9\/\-\_]+)$/', $ref)) {
            throw new AnalyticsDbException('Entity referrer can only contain ([A-z0-9\/\-]).');
        }

        $entry = new LogEntry($entity);
        $entry->setRef($ref);
        $entry->setIncrement($increment);

        $this->logBuffer->addEntry($entry);

        return $entry;
    }

    /**
     * Returns log buffer.
     *
     * @return LogBuffer
     */
    public function getLogBuffer()
    {
        return $this->logBuffer;
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
            $entry['entity'] = $e->getName();
            $entry['ref'] = $e->getRef();

            // insert or update the DAILY stat
            $this->mongo->update(self::ADB_STATS_DAILY, // match
                [
                    'entity' => $e->getName(),
                    'ref'    => $e->getRef(),
                    'ts'     => $this->ts
                ], // update
                [
                    '$inc'         => ['count' => $e->getIncrement()],
                    '$setOnInsert' => $entry
                ], // options
                ['upsert' => true]);

            // insert or update the MONTHLY stat
            unset($entry['ts']);
            $entry['month'] = $this->month;
            $entry['year'] = $this->year;
            $this->mongo->update(self::ADB_STATS_MONTHLY, // match
                [
                    'entity' => $e->getName(),
                    'ref'    => $e->getRef(),
                    'ts'     => $this->monthTs
                ], // update
                [
                    '$inc'         => ['count' => $e->getIncrement()],
                    '$setOnInsert' => $entry
                ], // options
                ['upsert' => true]);

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
                    ['upsert' => true]);
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
     * @return Query
     */
    public function query($entity, $ref = 0, array $dateRange)
    {
        return new Query($this->mongo, $entity, $ref, $dateRange);
    }

    /**
     * Creates the necessary indexes and collections if they don't exist.
     */
    private function createCollections()
    {
        $collections = $this->mongo->getCollectionNames();

        $collectionsCreated = false;
        foreach ($collections as $collection) {
            /* @var $collection CollectionInfo */
            if ($collection == self::ADB_STATS_DAILY) {
                $collectionsCreated = true;
                break;
            }
        }

        if (!$collectionsCreated) {

            // create collections
            $this->mongo->createCollection(self::ADB_STATS_DAILY);
            $this->mongo->createCollection(self::ADB_STATS_MONTHLY);
            $this->mongo->createCollection(self::ADB_DIMS);

            // ensure indexes
            $this->mongo->createIndex(self::ADB_STATS_DAILY,
                new CompoundIndex('entityTsEntry', ['entity', 'ref', 'ts'], true, true));

            $this->mongo->createIndex(self::ADB_STATS_MONTHLY,
                new CompoundIndex('entityMonthEntry', ['entity', 'ref', 'ts'], true, true));

            $this->mongo->createIndex(self::ADB_DIMS,
                new CompoundIndex('dimension', ['name', 'value', 'entity', 'ts'], true, true));

            $this->mongo->createIndex(self::ADB_DIMS, new CompoundIndex('dimension_entity', ['entity', 'ts'], true));
        }
    }
}