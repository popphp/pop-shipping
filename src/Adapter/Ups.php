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

use Pop\Shipping\Client\AbstractShippingClient;

/**
 * Pop shipping UPS adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
    protected ?string $ratesApiUrl = '/api/rating/v2403/Shop'; // POST

    /**
     * Tracking API URL
     * @var ?string
     */
    protected ?string $trackingApiUrl = '/api/track/v1/details/'; // GET - requires the tracking number to be appended to the URL

    /**
     * UPS Shipping Services
     * @var array
     */
    protected array $shippingServices = [
        '01' => 'Next Day Air',
        '02' => '2nd Day Air',
        '03' => 'Ground',
        '12' => '3 Day Select',
        '13' => 'Next Day Air Saver',
        '14' => 'Next Day Air Early',
        '59' => '2nd Day Air A.M.',
        '75' => 'Heavy Goods',
        '07' => 'Worldwide Express',
        '08' => 'Worldwide Expedited',
        '11' => 'Standard',
        '54' => 'Worldwide Express Plus',
        '65' => 'Worldwide Saver',
        '70' => 'Access Point Economy',
        '82' => 'Today Standard',
        '83' => 'Today Dedicated Courier',
        '84' => 'Today Intercity',
        '85' => 'Today Express',
        '86' => 'Today Express Saver',
        '96' => 'Worldwide Express Freight',
    ];

    /**
     * Get rates
     *
     * @throws Exception
     * @return array
     */
    public function getRates(): array
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: There is no HTTP client for this shipping adapter.');
        }

        $shipper   = [];
        $recipient = [];
        $packages  = [];

        if (!empty($this->shipFrom['address1'])) {
            $shipper['Address']['AddressLine'] = [$this->shipFrom['address1']];
            if (!empty($this->shipFrom['address2'])) {
                $shipper['Address']['AddressLine'] = $this->shipFrom['address2'];
            }
        }
        if (!empty($this->shipFrom['city'])) {
            $shipper['Address']['City'] = $this->shipFrom['city'];
        }
        if (!empty($this->shipFrom['state'])) {
            $shipper['Address']['StateProvinceCode'] = $this->shipFrom['state'];
        }
        $shipper['Address']['PostalCode']  = $this->shipFrom['postal_code'];
        $shipper['Address']['CountryCode'] = $this->shipFrom['country'] ?? 'US';

        if (!empty($this->shipTo['address1'])) {
            $recipient['Address']['AddressLine'] = [$this->shipTo['address1']];
            if (!empty($this->shipTo['address2'])) {
                $recipient['Address']['AddressLine'] = $this->shipTo['address2'];
            }
        }
        if (!empty($this->shipTo['city'])) {
            $recipient['Address']['City'] = $this->shipTo['city'];
        }
        if (!empty($this->shipTo['state'])) {
            $recipient['Address']['StateProvinceCode'] = $this->shipTo['state'];
        }
        $recipient['Address']['PostalCode']  = $this->shipTo['postal_code'];
        $recipient['Address']['CountryCode'] = $this->shipTo['country'] ?? 'US';

        foreach ($this->packages as $package) {
            if ($package->getDimensionUnit() == 'OZ') {
                $weightUnitDesc = 'Ounces';
            } else if ($package->getDimensionUnit() == 'KG') {
                $weightUnitDesc = 'Kilograms';
            } else {
                $weightUnitDesc = 'Pounds';
            }

            $pkg = [
                "PackagingType" => [
                    "Code" => "02",
                    "Description" => "Packaging"
                ],
                'Dimensions' => [
                    'Width'  => (string)$package->getWidth(),
                    'Height' => (string)$package->getHeight(),
                    'Length' => (string)$package->getDepth(),
                    'UnitOfMeasurement' => [
                        'Code'        => $package->getDimensionUnit(),
                        'Description' => ($package->getDimensionUnit() == 'IN') ? 'Inches' : 'Centimeters'
                    ]
                ],
                'PackageWeight' => [
                    'Weight' => (string)$package->getWeight(),
                    'UnitOfMeasurement' => [
                        'Code'        => $package->getWeightUnit() . 'S',
                        'Description' => $weightUnitDesc
                    ]
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
                    'RequestOption' => 'SHOP'
                ],
                'Shipment'   => [
                    'Shipper'     => $shipper,
                    'ShipTo'      => $recipient,
                    'ShipFrom'    => $shipper,
                    'NumOfPieces' => count($this->packages),
                    'Package'     => $packages
                ]
            ]
        ];

        $transSource = $this->authClient->isProduction() ? $this->userAgent : 'testing';

        $this->client->reset()->setData($data);
        $this->client->addHeader('transId', uniqid())
            ->addHeader('transactionSrc', $transSource);

        $response = $this->client->post($this->ratesApiUrl);

        if ($response->isSuccess()) {
            $this->response = $response->getParsedResponse();
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
        $results = [];

        if (!empty($this->response) && is_array($this->response) &&
            isset($this->response['RateResponse']) && isset($this->response['RateResponse']['RatedShipment'])) {
            foreach ($this->response['RateResponse']['RatedShipment'] as $ratedShipment) {
                $results[] = [
                    'serviceType' => $ratedShipment['Service']['Code'],
                    'serviceName' => $this->shippingServices[$ratedShipment['Service']['Code']] ?? null,
                    'totalCharge' => number_format($ratedShipment['TotalCharges']['MonetaryValue'], 2)
                ];
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

        $responses   = [];
        $transSource = $this->authClient->isProduction() ? $this->userAgent : 'testing';

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
                if (isset($response['trackResponse']) && isset($response['trackResponse']['shipment'])) {
                    foreach ($response['trackResponse']['shipment'] as $shipment) {
                        if (isset($shipment['package'][0]) && isset($shipment['package'][0]['activity'])) {
                            $results[$shipment['inquiryNumber']] = [];
                            foreach ($shipment['package'][0]['activity'] as $activity) {
                                $date = substr($activity['date'], 0, 4) . '-' . substr($activity['date'], 4, 2) . '-' . substr($activity['date'], 6, 2);
                                $time = substr($activity['time'], 0, 2) . ':' . substr($activity['time'], 2, 2) . ':' . substr($activity['time'], 4, 2);

                                $results[$shipment['inquiryNumber']][] = [
                                    'status'           => $activity['status']['statusCode'],
                                    'eventType'        => $activity['status']['type'],
                                    'eventDescription' => $activity['status']['description'],
                                    'dateTime'         => $date . ' ' . $time
                                ];
                            }

                            usort($results[$shipment['inquiryNumber']], fn($a, $b) => $a['dateTime'] <=> $b['dateTime']);
                        } else if (isset($shipment['warnings'][0]['message'])) {
                            $results[$shipment['inquiryNumber']] = $shipment['warnings'][0]['message'];
                        }
                    }
                }
            }
        }

        return $results;
    }

}
