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
namespace Pop\Shipping\Adapter;

use Pop\Http;
use Pop\Shipping\Auth\AbstractAuthClient;
use Pop\Shipping\Client\AbstractShippingClient;
use Pop\Shipping\Package;

/**
 * Pop shipping adapter abstract class
 *
 * @category   Pop
 * @package    Pop\AbstractAdapter
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
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
     * Address API URL
     * @var ?string
     */
    protected ?string $addressApiUrl = null;

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
     * @var array
     */
    protected array $shipTo = [
        'first_name'  => null,
        'last_name'   => null,
        'company'     => null,
        'address1'    => null,
        'address2'    => null,
        'city'        => null,
        'state'       => null,
        'zip'         => null,
        'country'     => null,
        'phone'       => null,
        'residential' => false,
    ];

    /**
     * Ship from fields
     *
     * @var array
     */
    protected array $shipFrom = [
        'first_name'  => null,
        'last_name'   => null,
        'company'     => null,
        'address1'    => null,
        'address2'    => null,
        'city'        => null,
        'state'       => null,
        'zip'         => null,
        'country'     => null,
        'phone'       => null,
        'residential' => false,
    ];

    /**
     * API response
     * @var mixed
     */
    protected mixed $response = null;

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
     * Set ship type
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
     * Set address API URL
     *
     * @param  string $addressApiUrl
     * @return AbstractAdapter
     */
    public function setAddressApiUrl(string $addressApiUrl): AbstractAdapter
    {
        $this->addressApiUrl = $addressApiUrl;
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
     * Get address API URL
     *
     * @return ?string
     */
    public function getAddressApiUrl(): ?string
    {
        return $this->addressApiUrl;
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
     * Has address API URL
     *
     * @return bool
     */
    public function hasAddressApiUrl(): bool
    {
        return !empty($this->addressApiUrl);
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
        foreach ($shipTo as $key => $value) {
            if (array_key_exists($key, $this->shipTo)) {
                $this->shipTo[$key] = $value;
            }
        }

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
        foreach ($shipFrom as $key => $value) {
            if (array_key_exists($key, $this->shipFrom)) {
                $this->shipFrom[$key] = $value;
            }
        }

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
     * @return array
     */
    abstract public function getRates(): array;

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

    /**
     * Validate address
     *
     * @param  mixed $address
     * @return mixed
     */
    abstract public function validateAddress(mixed $address = null): mixed;

}
