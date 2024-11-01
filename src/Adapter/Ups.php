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

use Pop\Shipping\Client\AbstractShippingClient;

/**
 * Pop shipping UPS adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Ups extends AbstractAdapter
{

    /**
     * Production API URL
     * @var ?string
     */
    protected ?string $prodApiUrl = AbstractShippingClient::UPS_PROD_API_URL;

    /**
     * Test API URL
     * @var ?string
     */
    protected ?string $testApiUrl = AbstractShippingClient::UPS_TEST_API_URL;

    /**
     * Rates API URL
     * @var ?string
     */
    protected ?string $ratesApiUrl = '/api/rating/v2403/Rate'; // POST

    /**
     * Tracking API URL
     * @var ?string
     */
    protected ?string $trackingApiUrl = '/api/track/v1/details/'; // GET - requires the tracking number to be appended to the URL

    /**
     * Get rates
     *
     * @throws Exception
     * @return mixed
     */
    public function getRates(): mixed
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: There is no HTTP client for this shipping adapter.');
        }

        $shipper   = [];
        $recipient = [];
        $packages  = [];

        if (!empty($this->shipTo['address1'])) {
            $shipper['Address']['AddressLine'] = [$this->shipTo['address1']];
            if (!empty($this->shipTo['address2'])) {
                $shipper['Address']['AddressLine'] = $this->shipTo['address2'];
            }
        }
        if (!empty($this->shipTo['city'])) {
            $shipper['Address']['City'] = $this->shipTo['city'];
        }
        if (!empty($this->shipTo['state'])) {
            $shipper['Address']['StateProvinceCode'] = $this->shipTo['state'];
        }
        $shipper['Address']['PostalCode']  = $this->shipTo['zip'];
        $shipper['Address']['CountryCode'] = $this->shipTo['countryCode'] ?? 'US';

        if (!empty($this->shipFrom['address1'])) {
            $recipient['Address']['AddressLine'] = [$this->shipFrom['address1']];
            if (!empty($this->shipFrom['address2'])) {
                $recipient['Address']['AddressLine'] = $this->shipFrom['address2'];
            }
        }
        if (!empty($this->shipFrom['city'])) {
            $recipient['Address']['City'] = $this->shipFrom['city'];
        }
        if (!empty($this->shipFrom['state'])) {
            $recipient['Address']['StateProvinceCode'] = $this->shipFrom['state'];
        }
        $recipient['Address']['PostalCode']  = $this->shipFrom['zip'];
        $recipient['Address']['CountryCode'] = $this->shipFrom['countryCode'] ?? 'US';

        foreach ($this->packages as $package) {
            $pkg = [
                'weight' => [
                    'value' => $package->getWeight(),
                    'units'  => $package->getWeightUnit()
                ],
                'dimensions' => [
                    'width'  => $package->getWidth(),
                    'height' => $package->getHeight(),
                    'length' => $package->getDepth(),
                    'units'  => $package->getDimensionUnit()
                ]
            ];

            if ($package->hasValue()) {
                $pkg['declaredValue'] = [
                    'amount'   => $package->getValue(),
                    'currency' => $package->getValueUnit()
                ];
            }

            $packages[] = $pkg;
        }

        $data = [
            'RateRequest' => [
                'Request' => [
                    'RequestOption' => 'RATE'
                ],
                'PickupType' => $this->shipType ?? '06',
                'Shipment'   => [
                    'Shipper'     => $shipper,
                    'ShipTo'      => $recipient,
                    'ShipFrom'    => $shipper,
                    'NumOfPieces' => count($this->packages),
                    'Package'     => $packages
                ]
            ]
        ];

        $transSource = $this->isProd ? $this->userAgent : 'testing';

        $this->client->reset();
        $this->client->addHeader('transId', uniqid())
            ->addHeader('transactionSrc', $transSource);

        return $this;
    }

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @throws Exception
     * @return mixed
     */
    public function getTracking(string|array|null $trackingNumbers = null): mixed
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: There is no HTTP client for this shipping adapter.');
        }
        if ($trackingNumbers !== null) {
            if (is_array($trackingNumbers)) {
                $this->addTrackingNumbers($trackingNumbers);
            } else {
                $this->addTrackingNumber($trackingNumbers);
            }
        }

        if (!$this->hasTrackingNumbers()) {
            throw new Exception('Error: No tracking numbers have been passed.');
        }

        $responses   = [];
        $transSource = $this->isProd ? $this->userAgent : 'testing';

        $this->client->reset();
        $this->client->addHeader('transId', uniqid())
            ->addHeader('transactionSrc', $transSource);

        foreach ($this->trackingNumbers as $trackingNumber) {
            $response = $this->client->get($this->trackingApiUrl . $trackingNumber);
            if ($response->isSuccess()) {
                $responses[] = $response->getParsedResponse();
            }
            $this->client->reset();
        }

        if (!empty($responses)) {
            $this->response = $responses;
        }

        return $this;
    }

    /**
     * Validate address
     *
     * @param  mixed $address  An array of address data or a string with containing "from" to indicate
     *                         using the ship-from address. If empty, will default to the ship-to address
     * @throws Exception
     * @return mixed
     */
    public function validateAddress(mixed $address = null): mixed
    {
        if ($address !== null) {
            // Use the ship-from address
            if (is_string($address) && str_contains(strtolower($address), 'from') && !empty($this->shipFrom)) {
                $address = $this->shipFrom;
                // Else, check that the address is an array of address data
            } else if (!is_array($address)) {
                throw new Exception('Error: The provided address must be an array of address data.');
            }
        } else if (!empty($this->shipTo)) {
            $address = $this->shipTo;
        }

        if (empty($address) && empty($this->shipTo) && empty($this->shipFrom)) {
            throw new Exception('Error: There is no ship-to or ship-from address and no other address was provided.');
        }

        /**
         * TO-DO
         */

        return $this;
    }

}
