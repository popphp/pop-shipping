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
use Pop\Shipping\Client\AbstractShippingClient;

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
abstract class AbstractAdapter extends AbstractShippingClient implements AdapterInterface
{

    /**
     * Rates API URL
     * @var ?string
     */
    protected ?string $ratesApiUrl = null;

    /**
     * Tracking API URL
     * @var ?string
     */
    protected ?string $trackingApiUrl = null;

    /**
     * Create shipping adapter
     *
     * @param  string  $authToken
     * @param  bool    $prod
     * @return static
     */
    public static function createAdapter(string $authToken, bool $prod = false): static
    {
        $adapter = new static();
        $client  = new Http\Client(
            Http\Auth::createBearer($authToken),
            [
                'base_uri' => (($prod) ? $adapter->getProdApiUrl() : $adapter->getTestApiUrl())
            ]
        );

        $adapter->setClient($client);

        return $adapter;
    }

    /**
     * Set rates API URL
     *
     * @param  string $ratesApiUrl
     * @return AbstractAdapter
     */
    public function setRatesApiUrl(string $ratesApiUrl): AbstractAdapter
    {
        $this->ratesApiUrl = $ratesApiUrl;
        return $this;
    }

    /**
     * Set tracking API URL
     *
     * @param  string $trackingApiUrl
     * @return AbstractAdapter
     */
    public function setTrackingApiUrl(string $trackingApiUrl): AbstractAdapter
    {
        $this->trackingApiUrl = $trackingApiUrl;
        return $this;
    }

    /**
     * Get rates API URL
     *
     * @return ?string
     */
    public function getRatesApiUrl(): ?string
    {
        return $this->ratesApiUrl;
    }

    /**
     * Get tracking API URL
     *
     * @return ?string
     */
    public function getTrackingApiUrl(): ?string
    {
        return $this->trackingApiUrl;
    }

    /**
     * Has rates API URL
     *
     * @return bool
     */
    public function hasRatesApiUrl(): bool
    {
        return !empty($this->ratesApiUrl);
    }

    /**
     * Has tracking API URL
     *
     * @return bool
     */
    public function hasTrackingApiUrl(): bool
    {
        return !empty($this->trackingApiUrl);
    }

}
