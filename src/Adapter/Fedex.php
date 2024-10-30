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

        /**
         * TO-DO
         */

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
