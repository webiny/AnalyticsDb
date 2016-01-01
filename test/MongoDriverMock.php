<?php
/**
 * Webiny (http://www.webiny.com/)
 *
 * @link      http://www.webiny.com/ for the canonical source repository
 * @copyright Copyright (c) 2009-2015 Webiny LTD. (http://www.webiny.com)
 * @license   MIT
 */

namespace Webiny\AnalyticsDb\Test;

use Webiny\Component\Mongo\MongoCursor;
use Webiny\Component\Mongo\MongoInterface;

class MongoDriverMock implements MongoInterface
{

    public function connect($host, $database, $user = null, $password = null, array $options = [])
    {
        return true;
    }

    /**
     * Get database collection names
     *
     * @param bool $includeSystemCollections
     *
     * @return array
     */
    public function getCollectionNames($includeSystemCollections = false)
    {
        return [];
    }

    /**
     * Insert data into collection<br>
     * Returns an array containing the status of the insertion if the "w" option is set.<br>
     * Otherwise, returns TRUE if the inserted array is not empty (a MongoException will be thrown if the inserted array is empty).
     *
     * @param string $collectionName
     * @param array  $data
     * @param array  $options options
     *
     * @return array|bool
     */
    public function insert($collectionName, array $data, $options = [])
    {
        // TODO: Implement insert() method.
    }

    /**
     * Performs an operation similar to SQL's GROUP BY command
     *
     * @param string $collectionName collection name
     * @param array  $keys           keys
     * @param array  $initial        initial
     * @param array  $reduce         reduce
     * @param array  $condition      condition
     *
     * @see http://php.net/manual/en/mongocollection.group.php
     *
     * @return array
     */
    public function group($collectionName, $keys, array $initial, $reduce, array $condition = [])
    {
        // TODO: Implement group() method.
    }

    /**
     * Ensure index<br>
     * Returns an array containing the status of the index creation.
     *
     * @param string $collectionName name
     * @param string $keys           keys
     * @param array  $options        options
     *
     * @return array
     */
    public function ensureIndex($collectionName, $keys, $options = [])
    {
        return [];
    }

    /**
     * Get reference
     *
     * @param array $ref ref
     *
     * @return \MongoDBRef
     */
    public function getReference(array $ref)
    {
        // TODO: Implement getReference() method.
    }

    /**
     * Get collection indexes
     *
     * @param string $collectionName Collection name
     *
     * @return array
     */
    public function getIndexInfo($collectionName)
    {
        // TODO: Implement getIndexInfo() method.
    }

    /**
     * Delete index from given collection
     *
     * @param string $collectionName Collection name
     * @param string $index          Index name
     *
     * @return mixed
     */
    public function deleteIndex($collectionName, $index)
    {
        // TODO: Implement deleteIndex() method.
    }

    /**
     * Delete all indexes from given collection
     *
     * @param string $collectionName Collection name
     *
     * @return array
     */
    public function deleteAllIndexes($collectionName)
    {
        // TODO: Implement deleteAllIndexes() method.
    }

    /**
     * Execute JavaScript code on the database server.<br>
     * Returns result of the evaluation.
     *
     * @param string $code code
     * @param array  $args array
     *
     * @see http://php.net/manual/en/mongodb.execute.php
     *
     * @return mixed
     */
    public function execute($code, array $args = [])
    {
        // TODO: Implement execute() method.
    }

    /**
     * Find
     *
     * @param string $collectionName collection name
     * @param array  $query          query
     * @param array  $fields         fields
     *
     * @return MongoCursor
     */
    public function find($collectionName, array $query = [], array $fields = [])
    {
        // TODO: Implement find() method.
    }

    /**
     * Create collection
     *
     * @param string $name   name
     * @param bool   $capped Enables a capped collection. To create a capped collection, specify true. If you specify true, you must also set a maximum size in the size field.
     * @param int    $size   Specifies a maximum size in bytes for a capped collection. The size field is required for capped collections. If capped is false, you can use this field to preallocate space for an ordinary collection.
     * @param int    $max    The maximum number of documents allowed in the capped collection. The size limit takes precedence over this limit. If a capped collection reaches its maximum size before it reaches the maximum number of documents, MongoDB removes old documents. If you prefer to use this limit, ensure that the size limit, which is required, is sufficient to contain the documents limit.
     *
     * @return array
     */
    public function createCollection($name, $capped = false, $size = 0, $max = 0)
    {
        return [];
    }

    /**
     * Drop collection<br>
     * Returns the database response.
     * <code>
     * Array
     *   (
     *       [nIndexesWas] => 1
     *       [msg] => all indexes deleted for collection
     *       [ns] => my_db.articles
     *       [ok] => 1
     *   )
     * </code>
     *
     * @param $collectionName
     *
     * @return array
     */
    public function dropCollection($collectionName)
    {
        // TODO: Implement dropCollection() method.
    }

    /**
     * Execute Mongo command
     *
     * @param array $data data
     *
     * @see http://php.net/manual/en/mongodb.command.php
     *
     * @return string|null
     */
    public function command(array $data)
    {
        // TODO: Implement command() method.
    }

    /**
     * Returns an array of distinct values, or FALSE on failure
     *
     * @param array $data Aggregation data
     *
     * @see http://php.net/manual/en/mongocollection.distinct.php
     *
     * @return array|false
     */
    public function distinct(array $data)
    {
        // TODO: Implement distinct() method.
    }

    /**
     * Find one<br>
     * Returns array of data or NULL if not found.
     *
     * @param string $collectionName collection name
     * @param array  $query          query
     * @param array  $fields         fields
     *
     * @return array|null
     */
    public function findOne($collectionName, array $query = [], array $fields = [])
    {
        // TODO: Implement findOne() method.
    }

    /**
     * Returns number of documents in given collection by given criteria.
     *
     * @param string $collectionName collection name
     * @param array  $query          query
     *
     * @return int
     */
    public function count($collectionName, array $query = [])
    {
        // TODO: Implement count() method.
    }

    /**
     * Remove documents from collection by given criteria.<br>
     * Returns array containing result of remove operation.
     *
     * <code>
     * Array
     *   (
     *       [n] => 1
     *       [connectionId] => 61
     *       [err] =>
     *       [ok] => 1
     *   )
     *
     * </code>
     *
     * @param string $collectionName collection name
     * @param array  $criteria       criteria
     * @param array  $options        options
     *
     * @return array
     */
    public function remove($collectionName, array $criteria, $options = [])
    {
        // TODO: Implement remove() method.
    }

    /**
     * Insert or update existing record<br>
     * If `w` was set, returns an array containing the status of the save.<br>
     * Otherwise, returns a boolean representing if the array was not empty (an empty array will not be inserted).
     *
     * @param string $collectionName collection name
     * @param array  $data           data
     * @param array  $options        options
     *
     * @return array|bool
     */
    public function save($collectionName, array $data, $options = [])
    {
        // TODO: Implement save() method.
    }

    /**
     * Update document<br>
     * Returns array containing result of update operation.
     * <code>
     * Array
     *   (
     *       [updatedExisting] => 1
     *       [n] => 1
     *       [connectionId] => 67
     *       [err] =>
     *       [ok] => 1
     *   )
     * </code>
     *
     * @param string $collectionName collection name
     * @param array  $criteria       criteria
     * @param array  $newObj         new obj
     * @param array  $options        options
     *
     * @return array
     */
    public function update($collectionName, array $criteria, array $newObj, $options = [])
    {
        return [];
    }

    /**
     * Aggregate
     *
     * @param       $collectionName
     * @param array $pipeline
     * @param array $options
     *
     * @return array
     */
    public function aggregate($collectionName, array $pipeline, $options = [])
    {
        // TODO: Implement aggregate() method.
    }
}