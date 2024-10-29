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
namespace Pop\Shipping\Client;

use Pop\Http;

/**
 * Pop shipping client interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface ShippingClientInterface
{

    /**
     * Set the HTTP client
     *
     * @param  Http\Client $client
     * @return ShippingClientInterface
     */
    public function setClient(Http\Client $client): ShippingClientInterface;

    /**
     * Get the HTTP client
     *
     * @return ?Http\Client
     */
    public function getClient(): ?Http\Client;

    /**
     * Has HTTP client
     *
     * @return bool
     */
    public function hasClient(): bool;

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
