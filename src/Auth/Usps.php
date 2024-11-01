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
 * Pop shipping USPS adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Usps extends AbstractAuthClient
{

    /**
     * Production API URL
     * @var ?string
     */
    protected ?string $prodApiUrl = AbstractShippingClient::USPS_PROD_API_URL;

    /**
     * Test API URL
     * @var ?string
     */
    protected ?string $testApiUrl = AbstractShippingClient::USPS_TEST_API_URL;

    /**
     * Auth API URL
     * @var ?string
     */
    protected ?string $authApiUrl = '/oauth2/v3/token'; // POST

    /**
     * Create USPS auth client
     *
     * @param  string  $clientId
     * @param  string  $secret
     * @param  string  $accountId
     * @param  bool    $prod
     * @return static
     */
    public static function createAuthClient(string $clientId, string $secret, string $accountId, bool $prod = false): static
    {
        $authClient = new static();
        $authClient->setAccountNumber($accountId)
            ->setProduction($prod);

        $client = new Http\Client([
            'base_uri' => $authClient->getApiUrl(),
            'method' => 'POST',
            'data'   => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $secret
            ]
        ]);

        $authClient->setClient($client);

        return $authClient;
    }
}
