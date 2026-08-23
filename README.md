pop-shipping
============

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Create Auth Client](#create-auth-client)
* [Create Shipping Adapter](#create-shipping-adapter)
* [Get Rates](#get-rates)
* [Get Tracking](#get-tracking)
* [Error Handling](#error-handling)
* [Validate Address](#validate-address)

Overview
--------
Pop Shipping is a helpful component to manage different shipping APIs from common shipping providers.
With it, you can get shipping rates as well as track packages. Currently, the supported shipping providers are:

- FedEx
- UPS

Also, it provides address validation through the Google Address Validation API.

[Top](#pop-shipping)

Install
-------

Install `pop-shipping` using Composer.

    composer require popphp/pop-shipping

Or, require it in your composer.json file

    "require": {
        "popphp/pop-shipping" : "^4.0.0"
    }

[Top](#pop-shipping)

Create Auth Client
------------------

FedEx and UPS both authenticate via OAuth2 client-credentials — an auth client handles fetching and caching the
bearer token that shipping adapters use to call the actual rates/tracking APIs.

### FedEx

```php
$authClient = Pop\Shipping\Auth\Fedex::createAuthClient(
    'CLIENT_ID', 'SECRET', 'ACCOUNT_ID'
);
```

### UPS

```php
$authClient = Pop\Shipping\Auth\Ups::createAuthClient(
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

`fetchAuthToken()` reads the cached token from the file and only calls out to FedEx/UPS to re-authenticate if the
token is missing or close to expiring (within 10 seconds, by default — pass a second `$buffer` argument in seconds
to change that window). This avoids hitting the auth endpoint on every request, which most providers rate-limit.

#### Production vs. sandbox

Both `createAuthClient()` methods take an optional 4th `$prod` argument (default `false`), which controls whether
every subsequent auth and API call targets the provider's live production host or its sandbox:

```php
$authClient = Pop\Shipping\Auth\Fedex::createAuthClient(
    'CLIENT_ID', 'SECRET', 'ACCOUNT_ID', true // production
);
```

The shipping adapter created from this auth client (see below) automatically inherits the same setting, so it only
needs to be set once, on the auth client.

[Top](#pop-shipping)

Create Shipping Adapter
-----------------------

A shipping adapter is created from an already-authenticated auth client — call `authenticate()` or
`fetchAuthToken()` (above) first, or `createAdapter()` won't have a valid token to build its HTTP client from.

### FedEx

```php
$adapter = Pop\Shipping\Adapter\Fedex::createAdapter($authClient);
```

### UPS

```php
$adapter = Pop\Shipping\Adapter\Ups::createAdapter($authClient);
```

[Top](#pop-shipping)

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

print_r($shipping->fetchRates());
```

`fetchRates()` performs the actual API call and returns the normalized rates — it's the method to use to get rates.
`getRates()` is separate: it just re-reads whatever `fetchRates()` last cached, without making a new request, which
is useful if you need the same result again later without re-fetching it.

Each rate in the returned array has the same shape regardless of provider, sorted cheapest-first:

```php
[
    'service'     => 'Fedex', // or 'UPS'
    'serviceType' => 'FEDEX_GROUND', // the provider's own service code
    'serviceName' => 'FedEx Ground', // a human-readable name
    'totalCharge' => '24.55', // string, 2 decimal places
]
```

A few other things worth knowing:

- `Package`'s constructor is `(width, height, depth, weight, value = null, packaging = null)` — `value` (for
  insurance/customs) and `packaging` (box/wrapping cost) are both optional. Dimensions default to inches/pounds/USD;
  call `setDimensionUnit()`, `setWeightUnit()`, or `setValueUnit()` on a `Package` to use different units (e.g.
  `'CM'`, `'KG'`).
- Multiple packages can be added at once with `addPackages([$package1, $package2])`.
- `Address` accepts `zip`/`postal_code` and `province`/`state` interchangeably — `setShipTo(['zip' => ...])` and
  `setShipTo(['postal_code' => ...])` both work.

[Top](#pop-shipping)

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

Tracking numbers can also be passed directly to `getTracking()` instead of adding them first:

```php
print_r($shipping->getTracking('11111111111111'));
print_r($shipping->getTracking(['11111111111111', '11111111111112']));
```

The result is keyed by tracking number, each holding a list of tracking events sorted oldest-first:

```php
[
    '11111111111111' => [
        [
            'status'           => 'IT', // provider's own status code
            'eventType'        => 'X',
            'eventDescription' => 'In transit',
            'dateTime'         => '2026-01-01 12:00:00',
        ],
        // ...
    ],
]
```

UPS occasionally returns a plain warning message instead of an event list for a given tracking number (e.g. "No
tracking information available") — in that case, the value at that tracking number's key is a string, not an array,
so check `is_array()` before iterating if you need to handle that case explicitly.

[Top](#pop-shipping)

Error Handling
--------------

Provider API calls fail — bad addresses, expired tokens, rate limits — so don't assume `fetchRates()`/
`getTracking()` always return usable data. Check for an error after either call:

```php
$rates = $shipping->fetchRates();

if (!$shipping->isSuccess()) {
    echo $shipping->getErrorCode() . ': ' . $shipping->getErrorMessage();
} else {
    print_r($rates);
}
```

`isSuccess()` reflects the outcome of the last `fetchRates()` call specifically (there's no separate
tracking-success flag — inspect the returned tracking array itself, since a failed tracking lookup for one number
doesn't prevent others in the same batch from succeeding). `getResponse()` is also available if you need the raw,
unparsed provider response for debugging.

[Top](#pop-shipping)

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
    echo 'Address has been confirmed.';
} else if ($google->hasSuggestedAddress()) {
    print_r($google->getSuggestedAddress()->toArray());
} else {
    echo 'Address could not be validated - review it manually.';
}
```

`validate()` returns `true` only when Google accepts the address exactly as given. Otherwise, one of two things
happened, and only `hasSuggestedAddress()` tells them apart:

- Google found a standardized/corrected version of the address that differs from the input — `getSuggestedAddress()`
  returns it as an `Address` object for the caller to confirm with the end user.
- The address has unresolved issues Google can't reliably auto-correct (e.g. missing/invalid components) — there is
  no suggested address, and the caller needs to prompt for a corrected address some other way.

The address can also be passed directly to `validate($address)` instead of calling `setOriginalAddress()` first.

[Top](#pop-shipping)
