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
 * Pop shipping FedEx adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Fedex extends AbstractAdapter
{

    /**
     * Production API URL
     * @var ?string
     */
    protected ?string $prodApiUrl = AbstractShippingClient::FEDEX_PROD_API_URL;

    /**
     * Test API URL
     * @var ?string
     */
    protected ?string $testApiUrl = AbstractShippingClient::FEDEX_TEST_API_URL;

    /**
     * Rates API URL
     * @var ?string
     */
    protected ?string $ratesApiUrl = '/rate/v1/rates/quotes'; // POST

    /**
     * Tracking API URL
     * @var ?string
     */
    protected ?string $trackingApiUrl = '/track/v1/trackingnumbers'; // POST

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

        $shipper   = ['address' => []];
        $recipient = ['address' => []];
        $packages  = [];

        if (!empty($this->shipFrom['address1'])) {
            $shipper['address']['streetLines'] = [$this->shipFrom['address1']];
            if (!empty($this->shipFrom['address2'])) {
                $shipper['address']['streetLines'][] = $this->shipFrom['address2'];
            }
        }
        if (!empty($this->shipFrom['city'])) {
            $shipper['address']['city'] = $this->shipFrom['city'];
        }
        if (!empty($this->shipFrom['state'])) {
            $shipper['address']['stateOrProvinceCode'] = $this->shipFrom['state'];
        }
        $shipper['address']['postalCode']  = $this->shipFrom['zip'];
        $shipper['address']['countryCode'] = $this->shipFrom['countryCode'] ?? 'US';
        $shipper['address']['residential'] = (bool)$this->shipFrom['residential'] ?? false;

        if (!empty($this->shipTo['address1'])) {
            $recipient['address']['streetLines'] = [$this->shipTo['address1']];
            if (!empty($this->shipTo['address2'])) {
                $recipient['address']['streetLines'][] = $this->shipTo['address2'];
            }
        }
        if (!empty($this->shipTo['city'])) {
            $recipient['address']['city'] = $this->shipTo['city'];
        }
        if (!empty($this->shipTo['state'])) {
            $recipient['address']['stateOrProvinceCode'] = $this->shipTo['state'];
        }
        $recipient['address']['postalCode']  = $this->shipTo['zip'];
        $recipient['address']['countryCode'] = $this->shipTo['countryCode'] ?? 'US';
        $recipient['address']['residential'] = (bool)$this->shipTo['residential'] ?? false;

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
            'accountNumber' => [
                'value' => $this->authClient->getAccountNumber()
            ],
            'requestedShipment' => [
                'shipper'                   => $shipper,
                'recipient'                 => $recipient,
                'requestedPackageLineItems' => $packages,
                'rateRequestType'           => ['LIST'],
                'pickupType'                => $this->shipType ?? 'DROPOFF_AT_FEDEX_LOCATION'
            ],
        ];

        $this->client->reset()->setData($data);

        $response = $this->client->post($this->ratesApiUrl);

        if ($response->isSuccess()) {
            $this->response = $response->getParsedResponse();
            return $this->parseRates();
        } else {
            return $this;
        }
    }

    /**
     * Parse rates response
     *
     * @return mixed
     */
    public function parseRates(): array
    {
        $results = [];

        if (!empty($this->response) && is_array($this->response) &&
            isset($this->response['output']) && isset($this->response['output']['rateReplyDetails'])) {
            foreach ($this->response['output']['rateReplyDetails'] as $rateReplyDetail) {
                if (!empty($rateReplyDetail['ratedShipmentDetails'][0]['totalNetCharge'])) {
                    $results[] = [
                        'serviceType' => $rateReplyDetail['serviceType'],
                        'serviceName' => $rateReplyDetail['serviceName'],
                        'totalCharge' => number_format($rateReplyDetail['ratedShipmentDetails'][0]['totalNetCharge'], 2)
                    ];
                }
            }

            usort($results, fn($a, $b) => $a['totalCharge'] <=> $b['totalCharge']);
        }

        return $results;
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

        $data = [
            'includeDetailedScans' => true,
            'trackingInfo'         => []
        ];

        foreach ($this->trackingNumbers as $trackingNumber) {
            $data['trackingInfo'][] = [
                'trackingNumberInfo' => [
                    'trackingNumber' => $trackingNumber
                ]
            ];
        }

        $this->client->reset()->setData($data);

        $response = $this->client->post($this->trackingApiUrl);

        if ($response->isSuccess()) {
            $this->response = $response->getParsedResponse();
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
