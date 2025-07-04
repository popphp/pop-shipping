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

use Pop\Shipping\Address;
use Pop\Shipping\Package;

/**
 * Pop shipping adapter interface
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
     * @param  array|Address $shipTo
     * @return AbstractAdapter
     */
    public function setShipTo(array|Address $shipTo): AbstractAdapter;

    /**
     * Get ship to
     *
     * @return ?Address
     */
    public function getShipTo(): ?Address;

    /**
     * Has ship to
     *
     * @return bool
     */
    public function hasShipTo(): bool;

    /**
     * Set ship from
     *
     * @param  array|Address $shipFrom
     * @return AbstractAdapter
     */
    public function setShipFrom(array|Address $shipFrom): AbstractAdapter;

    /**
     * Get ship from
     *
     * @return ?Address
     */
    public function getShipFrom(): ?Address;

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
     * @return array
     */
    public function getRates(): array;

    /**
     * Parse rates response
     *
     * @return mixed
     */
    public function parseRatesResponse(): array;

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @return array
     */
    public function getTracking(string|array|null $trackingNumbers = null): array;

    /**
     * Parse tracking response
     *
     * @return mixed
     */
    public function parseTrackingResponse(): array;

}
