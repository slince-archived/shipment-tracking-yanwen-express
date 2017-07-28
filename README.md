# Shipment Tracking Library For Yanwen Express

[![Build Status](https://img.shields.io/travis/slince/shipment-tracking-yanwen-express/master.svg?style=flat-square)](https://travis-ci.org/slince/shipment-tracking-yanwen-express)
[![Coverage Status](https://img.shields.io/codecov/c/github/slince/shipment-tracking-yanwen-express.svg?style=flat-square)](https://codecov.io/github/slince/shipment-tracking-yanwen-express)
[![Latest Stable Version](https://img.shields.io/packagist/v/slince/shipment-tracking-yanwen-express.svg?style=flat-square&label=stable)](https://packagist.org/packages/slince/shipment-tracking-yanwen-express)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/slince/shipment-tracking-yanwen-express.svg?style=flat-square)](https://scrutinizer-ci.com/g/slince/shipment-tracking-yanwen-express/?branch=master)

A flexible and shipment tracking library for Yanwen Express(燕文物流).

## Installation

Install via composer

```bash
$ composer require slince/shipment-tracking-yanwen-express
```
## Basic Usage


```php

$tracker = new Slince\ShipmentTracking\YanWenExpress\YanWenTracker(KEY, 'en');

try {
   $shipment = $tracker->track('CNAQV100168101');
   
   if ($shipment->isDelivered()) {
       echo "Delivered";
   }
   echo $shipment->getOrigin();
   echo $shipment->getDestination();
   print_r($shipment->getEvents());  //print the shipment events
   
} catch (Slince\ShipmentTracking\Exception\TrackException $exception) {
    exit('Track error: ' . $exception->getMessage());
}

```
## License
 
The MIT license. See [MIT](https://opensource.org/licenses/MIT)

