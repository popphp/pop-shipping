<?php

namespace Pop\Http;

/**
 * Declares the HTTP verb methods that Pop\Http\Client dispatches dynamically
 * through __call() (e.g. $client->get(), $client->post()), which PHPStan
 * cannot otherwise see. The real return type is Client\Response|Promise|
 * array|string (see Client::__call()), but PHPStan cannot resolve those
 * vendor classes while validating a stub file's @method tags, so `mixed`
 * is used here instead.
 *
 * @method mixed get(?string $uri = null)
 * @method mixed post(?string $uri = null)
 * @method mixed put(?string $uri = null)
 * @method mixed patch(?string $uri = null)
 * @method mixed delete(?string $uri = null)
 * @method mixed head(?string $uri = null)
 * @method mixed options(?string $uri = null)
 */
class Client
{
}
