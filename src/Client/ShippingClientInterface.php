<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Client;

use Pop\Http\Client;

/**
 * Pop shipping client interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface ShippingClientInterface
{

    /**
     * Set the HTTP client
     *
     * @param  Client $client
     * @return ShippingClientInterface
     */
    public function setClient(Client $client): ShippingClientInterface;

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
     * Get API URL
     *
     * @return ?string
     */
    public function getApiUrl(): ?string;

    /**
     * Set as production
     *
     * @param  bool $prod
     * @return AbstractShippingClient
     */
    public function setProduction(bool $prod): ShippingClientInterface;

    /**
     * Is production
     *
     * @return bool
     */
    public function isProduction(): bool;

    /**
     * Set production API URL
     *
     * @param  string $prodApiUrl
     * @return ShippingClientInterface
     */
    public function setProdApiUrl(string $prodApiUrl): ShippingClientInterface;

    /**
     * Set test API URL
     *
     * @param  string $testApiUrl
     * @return ShippingClientInterface
     */
    public function setTestApiUrl(string $testApiUrl): ShippingClientInterface;

    /**
     * Get production API URL
     *
     * @return ?string
     */
    public function getProdApiUrl(): ?string;

    /**
     * Get test API URL
     *
     * @return ?string
     */
    public function getTestApiUrl(): ?string;

    /**
     * Has production API URL
     *
     * @return bool
     */
    public function hasProdApiUrl(): bool;

    /**
     * Has test API URL
     *
     * @return bool
     */
    public function hasTestApiUrl(): bool;

}
