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
     * Shipping Adapter
     * @var ?Adapter\AbstractAdapter
     */
    protected ?Adapter\AbstractAdapter $adapter = null;

    /**
     * Constructor
     *
     * Instantiate the shipping object
     *
     */
    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            if ($arg instanceof Adapter\AbstractAdapter) {
                $this->setAdapter($arg);
            } else if ($arg instanceof Package) {
                $this->addPackage($arg);
            } else  if (is_string($arg)) {
                $this->addTrackingNumber($arg);
            } else if (is_array($arg)) {
                foreach ($arg as $a) {
                    if ($a instanceof Package) {
                        $this->addPackage($a);
                    } else if (is_string($a)) {
                        $this->addTrackingNumber($a);
                    }
                }
            }
        }
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
        $this->adapter?->addPackages($packages);
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
        $this->adapter?->addPackage($package);
        return $this;
    }

    /**
     * Get shipping packages
     *
     * @return array
     */
    public function getPackages(): array
    {
        return $this->adapter?->getPackages();
    }

    /**
     * Get shipping package
     *
     * @param  mixed $id
     * @return ?Package
     */
    public function getPackage(mixed $id): ?Package
    {
        return $this->adapter?->getPackage($id);
    }

    /**
     * Has shipping packages
     *
     * @return bool
     */
    public function hasPackages(): bool
    {
        return $this->adapter?->hasPackages();
    }

    /**
     * Has shipping package
     *
     * @param  mixed $id
     * @return bool
     */
    public function hasPackage(mixed $id): bool
    {
        return $this->adapter?->hasPackage($id);
    }

    /**
     * Add shipping tracking numbers
     *
     * @param  array $trackingNumbers
     * @return Shipping
     */
    public function addTrackingNumbers(array $trackingNumbers): Shipping
    {
        $this->adapter?->addTrackingNumbers($trackingNumbers);
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
        $this->adapter?->addTrackingNumber($trackingNumber);
        return $this;
    }

    /**
     * Get shipping packages
     *
     * @return array
     */
    public function getTrackingNumbers(): array
    {
        return $this->adapter?->getTrackingNumbers();
    }

    /**
     * Has shipping tracking numbers
     *
     * @return bool
     */
    public function hasTrackingNumbers(): bool
    {
        return $this->adapter?->hasTrackingNumbers();
    }

    /**
     * Has shipping tracking number
     *
     * @param  string $trackingNumber
     * @return bool
     */
    public function hasTrackingNumber(string $trackingNumber): bool
    {
        return $this->adapter?->hasTrackingNumber($trackingNumber);
    }

    /**
     * Set ship to
     *
     * @param  array $shipTo
     * @return Shipping
     */
    public function setShipTo(array $shipTo): Shipping
    {
        $this->adapter?->setShipTo($shipTo);
        return $this;
    }

    /**
     * Get ship to
     *
     * @return array
     */
    public function getShipTo(): array
    {
        return $this->adapter?->getShipTo();
    }

    /**
     * Has ship to
     *
     * @return bool
     */
    public function hasShipTo(): bool
    {
        return $this->adapter?->hasShipTo();
    }

    /**
     * Set ship from
     *
     * @param  array $shipFrom
     * @return Shipping
     */
    public function setShipFrom(array $shipFrom): Shipping
    {
        $this->adapter?->setShipFrom($shipFrom);
        return $this;
    }

    /**
     * Get ship from
     *
     * @return array
     */
    public function getShipFrom(): array
    {
        return $this->adapter?->getShipFrom();
    }

    /**
     * Has ship from
     *
     * @return bool
     */
    public function hasShipFrom(): bool
    {
        return $this->adapter?->hasShipFrom();
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse(): mixed
    {
        return $this->adapter?->getResponse();
    }

    /**
     * Has response
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return $this->adapter?->hasResponse();
    }

    /**
     * Get rates
     *
     * @return mixed
     */
    public function getRates(): mixed
    {
        return $this->adapter?->getRates();
    }

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @return mixed
     */
    public function getTracking(string|array|null $trackingNumbers = null): mixed
    {
        return $this->adapter?->getTracking($trackingNumbers);
    }

    /**
     * Validate address
     *
     * @param  mixed $address  An array of address data or a string with containing "from" to indicate
     *                         using the ship-from address. If empty, will default to the ship-to address
     * @return mixed
     */
    public function validateAddress(mixed $address = null): mixed
    {
        return $this->adapter?->validateAddress($address);
    }

}
