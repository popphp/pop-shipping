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
interface AuthClientInterface
{

    /**
     * Get token data
     *
     * @param  ?string $key
     * @return mixed
     */
    public function getTokenData(?string $key = null): mixed;

    /**
     * Has token data
     *
     * @return bool
     */
    public function hasTokenData(): bool;

    /**
     * Load token data
     *
     * @param  array $tokenData
     * @return AuthClientInterface
     */
    public function loadTokenData(array $tokenData): AuthClientInterface;

    /**
     * Load token data from file
     *
     * @param  string $tokenFile
     * @return AuthClientInterface
     */
    public function loadTokenDataFromFile(string $tokenFile): AuthClientInterface;

    /**
     * Save token data to file
     *
     * @param  string $tokenFile
     * @param  ?array $tokenData
     * @return AuthClientInterface
     */
    public function saveTokenDataToFile(string $tokenFile, ?array $tokenData = null): AuthClientInterface;

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
     * Fetch auth token, either the current valid one, or get a new/refreshed auth token
     *
     * @param  int $buffer    Buffer in seconds to check the expiration
     * @return ?string
     */
    public function fetchAuthToken(int $buffer = 10): ?string;

    /**
     * Has auth token type
     *
     * @return bool
     */
    public function hasTokenType(): bool;

    /**
     * Get auth token type
     *
     * @return ?string
     */
    public function getTokenType(): ?string;

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
     * @param  ?string $tokenFile
     * @return AuthClientInterface
     */
    public function authenticate(?string $tokenFile = null): AuthClientInterface;

    /**
     * Refresh auth token
     *
     * @param  ?string $tokenFile
     * @return AuthClientInterface
     */
    public function refresh(?string $tokenFile = null): AuthClientInterface;

}
