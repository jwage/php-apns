PHP APNS
========

PHP Apple Push Notification Service Library

[![Build Status](https://secure.travis-ci.org/jwage/php-apns.png?branch=master)](http://travis-ci.org/jwage/php-apns)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/jwage/php-apns/badges/quality-score.png?s=98f9260f6488ed20d32d21106dc349c1eac26a35)](https://scrutinizer-ci.com/g/jwage/php-apns/)
[![Code Coverage](https://scrutinizer-ci.com/g/jwage/php-apns/badges/coverage.png?s=cd50bac60d2699353b0d9ffef3b7a1a4f6568f70)](https://scrutinizer-ci.com/g/jwage/php-apns/)
[![Latest Stable Version](https://poser.pugx.org/jwage/php-apns/v/stable.png)](https://packagist.org/packages/jwage/php-apns)
[![Total Downloads](https://poser.pugx.org/jwage/php-apns/downloads.png)](https://packagist.org/packages/jwage/php-apns)
[![Dependency Status](https://www.versioneye.com/php/jwage:php-apns/1.0.0/badge.png)](https://www.versioneye.com/php/jwage:php-apns/1.0.0)


## Install

Install PHP APNS using composer:

    composer require jwage/php-apns

## Generate Safari Notification Package

```php
use JWage\APNS\Certificate;
use JWage\APNS\Safari\PackageGenerator;

$certificate = new Certificate(file_get_contents('apns.p12'), 'certpassword');
$packageGenerator = new PackageGenerator(
    $certificate, '/base/pushPackage/path', 'yourdomain.com'
);

// returns JWage\APNS\Safari\Package instance
$package = $packageGenerator->createPushPackageForUser('userid');

// send zip file to the browser
echo $package->getZipPath();
```

## Sending Notifications

```php
use JWage\APNS\Certificate;
use JWage\APNS\Client;
use JWage\APNS\Sender;
use JWage\APNS\SocketClient;

$certificate = new Certificate(file_get_contents('apns.pem'));
$socketClient = new SocketClient($certificate, 'gateway.push.apple.com', 2195);
$client = new Client($socketClient);
$sender = new Sender($client);

$sender->send('devicetoken', 'Title of push', 'Body of push', 'http://deeplink.com');
```
