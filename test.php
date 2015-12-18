<?php
require_once 'vendor/autoload.php';

$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'webiny');

$a = new \Webiny\AnalyticsDb\AnalyticsDb($mongo);
$a->log('page', 100);
$a->log('page', 101);
$a->log('page', 102);
$a->log('page', 103);
$a->log('page', 104);
$a->log('page', 105);
$a->save();