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
 * Pop shipping USPS adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Usps extends AbstractAdapter
{

    /**
     * Production API URL
     * @var ?string
     */
    protected ?string $prodApiUrl = AbstractShippingClient::USPS_PROD_API_URL;

    /**
     * Test API URL
     * @var ?string
     */
    protected ?string $testApiUrl = AbstractShippingClient::USPS_TEST_API_URL;

    /**
     * Rates API URL
     * @var ?string
     */
    protected ?string $ratesApiUrl = '/shipments/v3/options/search'; // POST

    /**
     * Tracking API URL
     * @var ?string
     */
    protected ?string $trackingApiUrl = '/tracking/v3/tracking/'; // GET - requires the tracking number to be appended to the URL

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

        $responses = [];

        foreach ($this->packages as $package) {
            $data = [
                'pricingOptions' => [
                    [
                        'priceType'         => ($this->shipTo['residential']) ? 'RETAIL' : 'COMMERCIAL',
                        'paymentAccount'    => [
                            'accountType'   => 'EPS',
                            'accountNumber' => $this->authClient->getAccountNumber()
                        ]
                    ]
                ],
                'originZIPCode'                => $this->shipFrom['postal_code'],
                'destinationZIPCode'           => $this->shipTo['postal_code'],
                'destinationCountryCode'       => $this->shipTo['country'] ?? 'US',
                'foreignPostalCode'            => $this->shipTo['postal_code'],
                'destinationEntryFacilityType' => 'NONE',
                'packageDescription'           => [
                    'weight'                   => $package->getWeight(),
                    'length'                   => $package->getDepth(),
                    'width'                    => $package->getWidth(),
                    'height'                   => $package->getHeight(),
                    'mailClass'                => 'ALL',
                    'packageValue'             => $package->getValue()
                ],
                'shippingFilter' => 'PRICE'
            ];

            $this->client->reset()->setData($data);

            $response = $this->client->post($this->ratesApiUrl);

            if ($response->isSuccess()) {
                $responses[] = $response->getParsedResponse();
            }
        }

        $this->response = $responses;

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
            isset($this->response['pricingOptions']) && isset($this->response['pricingOptions']['shippingOptions'])) {
            foreach ($this->response['pricingOptions']['shippingOptions'] as $shippingOption) {
                if (!empty($shippingOption['rateOptions'][0]['totalBasePrice'])) {
                    $results[] = [
                        'serviceType' => $shippingOption['mailClass'],
                        'serviceName' => ucwords(str_replace('_', ' ', strtolower($shippingOption['mailClass']))),
                        'totalCharge' => number_format($shippingOption['rateOptions'][0]['totalBasePrice'], 2)
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

        return $results;
    }

}
