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

use Pop\Shipping\Client\AbstractShippingClient;

/**
 * Pop shipping FedEx adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
     * Fetch rates from the API
     *
     * @throws Exception
     * @return array
     */
    public function fetchRates(): array
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
        $shipper['address']['postalCode']  = $this->shipFrom['postal_code'];
        $shipper['address']['countryCode'] = $this->shipFrom['country'] ?? 'US';
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
        $recipient['address']['postalCode']  = $this->shipTo['postal_code'];
        $recipient['address']['countryCode'] = $this->shipTo['country'] ?? 'US';
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
        } else {
            $this->errorCode = $response->getCode();
            $parsedResponse  = $response->getParsedResponse();
            if (is_string($parsedResponse)) {
                $parsedResponse = json_decode($parsedResponse, true);
            }
            if (isset($parsedResponse['errors'])) {
                $errorMessages = [];
                foreach ($parsedResponse['errors'] as $error) {
                    $errorMessages[] = $error['message'] . ' (' . $error['code'] . ')';
                }
                $this->errorMessage = implode('; ', $errorMessages);
            }
        }

        return (!empty($this->response)) ? $this->parseRatesResponse() : [];
    }

    /**
     * Parse rates response
     *
     * @return mixed
     */
    public function parseRatesResponse(): array
    {
        $this->rates = [];

        if (!empty($this->response) && is_array($this->response) &&
            isset($this->response['output']) && isset($this->response['output']['rateReplyDetails'])) {
            foreach ($this->response['output']['rateReplyDetails'] as $rateReplyDetail) {
                if (!empty($rateReplyDetail['ratedShipmentDetails'][0]['totalNetCharge'])) {
                    $this->rates[] = [
                        'service'     => 'Fedex',
                        'serviceType' => $rateReplyDetail['serviceType'],
                        'serviceName' => $rateReplyDetail['serviceName'],
                        'totalCharge' => number_format($rateReplyDetail['ratedShipmentDetails'][0]['totalNetCharge'], 2, '.', '')
                    ];
                }
            }

            usort($this->rates, fn($a, $b) => $a['totalCharge'] <=> $b['totalCharge']);
        }

        return $this->rates;
    }

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @throws Exception
     * @return array
     */
    public function getTracking(string|array|null $trackingNumbers = null): array
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

        $responses = [];

        foreach ($this->trackingNumbers as $trackingNumber) {
            $data = [
                'includeDetailedScans' => true,
                'trackingInfo'         => []
            ];
            $data['trackingInfo'][] = [
                'trackingNumberInfo' => [
                    'trackingNumber' => $trackingNumber
                ]
            ];

            $this->client->reset()->setData($data);

            $response = $this->client->post($this->trackingApiUrl);

            if ($response->isSuccess()) {
                $responses[] = $response->getParsedResponse();
            }
        }

        if (!empty($responses)) {
            $this->response = $responses;
        }

        return (!empty($this->response)) ? $this->parseTrackingResponse() : [];
    }

    /**
     * Parse tracking response
     *
     * @return mixed
     */
    public function parseTrackingResponse(): array
    {
        $results = [];

        if (!empty($this->response) && is_array($this->response)) {
            foreach ($this->response as $response) {
                if (isset($response['output']) && isset($response['output']['completeTrackResults'])) {
                    foreach ($response['output']['completeTrackResults'] as $completeTrackResult) {
                        $results[$completeTrackResult['trackingNumber']] = [];
                        foreach ($completeTrackResult['trackResults'][0]['scanEvents'] as $scanEvent) {
                            $results[$completeTrackResult['trackingNumber']][] = [
                                'status'           => $scanEvent['derivedStatus'],
                                'eventType'        => $scanEvent['eventType'],
                                'eventDescription' => $scanEvent['eventDescription'],
                                'dateTime'         => $scanEvent['date']
                            ];
                        }

                        usort($results[$completeTrackResult['trackingNumber']], fn($a, $b) => $a['dateTime'] <=> $b['dateTime']);
                    }
                }
            }
        }

        return $results;
    }

}
