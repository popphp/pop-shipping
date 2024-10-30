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
use Pop\Shipping\Package;

/**
 * Pop shipping adapter abstract class
 *
 * @category   Pop
 * @package    Pop\AbstractAdapter
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
     * User-Agent
     * @var string
     */
    protected string $userAgent = 'popphp/pop-shipping 3.0.0';

    /**
     * AbstractAdapter packages
     * @var Package[]
     */
    protected array $packages = [];

    /**
     * AbstractAdapter tracking numbers
     * @var array
     */
    protected array $trackingNumbers = [];

    /**
     * Ship to fields
     * @var array
     */
    protected array $shipTo = [];

    /**
     * Ship from fields
     * @var array
     */
    protected array $shipFrom = [];

    /**
     * API response
     * @var mixed
     */
    protected mixed $response = null;

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
        $adapter->setProduction($prod);

        $client = new Http\Client(
            Http\Auth::createBearer($authToken),
            [
                'base_uri' => $adapter->getApiUrl(),
                'type'     => 'application/json'
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
     * Set user-agent
     *
     * @param  string $userAgent
     * @return AbstractAdapter
     */
    public function setUserAgent(string $userAgent): AbstractAdapter
    {
        $this->userAgent = $userAgent;
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
     * Get user-agent
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
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

    /**
     * Add shipping packages
     *
     * @param  array $packages
     * @return AbstractAdapter
     */
    public function addPackages(array $packages): AbstractAdapter
    {
        foreach ($packages as $package) {
            $this->addPackage($package);
        }
        return $this;
    }

    /**
     * Add shipping package
     *
     * @param  Package $package
     * @return AbstractAdapter
     */
    public function addPackage(Package $package): AbstractAdapter
    {
        if (!$package->hasId()) {
            $package->setId(uniqid());
        }
        $this->packages[$package->getId()] = $package;
        return $this;
    }

    /**
     * Get shipping packages
     *
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * Get shipping package
     *
     * @param  mixed $id
     * @return ?Package
     */
    public function getPackage(mixed $id): ?Package
    {
        return $this->packages[$id] ?? null;
    }

    /**
     * Has shipping packages
     *
     * @return bool
     */
    public function hasPackages(): bool
    {
        return (!empty($this->packages));
    }

    /**
     * Has shipping package
     *
     * @param  mixed $id
     * @return bool
     */
    public function hasPackage(mixed $id): bool
    {
        return (isset($this->packages[$id]));
    }

    /**
     * Add shipping tracking numbers
     *
     * @param  array $trackingNumbers
     * @return AbstractAdapter
     */
    public function addTrackingNumbers(array $trackingNumbers): AbstractAdapter
    {
        foreach ($trackingNumbers as $trackingNumber) {
            $this->addTrackingNumber($trackingNumber);
        }
        return $this;
    }

    /**
     * Add shipping tracking number
     *
     * @param  string $trackingNumber
     * @return AbstractAdapter
     */
    public function addTrackingNumber(string $trackingNumber): AbstractAdapter
    {
        if (str_contains($trackingNumber, ',')) {
            $trackingNumbers = array_filter(array_map('trim', explode(',', $trackingNumber)));
        } else {
            $trackingNumbers[] = $trackingNumber;
        }

        foreach ($trackingNumbers as $trackingNumber) {
            if (!in_array($trackingNumber, $this->trackingNumbers)) {
                $this->trackingNumbers[] = $trackingNumber;
            }
        }

        return $this;
    }

    /**
     * Get shipping packages
     *
     * @return array
     */
    public function getTrackingNumbers(): array
    {
        return $this->trackingNumbers;
    }

    /**
     * Has shipping tracking numbers
     *
     * @return bool
     */
    public function hasTrackingNumbers(): bool
    {
        return (!empty($this->trackingNumbers));
    }

    /**
     * Has shipping tracking number
     *
     * @param  string $trackingNumber
     * @return bool
     */
    public function hasTrackingNumber(string $trackingNumber): bool
    {
        return (in_array($trackingNumber, $this->trackingNumbers));
    }

    /**
     * Set ship to
     *
     * @param  array $shipTo
     * @return AbstractAdapter
     */
    public function setShipTo(array $shipTo): AbstractAdapter
    {
        $this->shipTo = $shipTo;
        return $this;
    }

    /**
     * Get ship to
     *
     * @return array
     */
    public function getShipTo(): array
    {
        return $this->shipTo;
    }

    /**
     * Has ship to
     *
     * @return bool
     */
    public function hasShipTo(): bool
    {
        return !empty($this->shipTo);
    }

    /**
     * Set ship from
     *
     * @param  array $shipFrom
     * @return AbstractAdapter
     */
    public function setShipFrom(array $shipFrom): AbstractAdapter
    {
        $this->shipFrom = $shipFrom;
        return $this;
    }

    /**
     * Get ship from
     *
     * @return array
     */
    public function getShipFrom(): array
    {
        return $this->shipFrom;
    }

    /**
     * Has ship from
     *
     * @return bool
     */
    public function hasShipFrom(): bool
    {
        return !empty($this->shipFrom);
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse(): mixed
    {
        return $this->response;
    }

    /**
     * Has response
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return !empty($this->response);
    }

    /**
     * Get rates
     *
     * @return mixed
     */
    abstract public function getRates(): mixed;

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @return mixed
     */
    abstract public function getTracking(string|array|null $trackingNumbers = null): mixed;

    /**
     * Validate address
     *
     * @param  mixed $address
     * @return mixed
     */
    abstract public function validateAddress(mixed $address = null): mixed;

}
