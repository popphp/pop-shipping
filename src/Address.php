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
namespace Pop\Shipping;

/**
 * Pop shipping address class
 *
 * @category   Pop
 * @package    Pop\Shipping
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Address implements \ArrayAccess
{

    /**
     * Address data
     * @var array
     */
    protected array $data = [
        'first_name'  => null,
        'last_name'   => null,
        'company'     => null,
        'address1'    => null,
        'address2'    => null,
        'city'        => null,
        'state'       => null,
        'postal_code' => null,
        'country'     => null,
        'phone'       => null,
        'residential' => false,
    ];

    /**
     * Constructor
     *
     * Instantiate the shipping address object
     *
     * @param  array $address
     */
    public function __construct(array $address = [])
    {
        $this->data['first_name']  = $address['first_name'] ?? null;
        $this->data['last_name']   = $address['last_name'] ?? null;
        $this->data['company']     = $address['company'] ?? null;
        $this->data['address1']    = $address['address1'] ?? null;
        $this->data['address2']    = $address['address2'] ?? null;
        $this->data['city']        = $address['city'] ?? null;
        $this->data['state']       = $address['state'] ?? null;
        $this->data['postal_code'] = $address['postal_code'] ?? null;
        $this->data['country']     = $address['country'] ?? null;
        $this->data['phone']       = $address['phone'] ?? null;
        $this->data['residential'] = $address['residential'] ?? false;
    }

    /**
     * Set first name
     *
     * @param  string $firstName
     * @return Address
     */
    public function setFirstName(string $firstName): Address
    {
        $this->data['first_name'] = $firstName;
        return $this;
    }

    /**
     * Get first name
     *
     * @return mixed
     */
    public function getFirstName(): mixed
    {
        return $this->data['first_name'] ?? null;
    }

    /**
     * Has first name
     *
     * @return bool
     */
    public function hasFirstName(): bool
    {
        return !empty($this->data['first_name']);
    }

    /**
     * Set last name
     *
     * @param  string $lastName
     * @return Address
     */
    public function setLastName(string $lastName): Address
    {
        $this->data['last_name'] = $lastName;
        return $this;
    }

    /**
     * Get last name
     *
     * @return mixed
     */
    public function getLastName(): mixed
    {
        return $this->data['last_name'] ?? null;
    }

    /**
     * Has last name
     *
     * @return bool
     */
    public function hasLastName(): bool
    {
        return !empty($this->data['last_name']);
    }

    /**
     * Set company
     *
     * @param  string $company
     * @return Address
     */
    public function setCompany(string $company): Address
    {
        $this->data['company'] = $company;
        return $this;
    }

    /**
     * Get company
     *
     * @return mixed
     */
    public function getCompany(): mixed
    {
        return $this->data['company'] ?? null;
    }

    /**
     * Has company
     *
     * @return bool
     */
    public function hasCompany(): bool
    {
        return !empty($this->data['company']);
    }

    /**
     * Set address 1
     *
     * @param  string $address1
     * @return Address
     */
    public function setAddress1(string $address1): Address
    {
        $this->data['address1'] = $address1;
        return $this;
    }

    /**
     * Get address 1
     *
     * @return mixed
     */
    public function getAddress1(): mixed
    {
        return $this->data['address1'] ?? null;
    }

    /**
     * Has address 1
     *
     * @return bool
     */
    public function hasAddress1(): bool
    {
        return !empty($this->data['address1']);
    }

    /**
     * Set address 2
     *
     * @param  string $address2
     * @return Address
     */
    public function setAddress2(string $address2): Address
    {
        $this->data['address2'] = $address2;
        return $this;
    }

    /**
     * Get address 2
     *
     * @return mixed
     */
    public function getAddress2(): mixed
    {
        return $this->data['address2'] ?? null;
    }

    /**
     * Has address 2
     *
     * @return bool
     */
    public function hasAddress2(): bool
    {
        return !empty($this->data['address2']);
    }

    /**
     * Set city
     *
     * @param  string $city
     * @return Address
     */
    public function setCity(string $city): Address
    {
        $this->data['city'] = $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return mixed
     */
    public function getCity(): mixed
    {
        return $this->data['city'] ?? null;
    }

    /**
     * Has city
     *
     * @return bool
     */
    public function hasCity(): bool
    {
        return !empty($this->data['city']);
    }

    /**
     * Set state
     *
     * @param  string $state
     * @return Address
     */
    public function setState(string $state): Address
    {
        $this->data['state'] = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return mixed
     */
    public function getState(): mixed
    {
        return $this->data['state'] ?? null;
    }

    /**
     * Has state
     *
     * @return bool
     */
    public function hasState(): bool
    {
        return !empty($this->data['state']);
    }

    /**
     * Set postal code
     *
     * @param  string $postalCode
     * @return Address
     */
    public function setPostalCode(string $postalCode): Address
    {
        $this->data['postal_code'] = $postalCode;
        return $this;
    }

    /**
     * Get postal code
     *
     * @return mixed
     */
    public function getPostalCode(): mixed
    {
        return $this->data['postal_code'] ?? null;
    }

    /**
     * Has postal code
     *
     * @return bool
     */
    public function hasPostalCode(): bool
    {
        return !empty($this->data['postal_code']);
    }

    /**
     * Set country
     *
     * @param  string $country
     * @return Address
     */
    public function setCountry(string $country): Address
    {
        $this->data['country'] = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return mixed
     */
    public function getCountry(): mixed
    {
        return $this->data['country'] ?? null;
    }

    /**
     * Has country
     *
     * @return bool
     */
    public function hasCountry(): bool
    {
        return !empty($this->data['country']);
    }

    /**
     * Set phone
     *
     * @param  string $phone
     * @return Address
     */
    public function setPhone(string $phone): Address
    {
        $this->data['phone'] = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return mixed
     */
    public function getPhone(): mixed
    {
        return $this->data['phone'] ?? null;
    }

    /**
     * Has phone
     *
     * @return bool
     */
    public function hasPhone(): bool
    {
        return !empty($this->data['phone']);
    }

    /**
     * Set residential
     *
     * @param  bool $residential
     * @return Address
     */
    public function setResidential(bool $residential): Address
    {
        $this->data['residential'] = $residential;
        return $this;
    }

    /**
     * Is residential
     *
     * @return bool
     */
    public function isResidential(): bool
    {
        return (array_key_exists('residential', $this->data) ? $this->data['residential'] : false);
    }

    /**
     * Address to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Magic method to set the property to the value of $this->rowGateway[$name]
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value)
    {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name] = $value;
        }
    }

    /**
     * Magic method to return the value of $this->rowGateway[$name]
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic method to return the isset value of $this->rowGateway[$name]
     *
     * @param  string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return !empty($this->data[$name]);
    }

    /**
     * Magic method to unset $this->rowGateway[$name]
     *
     * @param  string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name] = null;
        }
    }

    /**
     * ArrayAccess offsetExists
     *
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * ArrayAccess offsetGet
     *
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * ArrayAccess offsetSet
     *
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess offsetUnset
     *
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }

}
