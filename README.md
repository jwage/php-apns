PHP APNS
========

PHP Apple Push Notification Service Library

[![Build Status](https://secure.travis-ci.org/jwage/php-apns.png?branch=master)](http://travis-ci.org/jwage/php-apns)

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
