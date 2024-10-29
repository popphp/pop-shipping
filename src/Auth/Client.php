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

/**
 * Pop shipping auth client class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Client extends AbstractClient
{

    /**
     * Constructor
     *
     * Instantiate the shipping auth object
     */
    public function __construct(?Http\Client $client = null)
    {
        if ($client !== null) {
            $this->setClient($client);
        }
    }

    /**
     * Create auth client
     *
     * @param  string  $authUrl
     * @param  string  $clientId
     * @param  string  $secret
     * @param  ?string $merchantId
     * @return static
     */
    public static function createAuthClient(string $authUrl, string $clientId, string $secret, ?string $merchantId = null): static
    {
        $client = new Http\Client($authUrl, ['method' => 'POST']);
        $data   = ['grant_type' => 'client_credentials'];

        if ($merchantId !== null) {
            $data['headers'] = ['x-merchant-id' => $merchantId];
            $client->setAuth(Http\Auth::createBasic($clientId, $secret));
        } else {
            $data['client_id']     = $clientId;
            $data['client_secret'] = $secret;
        }

        $client->setData($data);

        return new static($client);
    }

    /**
     * Authenticate and get auth token
     *
     * @throws Exception
     * @return Client
     */
    public function authenticate(): Client
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: The auth client does not have an HTTP client.');
        }

        $response = $this->client->send();

        if ($response->isSuccess()) {
            $this->loadTokenData($response->getParsedResponse());
        }
        return $this;
    }

    /**
     * Refresh auth token
     *
     * @return Client
     */
    public function refresh(): Client
    {
        $this->authToken  = null;
        $this->expiration = null;
        $this->tokenData  = [];

        $this->authenticate();

        return $this;
    }

}
