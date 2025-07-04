<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Shipping\Client;

use Pop\Http\Client;

/**
 * Pop shipping client abstract class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractShippingClient implements ShippingClientInterface
{

    /**
     * API URL Constants
     */
    const FEDEX_PROD_API_URL = 'https://apis.fedex.com';
    const FEDEX_TEST_API_URL = 'https://apis-sandbox.fedex.com';
    const UPS_PROD_API_URL   = 'https://onlinetools.ups.com';
    const UPS_TEST_API_URL   = 'https://wwwcie.ups.com';
    const USPS_PROD_API_URL  = 'https://api.usps.com';
    const USPS_TEST_API_URL  = 'https://api-cat.usps.com';

    /**
     * HTTP client
     * @var ?Client
     */
    protected ?Client $client = null;

    /**
     * Is production flag
     * @var bool
     */
    protected bool $isProd = false;

    /**
     * Production API URL
     * @var ?string
     */
    protected ?string $prodApiUrl = null;

    /**
     * Test API URL
     * @var ?string
     */
    protected ?string $testApiUrl = null;

    /**
     * Constructor
     *
     * Instantiate the shipping auth object
     */
    public function __construct(?Client $client = null)
    {
        if ($client !== null) {
            $this->setClient($client);
        }
    }

    /**
     * Set the HTTP client
     *
     * @param  Client $client
     * @return AbstractShippingClient
     */
    public function setClient(Client $client): AbstractShippingClient
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
     * Get API URL
     *
     * @return ?string
     */
    public function getApiUrl(): ?string
    {
        return ($this->isProduction()) ? $this->prodApiUrl : $this->testApiUrl;
    }

    /**
     * Set as production
     *
     * @param  bool $prod
     * @return AbstractShippingClient
     */
    public function setProduction(bool $prod): AbstractShippingClient
    {
        $this->isProd = $prod;
        return $this;
    }

    /**
     * Is production
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->isProd;
    }

    /**
     * Set production API URL
     *
     * @param  string $prodApiUrl
     * @return AbstractShippingClient
     */
    public function setProdApiUrl(string $prodApiUrl): AbstractShippingClient
    {
        $this->prodApiUrl = $prodApiUrl;
        return $this;
    }

    /**
     * Set test API URL
     *
     * @param  string $testApiUrl
     * @return AbstractShippingClient
     */
    public function setTestApiUrl(string $testApiUrl): AbstractShippingClient
    {
        $this->testApiUrl = $testApiUrl;
        return $this;
    }

    /**
     * Get production API URL
     *
     * @return ?string
     */
    public function getProdApiUrl(): ?string
    {
        return $this->prodApiUrl;
    }

    /**
     * Get test API URL
     *
     * @return ?string
     */
    public function getTestApiUrl(): ?string
    {
        return $this->testApiUrl;
    }

    /**
     * Has production API URL
     *
     * @return bool
     */
    public function hasProdApiUrl(): bool
    {
        return !empty($this->prodApiUrl);
    }

    /**
     * Has test API URL
     *
     * @return bool
     */
    public function hasTestApiUrl(): bool
    {
        return !empty($this->testApiUrl);
    }

}
