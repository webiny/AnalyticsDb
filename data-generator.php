<?php
require_once 'vendor/autoload.php';

$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'webiny');
$mongo->createCollection('AnalyticsDbStats');
$mongo->createCollection('AnalyticsDbDimensions');

$result = $mongo->getCollectionNames()->toArray();

print_r($result->toArray());
die();

$recordsToInsert = 1000000;

for ($i = 0; $i < $recordsToInsert; $i++) {

    $date = time() - (86400 * rand(1, 100));
    $refId = rand(100, 150);

    $record = [
        'name'       => 'page',
        'refId'      => $refId,
        'ts'         => new MongoDate($date),
        'dayOfWeek'  => date('N', $date),
        'weekOfYear' => date('W', $date),
        'dayOfYear'  => date('z', $date),
        'month'      => date('n', $date),
        'year'       => date('Y', $date),
        'count'      => rand(300, 2000),
    ];

    $mongo->insert('AnalyticsDbStats', $record);

    $record = [
        'name'       => 'country',
        'value'      => array_rand(['GB'=>1, 'DE'=>1, 'US'=>1, 'FR'=>1, 'HR'=>1]),
        'entity'     => 'page',
        'ts'         => new MongoDate($date),
        'dayOfWeek'  => date('N', $date),
        'weekOfYear' => date('W', $date),
        'dayOfYear'  => date('z', $date),
        'month'      => date('n', $date),
        'year'       => date('Y', $date),
        'counts'     => [
            rand(100, 109)  => rand(1, 200),
            rand(110, 119)  => rand(1, 200),
            rand(120, 129)  => rand(1, 200),
            rand(130, 139)  => rand(1, 200),
            rand(140, 149)  => rand(1, 200),
        ]
        ,
    ];
    //$mongo->insert('AnalyticsDbDimensions', $record);
}

// create indexes
/*
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('entityType', 'name'));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\CompoundIndex('entityId', ['name', 'refId']));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('ts', 'ts'));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('dayOfWeek', 'dayOfWeek'));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('weekOfYear', 'weekOfYear'));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('dayOfYear', 'dayOfYear'));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('month', 'month'));
$mongo->createIndex('AnalyticsDbStats', new \Webiny\Component\Mongo\Index\SingleIndex('year', 'year'));
*/
/*

$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\CompoundIndex('dimension', ['name', 'entity']));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('value', 'value'));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('ts', 'ts'));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('dayOfWeek', 'dayOfWeek'));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('weekOfYear', 'weekOfYear'));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('dayOfYear', 'dayOfYear'));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('month', 'month'));
$mongo->createIndex('AnalyticsDbDimensions', new \Webiny\Component\Mongo\Index\SingleIndex('year', 'year'));
*/
echo "\ndone\n";
die();