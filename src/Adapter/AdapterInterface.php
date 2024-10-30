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

use Pop\Shipping\Package;

/**
 * Pop shipping adapter interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
interface AdapterInterface
{

    /**
     * Set rates API URL
     *
     * @param  string $ratesApiUrl
     * @return AdapterInterface
     */
    public function setRatesApiUrl(string $ratesApiUrl): AdapterInterface;

    /**
     * Set tracking API URL
     *
     * @param  string $trackingApiUrl
     * @return AdapterInterface
     */
    public function setTrackingApiUrl(string $trackingApiUrl): AdapterInterface;

    /**
     * Set user-agent
     *
     * @param  string $userAgent
     * @return AdapterInterface
     */
    public function setUserAgent(string $userAgent): AdapterInterface;

    /**
     * Get rates API URL
     *
     * @return ?string
     */
    public function getRatesApiUrl(): ?string;

    /**
     * Get tracking API URL
     *
     * @return ?string
     */
    public function getTrackingApiUrl(): ?string;

    /**
     * Get user-agent
     *
     * @return string
     */
    public function getUserAgent(): string;

    /**
     * Has rates API URL
     *
     * @return bool
     */
    public function hasRatesApiUrl(): bool;

    /**
     * Has tracking API URL
     *
     * @return bool
     */
    public function hasTrackingApiUrl(): bool;

    /**
     * Add shipping packages
     *
     * @param  array $packages
     * @return AdapterInterface
     */
    public function addPackages(array $packages): AdapterInterface;

    /**
     * Add shipping package
     *
     * @param  Package $package
     * @return AdapterInterface
     */
    public function addPackage(Package $package): AdapterInterface;

    /**
     * Get shipping packages
     *
     * @return array
     */
    public function getPackages(): array;

    /**
     * Get shipping package
     *
     * @param  mixed $id
     * @return ?Package
     */
    public function getPackage(mixed $id): ?Package;

    /**
     * Has shipping packages
     *
     * @return bool
     */
    public function hasPackages(): bool;

    /**
     * Has shipping package
     *
     * @param  mixed $id
     * @return bool
     */
    public function hasPackage(mixed $id): bool;

    /**
     * Add shipping tracking numbers
     *
     * @param  array $trackingNumbers
     * @return AdapterInterface
     */
    public function addTrackingNumbers(array $trackingNumbers): AdapterInterface;

    /**
     * Add shipping tracking number
     *
     * @param  string $trackingNumber
     * @return AdapterInterface
     */
    public function addTrackingNumber(string $trackingNumber): AdapterInterface;

    /**
     * Get shipping packages
     *
     * @return array
     */
    public function getTrackingNumbers(): array;

    /**
     * Has shipping tracking numbers
     *
     * @return bool
     */
    public function hasTrackingNumbers(): bool;

    /**
     * Has shipping tracking number
     *
     * @param  string $trackingNumber
     * @return bool
     */
    public function hasTrackingNumber(string $trackingNumber): bool;

    /**
     * Set ship to
     *
     * @param  array $shipTo
     * @return AbstractAdapter
     */
    public function setShipTo(array $shipTo): AbstractAdapter;

    /**
     * Get ship to
     *
     * @return array
     */
    public function getShipTo(): array;

    /**
     * Has ship to
     *
     * @return bool
     */
    public function hasShipTo(): bool;

    /**
     * Set ship from
     *
     * @param  array $shipFrom
     * @return AbstractAdapter
     */
    public function setShipFrom(array $shipFrom): AbstractAdapter;

    /**
     * Get ship from
     *
     * @return array
     */
    public function getShipFrom(): array;

    /**
     * Has ship from
     *
     * @return bool
     */
    public function hasShipFrom(): bool;

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse(): mixed;

    /**
     * Has response
     *
     * @return bool
     */
    public function hasResponse(): bool;

    /**
     * Get rates
     *
     * @return AdapterInterface
     */
    public function getRates(): AdapterInterface;

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @return AdapterInterface
     */
    public function getTracking(string|array|null $trackingNumbers = null): AdapterInterface;

}
