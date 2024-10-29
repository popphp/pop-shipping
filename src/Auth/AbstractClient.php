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

use Pop\Http\Client;

/**
 * Pop shipping auth abstract client class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractClient implements ClientInterface
{

    /**
     * HTTP client
     * @var ?Client
     */
    protected ?Client $client = null;

    /**
     * Auth token
     * @var ?string
     */
    protected ?string $authToken = null;

    /**
     * Auth token expiration timestamp
     * @var ?int
     */
    protected ?int $expiration = null;

    /**
     * Auth token data
     * @var array
     */
    protected array $tokenData = [];

    /**
     * Set the HTTP client
     *
     * @param  Client $client
     * @return AbstractClient
     */
    public function setClient(Client $client): AbstractClient
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the HTTP client
     *
     * @return ?Client
     */
    public function getClient(): ?Client
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

    /**
     * Load token data
     *
     * @param  array $tokenData
     * @return AbstractClient
     */
    public function loadToken(array $tokenData): AbstractClient
    {
        $this->tokenData = $tokenData;
        return $this;
    }

    /**
     * Load token data from file
     *
     * @param  string $tokenFile
     * @return AbstractClient
     */
    public function loadTokenFromFile(string $tokenFile): AbstractClient
    {
        if (file_exists($tokenFile)) {
            $this->tokenData = json_decode(file_get_contents($tokenFile), true);
        }
        return $this;
    }

    /**
     * Save token data to file
     *
     * @param  string $tokenFile
     * @return ClientInterface
     */
    public function saveTokenToFile(string $tokenFile): AbstractClient
    {
        file_put_contents($tokenFile, $this->tokenData);
        return $this;
    }

    /**
     * Has valid auth token
     *
     * @return bool
     */
    public function hasAuthToken(): bool
    {
        return (($this->authToken !== null) && (!$this->isExpired()));
    }

    /**
     * Get auth token
     *
     * @return ?string
     */
    public function getAuthToken(): ?string
    {
        return ($this->hasAuthToken()) ? $this->authToken : null;
    }

    /**
     * Has auth token expiration
     *
     * @return bool
     */
    public function hasExpiration(): bool
    {
        return ($this->expiration !== null);
    }

    /**
     * Get auth token expiration
     *
     * @return ?int
     */
    public function getExpiration(): ?int
    {
        return $this->expiration;
    }

    /**
     * Determine if the auth token has expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return (($this->hasExpiration()) && (time() > $this->expiration));
    }

    /**
     * Determine when the token will expire in seconds
     *
     * @return int
     */
    public function willExpireIn(): int
    {
        return (($this->hasExpiration()) && (!$this->isExpired())) ? ($this->expiration - time()) : 0;
    }

    /**
     * Authenticate and get auth token
     *
     * @return AbstractClient
     */
    abstract public function authenticate(): AbstractClient;

    /**
     * Refresh auth token
     *
     * @return AbstractClient
     */
    abstract public function refresh(): AbstractClient;

}
