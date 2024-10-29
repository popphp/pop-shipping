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
namespace Pop\Shipping\Adapter;

use Pop\Http;

/**
 * Pop shipping adapter abstract class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractAdapter implements AdapterInterface
{

    /**
     * HTTP client
     * @var ?Http\Client
     */
    protected ?Http\Client $client = null;

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
     * Create shipping adapter
     *
     * @param  string  $baseApiUrl
     * @param  string  $authToken
     * @param  string  $method
     * @return static
     */
    public static function createAdapter(string $baseApiUrl, string $authToken, string $method = 'POST'): static
    {
        return new static(new Http\Client(Http\Auth::createBearer($authToken), ['base_uri' => $baseApiUrl, 'method' => $method]));
    }

    /**
     * Set the HTTP client
     *
     * @param  Http\Client $client
     * @return AbstractAdapter
     */
    public function setClient(Http\Client $client): AbstractAdapter
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the HTTP client
     *
     * @return ?Http\Client
     */
    public function getClient(): ?Http\Client
    {
        return $this->client;
    }

    /**
     * Has HTTP client
     *
     * @return bool
     */
    public function hasClient(): bool
    {
        return ($this->client !== null);
    }

}
