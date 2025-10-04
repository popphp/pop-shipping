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
namespace Pop\Shipping\Adapter;

use Pop\Http;
use Pop\Shipping\Address;
use Pop\Shipping\Auth\AbstractAuthClient;
use Pop\Shipping\Client\AbstractShippingClient;
use Pop\Shipping\Package;

/**
 * Pop shipping adapter abstract class
 *
 * @category   Pop
 * @package    Pop\AbstractAdapter
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
abstract class AbstractAdapter extends AbstractShippingClient implements AdapterInterface
{

    /**
     * Auth clientL
     * @var ?AbstractAuthClient
     */
    protected ?AbstractAuthClient $authClient = null;

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
     * Ship type (Drop-off, Pick-up, etc.)
     * @var ?string
     */
    protected ?string $shipType = null;

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
     *
     * @var ?Address
     */
    protected ?Address $shipTo = null;

    /**
     * Ship from fields
     *
     * @var ?Address
     */
    protected ?Address $shipFrom = null;

    /**
     * API response
     * @var mixed
     */
    protected mixed $response = null;

    /**
     * Rates
     * @var array
     */
    protected array $rates = [];

    /**
     * API error code
     * @var mixed
     */
    protected mixed $errorCode = null;

    /**
     * API error message
     * @var mixed
     */
    protected mixed $errorMessage = null;

    /**
     * Create shipping adapter
     *
     * @param  AbstractAuthClient $authClient
     * @return static
     */
    public static function createAdapter(AbstractAuthClient $authClient): static
    {
        $adapter = new static();
        $adapter->setAuthClient($authClient)
                ->setProduction($authClient->isProduction());

        if ($authClient->hasAuthToken()) {
            $adapter->setClient(new Http\Client(
                Http\Auth::createBearer($authClient->getAuthToken()),
                [
                    'base_uri' => $adapter->getApiUrl(),
                    'type'     => 'application/json'
                ]
            ));
        }

        return $adapter;
    }

    /**
     * Set auth client
     *
     * @param  AbstractAuthClient $authClient
     * @return AbstractAdapter
     */
    public function setAuthClient(AbstractAuthClient $authClient): AbstractAdapter
    {
        $this->authClient = $authClient;
        return $this;
    }

    /**
     * Set ship type
     *
     * @param  string $shipType
     * @return AbstractAdapter
     */
    public function setShipType(string $shipType): AbstractAdapter
    {
        $this->shipType = $shipType;
        return $this;
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
     * Get auth client
     *
     * @return ?AbstractAuthClient
     */
    public function getAuthClient(): ?AbstractAuthClient
    {
        return $this->authClient;
    }

    /**
     * Get ship type
     *
     * @return ?string
     */
    public function getShipType(): ?string
    {
        return $this->shipType;
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
     * Has auth client
     *
     * @return bool
     */
    public function hasAuthClient(): bool
    {
        return !empty($this->authClient);
    }

    /**
     * Has ship type
     *
     * @return bool
     */
    public function hasShipType(): bool
    {
        return !empty($this->shipType);
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
     * @param  array|Address $shipTo
     * @return AbstractAdapter
     */
    public function setShipTo(array|Address $shipTo): AbstractAdapter
    {
        $this->shipTo = !($shipTo instanceof Address) ? new Address($shipTo) : $shipTo;
        return $this;
    }

    /**
     * Get ship to
     *
     * @return Address
     */
    public function getShipTo(): Address
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
        return ($this->shipTo !== null);
    }

    /**
     * Set ship from
     *
     * @param  array|Address $shipFrom
     * @return AbstractAdapter
     */
    public function setShipFrom(array|Address $shipFrom): AbstractAdapter
    {
        $this->shipFrom = !($shipFrom instanceof Address) ? new Address($shipFrom) : $shipFrom;
        return $this;
    }

    /**
     * Get ship from
     *
     * @return Address
     */
    public function getShipFrom(): Address
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
        return ($this->shipFrom !== null);
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
     * Is success
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return (!empty($this->rates) && empty($this->errorCode));
    }

    /**
     * Get error code
     *
     * @return mixed
     */
    public function getErrorCode(): mixed
    {
        return $this->errorCode;
    }

    /**
     * Has error code
     *
     * @return bool
     */
    public function hasErrorCode(): bool
    {
        return (!empty($this->errorCode));
    }

    /**
     * Get error message
     *
     * @return mixed
     */
    public function getErrorMessage(): mixed
    {
        return $this->errorMessage;
    }

    /**
     * Has error message
     *
     * @return bool
     */
    public function hasErrorMessage(): bool
    {
        return (!empty($this->errorMessage));
    }

    /**
     * Get rates
     *
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * Has rates
     *
     * @return bool
     */
    public function hasRates(): bool
    {
        return (!empty($this->rates));
    }

    /**
     * Fetch rates from the API
     *
     * @return array
     */
    abstract public function fetchRates(): array;

    /**
     * Parse rates response
     *
     * @return mixed
     */
    abstract public function parseRatesResponse(): array;

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @return array
     */
    abstract public function getTracking(string|array|null $trackingNumbers = null): array;

    /**
     * Parse tracking response
     *
     * @return mixed
     */
    abstract public function parseTrackingResponse(): array;

}
