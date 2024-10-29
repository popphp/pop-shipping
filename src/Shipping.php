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
namespace Pop\Shipping;

/**
 * Pop shipping class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Shipping
{

    /**
     * Shipping Auth Client
     * @var ?Auth\Client
     */
    protected ?Auth\Client $authClient = null;

    /**
     * Shipping Adapter
     * @var ?Adapter\AbstractAdapter
     */
    protected ?Adapter\AbstractAdapter $adapter = null;

    /**
     * Shipping packages
     * @var Package[]
     */
    protected array $packages = [];

    /**
     * Shipping tracking numbers
     * @var array
     */
    protected array $trackingNumbers = [];

    /**
     * Constructor
     *
     * Instantiate the shipping object
     *
     */
    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $i => $arg) {
            if ($arg instanceof \Pop\Shipping\Auth\Client) {
                $this->setAuthClient($arg);
            } else if ($arg instanceof \Pop\Shipping\Adapter\AbstractAdapter) {
                $this->setAdapter($arg);
            } else if ($arg instanceof \Pop\Shipping\Package) {
                $this->addPackage($arg);
            } else  if (is_string($arg)) {
                $this->addTrackingNumber($arg);
            } else if (is_array($arg)) {
                foreach ($arg as $a) {
                    if ($a instanceof \Pop\Shipping\Package) {
                        $this->addPackage($a);
                    } else if (is_string($a)) {
                        $this->addTrackingNumber($a);
                    }
                }
            }
        }
    }

    /**
     * Set shipping auth client
     *
     * @param  Auth\Client $authClient
     * @return Shipping
     */
    public function setAuthClient(Auth\Client $authClient): Shipping
    {
        $this->authClient = $authClient;
        return $this;
    }

    /**
     * Get shipping auth client
     *
     * @return Auth\Client
     */
    public function getAuthClient(): Auth\Client
    {
        return $this->authClient;
    }

    /**
     * Has shipping auth client
     *
     * @return bool
     */
    public function hasAuthClient(): bool
    {
        return (!empty($this->authClient));
    }

    /**
     * Set shipping adapter
     *
     * @param  Adapter\AbstractAdapter $adapter
     * @return Shipping
     */
    public function setAdapter(Adapter\AbstractAdapter $adapter): Shipping
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Get shipping adapter
     *
     * @return Adapter\AbstractAdapter
     */
    public function getAdapter(): Adapter\AbstractAdapter
    {
        return $this->adapter;
    }

    /**
     * Has shipping adapter
     *
     * @return bool
     */
    public function hasAdapter(): bool
    {
        return (!empty($this->adapter));
    }

    /**
     * Add shipping packages
     *
     * @param  array $packages
     * @return Shipping
     */
    public function addPackages(array $packages): Shipping
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
     * @return Shipping
     */
    public function addPackage(Package $package): Shipping
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
     * @return Shipping
     */
    public function addTrackingNumbers(array $trackingNumbers): Shipping
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
     * @return Shipping
     */
    public function addTrackingNumber(string $trackingNumber): Shipping
    {
        if (!in_array($trackingNumber, $this->trackingNumbers)) {
            $this->trackingNumbers[] = $trackingNumber;
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

}
