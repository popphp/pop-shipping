pop-shipping
=========

[![Build Status](https://github.com/popphp/pop-shipping/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-shipping/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-shipping)](http://cc.popphp.org/pop-shipping/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Create Auth Client](#create-auth-client)
* [Create Shipping Adapter](#create-shipping-adapter)
* [Get Rates](#get-rates)
* [Get Tracking](#get-tracking)
* [Validate Address](#validate-address)

Overview
--------
Pop Shipping is a helpful component to manage different shipping APIs from common shipping providers.
With it, you can get shipping rates as well as track packages. Currently, the supported shipping providers are:

- UPS
- FedEx

Also, it provides address validation through the Google Maps API.

[Top](#pop-shipping)

Install
-------

Install `pop-shipping` using Composer.

    composer require popphp/pop-shipping

Or, require it in your composer.json file

    "require": {
        "popphp/pop-shipping" : "^3.0.0"
    }

[Top](#pop-shipping)

Create Auth Client
------------------

### FedEx

```php
$authClient = Pop\Shipping\Auth\Fedex::createAuthClient(
    'CLIENT_ID', 'SECRET', 'ACCOUNT_ID'
);
```

### UPS

```php
$authClient = Pop\Shipping\Auth\Fedex::createAuthClient(
    'CLIENT_ID', 'SECRET', 'ACCOUNT_ID'
    );
```

#### Authenticate and store token

```php
if ($authClient->hasTokenDataFile(__DIR__ . '/../data/access.json')) {
    $authClient->fetchAuthToken(__DIR__ . '/../data/access.json');
} else {
    $authClient->authenticate(__DIR__ . '/../data/access.json');
}
```

Create Shipping Adapter
-----------------------

### FedEx

```php
$adapter = Pop\Shipping\Adapter\Fedex::createAdapter($authClient);
```

### UPS

```php
$adapter = Pop\Shipping\Adapter\Ups::createAdapter($authClient);
```

Get Rates
---------

```php
$shipping = new Pop\Shipping\Shipping($adapter);

$shipping->setShipTo([
    'first_name'  => 'John',
    'last_name'   => 'Doe',
    'address1'    => '123 Main St',
    'city'        => 'Some Town',
    'state'       => 'FL',
    'zip'         => '12345',
    'residential' => true,
]);

$shipping->setShipFrom([
    'first_name' => 'Jane',
    'last_name'  => 'Doe',
    'address1'   => '456 Main St',
    'city'       => 'Main Town',
    'state'      => 'GA',
    'zip'        => '54321'
]);

$shipping->addPackage(new Pop\Shipping\Package(34, 24, 12, 65, 1000));

print_r($shipping->getRates());
```

Get Tracking
------------

```php
$shipping->addTrackingNumbers([
    '11111111111111',
    '11111111111112',
    '11111111111113'
]);
print_r($shipping->getTracking());
```

Validate Address
----------------

```php
$google = new Pop\Shipping\Adapter\Google('GOOGLE_API_KEY');
$google->setOriginalAddress([
    'address1'    => '123 Bad St.',
    'city'        => 'Wrong Town',
    'state'       => 'FL',
    'postal_code' => '12345'
]);

if ($google->validate()) {
    echo 'Address has been confirmed.'
} else {
    print_r($google->getSuggestedAddress());
}
```

[Top](#pop-shipping)
