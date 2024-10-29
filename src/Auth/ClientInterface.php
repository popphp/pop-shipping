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
 * Pop shipping auth client interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface ClientInterface
{

    /**
     * Set the HTTP client
     *
     * @param  Client $client
     * @return ClientInterface
     */
    public function setClient(Client $client): ClientInterface;

    /**
     * Get the HTTP client
     *
     * @return ?Client
     */
    public function getClient(): ?Client;

    /**
     * Has HTTP client
     *
     * @return bool
     */
    public function hasClient(): bool;

    /**
     * Load token data
     *
     * @param  array $tokenData
     * @return ClientInterface
     */
    public function loadToken(array $tokenData): ClientInterface;

    /**
     * Load token data from file
     *
     * @param  string $tokenFile
     * @return ClientInterface
     */
    public function loadTokenFromFile(string $tokenFile): ClientInterface;

    /**
     * Save token data to file
     *
     * @param  string $tokenFile
     * @return ClientInterface
     */
    public function saveTokenToFile(string $tokenFile): ClientInterface;

    /**
     * Has valid auth token
     *
     * @return bool
     */
    public function hasAuthToken(): bool;

    /**
     * Get auth token
     *
     * @return ?string
     */
    public function getAuthToken(): ?string;

    /**
     * Has auth token expiration
     *
     * @return bool
     */
    public function hasExpiration(): bool;

    /**
     * Get auth token expiration
     *
     * @return ?string
     */
    public function getExpiration(): ?int;

    /**
     * Determine if the auth token has expired
     *
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * Determine when the token will expire in seconds
     *
     * @return int
     */
    public function willExpireIn(): int;

    /**
     * Authenticate and get auth token
     *
     * @return ClientInterface
     */
    public function authenticate(): ClientInterface;

    /**
     * Refresh auth token
     *
     * @return ClientInterface
     */
    public function refresh(): ClientInterface;

}
