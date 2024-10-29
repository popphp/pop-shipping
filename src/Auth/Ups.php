<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Auth;

use Pop\Http;
use Pop\Shipping\Client\AbstractShippingClient;

/**
 * Pop shipping UPS adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Ups extends AbstractAuthClient
{

    /**
     * Production API URL
     * @var ?string
     */
    protected ?string $prodApiUrl = AbstractShippingClient::UPS_PROD_API_URL;

    /**
     * Test API URL
     * @var ?string
     */
    protected ?string $testApiUrl = AbstractShippingClient::UPS_TEST_API_URL;

    /**
     * Auth API URL
     * @var ?string
     */
    protected ?string $authApiUrl = '/security/v1/oauth/token'; // POST

    /**
     * Create auth client
     *
     * @param  string $clientId
     * @param  string $secret
     * @param  string $merchantId
     * @param  bool   $prod
     * @return static
     */
    public static function createAuthClient(string $clientId, string $secret, string $merchantId, bool $prod = false): static
    {
        $authClient = new static();
        $client     = new Http\Client(Http\Auth::createBasic($clientId, $secret), [
            'base_uri' => ($prod) ? $authClient->getProdApiUrl() : $authClient->getTestApiUrl(),
            'method'   => 'POST',
            'headers'  => ['x-merchant-id' => $merchantId],
            'data'     => [
                'grant_type'    => 'client_credentials'
            ]
        ]);

        $authClient->setClient($client);

        return $authClient;
    }

}
