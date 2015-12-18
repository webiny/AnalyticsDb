GeoIp
=================

GeoIp is a simple component providing location information for a given IPv4 or IPv6 address.

Example:

```php
$geo = new \Webiny\GeoIp\GeoIp(new \Webiny\GeoIp\Provider\FreeGeoIp\FreeGeoIp());
$location = $geo->getGeoIpLocation('8.8.8.8');

// output
array(
   'continentCode' => NULL,
   'continentName' => NULL,
   'countryCode' => 'US',
   'countryName' => 'United States',
   'cityName' => 'Mountain View',
   'subdivision1Code' => 'CA',
   'subdivision1Name' => 'California',
   'subdivision2Code' => NULL,
   'subdivision2Name' => NULL,
   'timeZone' => 'America/Los_Angeles',
)
```

Depending on the provider, different information will be available.
The GeoIp standard library comes with 2 providers:
- `FreeGeoIp`: uses the https://freegeoip.net/ API
- `MaxMindGeoLite2`: uses the GeoLite2 database provided by MaxMind (requires a Mongo database)

Install GeoIp
---------------------
The best way to install the component is using Composer.

```bash
composer require webiny/geoip
```
For additional versions of the package, visit the [Packagist page](https://packagist.org/packages/webiny/geoip).


## Setup

### FreeGeoIp

If you plan to use the `FreeGeoIp` API, no setup is required, just create a provider instance and pass it to the `GeoIp` class, like so:

```php
$providerInstance = new \Webiny\GeoIp\Provider\FreeGeoIp\FreeGeoIp()

$geo = new \Webiny\GeoIp\GeoIp($providerInstance);
$location = $geo->getGeoIpLocation('8.8.8.8');
```

### MaxMindGeoLite2

This provider requires a Mongo database, that is configured using `Webiny\Mongo` component. 
In addition to the database, some other parameters are also required, so here is an example config:

```yaml
MaxMind:
    GeoLite2Url: http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip
    Language:  en
Entity:
    Database: GeoIp
Mongo:
    Services:
        GeoIp:
            Class: \Webiny\Component\Mongo\Mongo
            Arguments:
                Host: 127.0.0.1:27017
                Database: YourDatabase ## CHANGE THIS
                Username: null
                Password: null
                CollectionPrefix: ''
```

The `MaxMind` section defines the download location of the GeoLite2 database and the language for the location name that will be imported from that database.
The `Entity` and `Mongo` section are standard configurations as described under `Webiny/Entity` and `Webiny/Mongo` components.

Once you have your configuration in place, run the following command in your terminal:

``` bash
php /path/to/src/Webiny/GeoIp/Provider/MaxMindGeoLite2/run-installer.php abs-path-to-your-config-yaml-file
```

This will download the GeoLite2 database and setup the Mongo collections for you. Note that the process might take up to 15-30 min, depending on your hardware.

Once the installation finishes, you can use the provider like so:

```php
$config = 'abs-path-to-your-config-yaml-file';
$provider = new Webiny\GeoIp\Provider\MaxMindGeoLite2\MaxMindGeoLite2($config);

$geo = new \Webiny\GeoIp\GeoIp($provider);
$location = $geo->getGeoIpLocation('8.8.8.8');
```

### Custom provider

If you wish to implement a custom provider, just create a class for your provider that implements `Webiny\GeoIp\ProviderInterface`;


## Location class

The location class is the result of the geo ip lookup. 
Depending on the provider, the amount of information might vary.
Overall, a typical lookup result looks like this:

```php
Webiny/GeoIp/Location::__set_state(array(
   'continentCode' => 'NA',
   'continentName' => 'North America',
   'countryCode' => 'US',
   'countryName' => 'United States',
   'cityName' => 'Mountain View',
   'subdivision1Code' => 'CA',
   'subdivision1Name' => 'California',
   'subdivision2Code' => '',
   'subdivision2Name' => '',
   'timeZone' => 'America/Los_Angeles',
))
```

Note that if the location cannot be determined, `false` will be returned instead of the `Location` instance.

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