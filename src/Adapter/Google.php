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

use Pop\Http\Client;
use Pop\Shipping\Address;

/**
 * Pop shipping Google adapter class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Google
{

    /**
     * Google Maps API address validation URL
     * @var string
     */
    protected string $apiUrl = 'https://addressvalidation.googleapis.com/v1:validateAddress?key=';

    /**
     * Google Maps API Key
     * @var ?string
     */
    protected ?string $apiKey = null;

    /**
     * HTTP Client
     * @var ?Client
     */
    protected ?Client $client = null;

    /**
     * Original address to validate
     * @var ?Address
     */
    protected ?Address $originalAddress = null;

    /**
     * Suggested address returned from the validation API request
     * @var ?Address
     */
    protected ?Address $suggestedAddress = null;

    /**
     * Validation response
     * @var array
     */
    protected array $response = [];

    /**
     * Confirmed
     * @var bool
     */
    protected bool $confirmed = false;

    /**
     * Constructor
     *
     * Instantiate the shipping object
     *
     */
    public function __construct(string $apiKey)
    {
        $this->setApiKey($apiKey);

        $client = new Client($this->apiUrl . $this->apiKey);
        $client->setType(Client\Request::JSON);

        $this->setClient($client);
    }

    /**
     * Get API URL
     *
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Set API key
     *
     * @param  string $apiKey
     * @return static
     */
    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Get API key
     *
     * @return ?string
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Has API key
     *
     * @return bool
     */
    public function hasApiKey(): bool
    {
        return (!empty($this->apiKey));
    }

    /**
     * Set client
     *
     * @param  Client $client
     * @return static
     */
    public function setClient(Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get client
     *
     * @return ?Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Has client
     *
     * @return bool
     */
    public function hasClient(): bool
    {
        return (!empty($this->client));
    }

    /**
     * Set original address
     *
     * @param  array|Address $address
     * @return static
     */
    public function setOriginalAddress(array|Address $address): static
    {
        $this->originalAddress = !($address instanceof Address) ? new Address($address) : $address;
        return $this;
    }

    /**
     * Get original address
     *
     * @return ?Address
     */
    public function getOriginalAddress(): ?Address
    {
        return $this->originalAddress;
    }

    /**
     * Has original address
     *
     * @return bool
     */
    public function hasOriginalAddress(): bool
    {
        return (!empty($this->originalAddress));
    }

    /**
     * Set suggested address
     *
     * @param  array|Address $address
     * @return static
     */
    public function setSuggestedAddress(array|Address $address): static
    {
        $this->suggestedAddress = !($address instanceof Address) ? new Address($address) : $address;
        return $this;
    }

    /**
     * Get suggested address
     *
     * @return ?Address
     */
    public function getSuggestedAddress(): ?Address
    {
        return $this->suggestedAddress;
    }

    /**
     * Has suggested address
     *
     * @return bool
     */
    public function hasSuggestedAddress(): bool
    {
        return (!empty($this->suggestedAddress));
    }

    /**
     * Get response
     *
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Is confirmed
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * Validate address
     *
     * @param  array|Address|null $address
     * @throws Exception
     * @return void
     */
    public function validate(array|Address|null $address = null): void
    {
        if ($address !== null) {
            $this->setOriginalAddress($address);
        }

        if (!$this->hasOriginalAddress()) {
            throw new Exception('Error: No original address was provided.');
        }
        if (!$this->originalAddress->hasPostalCode()) {
            throw new Exception('Error: The original address postal code is required.');
        }

        $addressData = [
            'address' => [
                'addressLines' => [],
                'postalCode'   => $this->originalAddress['postal_code']
            ]
        ];

        if (!empty($this->originalAddress['address1'])) {
            $addressData['address']['addressLines'][] = $this->originalAddress['address1'];
        }
        if (!empty($this->originalAddress['address2'])) {
            $addressData['address']['addressLines'][] = $this->originalAddress['address2'];
        }
        if (!empty($this->originalAddress['city']) && !empty($this->originalAddress['state'])) {
            $addressData['address']['addressLines'][] = $this->originalAddress['city'] . ', ' . $this->originalAddress['state'];
        }

        $response = $this->client->setData($addressData)->post();

        if ($response->isSuccess()) {
            $this->response = $response->getParsedResponse();
            if (isset($this->response['result']['verdict']) && isset($this->response['result']['verdict']['possibleNextAction'])) {
                if ($this->response['result']['verdict']['possibleNextAction'] == 'CONFIRM') {
                    $addressString = ucwords(strtolower($this->response['result']['uspsData']['standardizedAddress']['firstAddressLine']));
                    if (!empty($this->response['result']['uspsData']['standardizedAddress']['city'])) {
                        $addressString .= ', ' . ucwords(strtolower($this->response['result']['uspsData']['standardizedAddress']['city']));
                    }
                    if (!empty($this->response['result']['uspsData']['standardizedAddress']['state'])) {
                        $addressString .= ', ' . $this->response['result']['uspsData']['standardizedAddress']['state'];
                    }
                    if (!empty($this->response['result']['uspsData']['standardizedAddress']['zipCode'])) {
                        $addressString .= ' ' . $this->response['result']['uspsData']['standardizedAddress']['zipCode'];
                    }
                    if (!empty($this->response['result']['uspsData']['standardizedAddress']['zipCodeExtension'])) {
                        $addressString .= '-' . $this->response['result']['uspsData']['standardizedAddress']['zipCodeExtension'];
                    }

                    $this->setSuggestedAddress($this->parseAddress($addressString));
                } else {
                    $this->confirmed = true;
                }
            }
        }
    }

    /**
     * Parse address
     *
     * @param  string $address
     * @return array
     */
    protected function parseAddress(string $address): array
    {
        $parser = new Address\AddressParser();
        $parser->parse($address);

        $address1 = trim($parser->getStreetNumber()) . ' ' .
            (($parser->hasRouteType()) ? trim($parser->getStreetName()) . ' ' .
            trim($parser->getRouteType()) : trim($parser->getStreetName()));

        $postalCode = trim($parser->getPostalCode());
        $zip4       = trim($parser->getZip4());

        if (!empty($zip4)) {
            $postalCode .= '-' . $zip4;
        }

        return array_filter([
            'address1'      => $address1,
            'address2' => trim($parser->getUnit()),
            'state'         => trim($parser->getStateCode()),
            'city'          => trim($parser->getCity()),
            'postal_code'   => $postalCode,
        ]);
    }

}
