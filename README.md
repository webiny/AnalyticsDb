AnalyticsDb
=================

AnalyticsDb is a component that enables you to store and query different time-series (numerical) data.
Simple use-case would be tracking the number of visitors for your website inside the given date/time range, or tracking
ecommerce revenue for a given quarter.

```php
// get analytics instance
$mongo = new \Webiny\Component\Mongo\Mongo('127.0.0.1:27017', 'webiny');
$analytics = new \Webiny\AnalyticsDb\AnalyticsDb($mongo);

// store some visitor data
$analytics->log('visitor')->addDimension('browser', 'chrome')->addDimension('country', 'UK');

// store some revenue data
$analytics->log('revenue', 0, 120.00)
    ->addDimension('product', 'hdd', 79.50)
    ->addDimension('product', 'mouse', 20.50)
    ->addDimension('tax', 'in-country', 20);

// save the data
$analytics->save();

// query data
// get total number of visitors for the last 30 days, and group them by day
$qb = $analytics->getQueryBuilder('visitors', 0, DateHelper::rangeLast30Days());
$result = $qb->getStats();

// get total number of visitors for the last year, and group them by month
$qb = $analytics->getQueryBuilder('visitors', 0, DateHelper::rangeYear());
$result = $qb->getStats(QueryBuilder::STATS_GROUPBY_MONTH);

// get revenue for last quarter and group it by revenue type
$qb = $analytics->getQueryBuilder('revenue', 0, DateHelper::rangeYear());
$result = $qb->getDimension(null, null, QueryBuilder::DIM_GROUPBY_NAME);
```

## Dependencies

The component requires an instance of `\Webiny\Component\Mongo\Mongo` to access your Mongo database where it will create
several collections to store the data.

## Storing data

The data is stored using the `log` method. Note that data is not actually saved until you call the `save` method.

To assign attributes to your data, for example you wish to increment the number of visitors on your site, but you also want  
to store some attributes, like what browser the user used, and from which country he came from; for that you can use `dimensions`.
Dimensions are also counters which can be queried and grouped. 

For example, for this use case:
```php
$analytics->log('visitor')->addDimension('browser', 'chrome')->addDimension('country', 'UK');
```
You can know who many visitors you had for a given date range, and you can group that result either by day, or by month.
Since you stored some data in dimensions, you can also know, how many users used `chrome` vs, for example `firefox` or `ie` 
and then you can cross reference that to the total number of your visitors.
 
You can also assign a referral value to the log, for example, you can track per-page analytics like so:

```php
$analytics->log('page', 123)->addDimension('browser', 'chrome')->addDimension('country', 'UK');
```
This will track a visitor for page with the id of 123. And then later you can query the analytics data for only that page.

By default the `log` method will increment the value by 1, but in some cases, for example when you wish to track revenue, 
 you want to specify the increment value, and this is done by using the 3rd parameter, like so:
 
```php
$analytics->log('revenue', 0, 120.00);
```
This will increase the `revenue` counter by `120.00` (float). 


## Querying data

To query the data, you need to get an instance of the query builder, like so:
```php
$qb = $analytics->getQueryBuilder('visitors', 0, DateHelper::rangeLast30Days());
```
For the query builder you have to specify the entity name, referral, and the date range.
There is a `DateHelper` class to help you in regards to some commonly used date ranges, but you can also specify your own custom range, 
it is just an array with two unix timestamps `[dateFromTimestamp, dateToTimestamp]`.

Once you have the query builder instance, you can get the results for the given range. By default the data is grouped by day, but you 
can also get it in a per-month format.

```php
$qb = $analytics->getQueryBuilder('visitors', 0, DateHelper::rangeLast30Days());

// get data by day
$result = $qb->getStats();

// get data by month
$result = $qb->getStats(QueryBuilder::STATS_GROUPBY_MONTH);
```

To query dimensions, you use the `getDimension` method, like so:

```php
$qb = $analytics->getQueryBuilder('revenue', 0, DateHelper::rangeQ1());

// get total revenue for Q1, grouped by month
$result = $qb->getStats(QueryBuilder::STATS_GROUPBY_MONTH);

// show me the revenue breakdown by item tipe (eg, product, tax)
$result = $qb->getDimension(null, null, QueryBuilder::DIM_GROUPBY_NAME);

// show me total revenue just from products
$result = $qb->getDimension('product');

// show me total revenue breakdown by product type
$result = $qb->getDimension('product', null, QueryBuilder::DIM_GROUPBY_VALUE);

// show me total revenue for `HDD` product
$result = $qb->getDimension('product', 'HDD');
```

## License and Contributions

Contributing > Feel free to send PRs.

License > [MIT](LICENSE)

## Resources

To run unit tests, you need to use the following command:
```
$ cd path/to/GeoIp/
$ composer install
$ phpunit
```