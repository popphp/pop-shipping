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
     * Authenticate and get auth token
     *
     * @return AbstractClient
     */
    public function authenticate(): Client
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: The auth client does not have an HTTP client.');
        }

        $response = $this->client->send();

        if ($response->isSuccess()) {
            $this->loadToken($response->getParsedResponse());
        }
        return $this;
    }

    /**
     * Refresh auth token
     *
     * @return AbstractClient
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
