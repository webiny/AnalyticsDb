<?php
// get instance
$mongo = new \Webiny\Component\Mongo\Mongo();
$a = new AnalyticsDb($mongo);

// insert data
$a->log('visitor', null, ['country' => 'GB', 'browser' => 'Chrome', 'device'=>'mobile']);
$a->log('browser', 'Chrome');
$a->log('country', 'GB');
$a->log('page', '123', ['referrer' => 'www.google.com']);
$a->save(); // use multiple insert query

// time query
$a->query('page', '123', 'this week'); // no group by
$a->query('page', '123', ['date from', 'date to'], 'month'); // group by month

/*
// get top 10 pages for dec 2015 sorted by descending number of visitors
// $a->query('page', null, ['month'=>12, 'year'=>2015], 'sum', 10);
db.AnalyticsDb.aggregate([
  {$match: {'name':'page', 'month':'12', 'year':'2015'}},
  {$project: {'count': 1, 'ts' : 1, 'refId': 1}},
  {$group: {_id:"$refId", totalCount:{$sum:"$count"}}},
  {$sort: {"totalCount":-1}},
  {$limit: 10}
]);

// get the country dimension for page 100 for the given time window
// $a->query('page', 10, ['month'=>12, 'year'=>2015], 'sum');
// $result->getDimension('country')
db.AnalyticsDbDimensions.aggregate([
  {$match: {'name':'country', 'entity':'page', 'month':'12', 'year':'2015'}},
  {$project: {'count': 1, 'ts': 1, 'value': 1, 'counts.100':1}},
  {$group: {_id:"$value", totalCount:{$sum:"$counts.100"}}},
  {$sort: {"totalCount":-1}},
]);

// get total visitors for 2015 group data by months, sort by month
// $a->query('page', null, ['year'=>2015], ['month'=>1]);
db.AnalyticsDb.aggregate([
  {$match: {'name':'page', 'year':'2015'}},
  {$project: {'count': 1, 'ts' : 1, 'refId': 1, 'month': 1}},
  {$group: {_id:"$month", totalCount:{$sum:"$count"}}},
  {$sort: {"month":1}}
]);

db.runCommand({
        aggregate: "AnalyticsDbStats",
        pipeline: [
           {
              {$match: {'name':'page', 'year':'2015', 'refId':115}},
              {$project: {'count': 1, 'ts' : 1, 'refId': 1, 'month': 1}},
              {$group: {_id:"$month", totalCount:{$sum:"$count"}}},
              {$sort: {"month":1}}
           }
        ],
        explain: true
    });

*/