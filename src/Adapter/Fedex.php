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
     * @return Fedex
     */
    public function getRates(): Fedex
    {
        if (!$this->hasClient()) {
            throw new Exception('Error: There is no HTTP client for this shipping adapter.');
        }

        return $this;
    }

    /**
     * Get tracking
     *
     * @param  string|array|null $trackingNumbers
     * @throws Exception
     * @return Fedex
     */
    public function getTracking(string|array|null $trackingNumbers = null): Fedex
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

}
