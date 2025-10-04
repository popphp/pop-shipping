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
namespace Pop\Shipping;

/**
 * Pop shipping class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
     * @param  array|Address $shipTo
     * @return Shipping
     */
    public function setShipTo(array|Address $shipTo): Shipping
    {
        $this->adapter?->setShipTo($shipTo);
        return $this;
    }

    /**
     * Get ship to
     *
     * @return Address
     */
    public function getShipTo(): Address
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
     * @param  array|Address $shipFrom
     * @return Shipping
     */
    public function setShipFrom(array|Address $shipFrom): Shipping
    {
        $this->adapter?->setShipFrom($shipFrom);
        return $this;
    }

    /**
     * Get ship from
     *
     * @return Address
     */
    public function getShipFrom(): Address
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
     * Is success
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->adapter?->isSuccess();
    }

    /**
     * Get error code
     *
     * @return mixed
     */
    public function getErrorCode(): mixed
    {
        return $this->adapter?->getErrorCode();
    }

    /**
     * Has error code
     *
     * @return bool
     */
    public function hasErrorCode(): bool
    {
        return $this->adapter?->hasErrorCode();
    }

    /**
     * Get error message
     *
     * @return mixed
     */
    public function getErrorMessage(): mixed
    {
        return $this->adapter?->getErrorMessage();
    }

    /**
     * Has error message
     *
     * @return bool
     */
    public function hasErrorMessage(): bool
    {
        return $this->adapter?->hasErrorMessage();
    }

    /**
     * Get rates
     *
     * @return array
     */
    public function getRates(): array
    {
        return $this->adapter?->getRates();
    }

    /**
     * Has rates
     *
     * @return bool
     */
    public function hasRates(): bool
    {
        return $this->adapter?->hasRates();
    }

    /**
     * Fetch rates
     *
     * @return mixed
     */
    public function fetchRates(): mixed
    {
        return $this->adapter?->fetchRates();
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

}
