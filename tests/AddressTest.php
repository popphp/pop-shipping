<?php

namespace Pop\Shipping\Test;

use Pop\Shipping\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{

    public function testConstructorWithEmptyArrayDefaultsResidentialToFalse()
    {
        $address = new Address();
        $this->assertFalse($address->isResidential());
        $this->assertNull($address->getFirstName());
    }

    public function testConstructorAcceptsStateOverProvince()
    {
        $address = new Address(['state' => 'FL']);
        $this->assertEquals('FL', $address->getState());
    }

    public function testConstructorPrefersProvinceOverState()
    {
        $address = new Address(['province' => 'ON', 'state' => 'FL']);
        $this->assertEquals('ON', $address->getState());
    }

    public function testConstructorAcceptsPostalCodeOverZip()
    {
        $address = new Address(['postal_code' => '12345']);
        $this->assertEquals('12345', $address->getPostalCode());
    }

    public function testConstructorPrefersZipOverPostalCode()
    {
        $address = new Address(['zip' => '54321', 'postal_code' => '12345']);
        $this->assertEquals('54321', $address->getPostalCode());
    }

    public function testConstructorSetsAllFields()
    {
        $address = new Address([
            'first_name'  => 'John',
            'last_name'   => 'Doe',
            'company'     => 'Acme',
            'address1'    => '123 Main St',
            'address2'    => 'Apt 4',
            'city'        => 'Some Town',
            'state'       => 'FL',
            'zip'         => '12345',
            'country'     => 'US',
            'phone'       => '555-1234',
            'residential' => true,
        ]);

        $this->assertEquals('John', $address->getFirstName());
        $this->assertEquals('Doe', $address->getLastName());
        $this->assertEquals('Acme', $address->getCompany());
        $this->assertEquals('123 Main St', $address->getAddress1());
        $this->assertEquals('Apt 4', $address->getAddress2());
        $this->assertEquals('Some Town', $address->getCity());
        $this->assertEquals('FL', $address->getState());
        $this->assertEquals('12345', $address->getPostalCode());
        $this->assertEquals('US', $address->getCountry());
        $this->assertEquals('555-1234', $address->getPhone());
        $this->assertTrue($address->isResidential());
    }

    public function testSettersAreFluentAndGettersReflectThem()
    {
        $address = new Address();

        $this->assertInstanceOf(Address::class, $address->setFirstName('Jane'));
        $this->assertInstanceOf(Address::class, $address->setLastName('Smith'));
        $this->assertInstanceOf(Address::class, $address->setCompany('Acme'));
        $this->assertInstanceOf(Address::class, $address->setAddress1('456 Main St'));
        $this->assertInstanceOf(Address::class, $address->setAddress2('Suite 2'));
        $this->assertInstanceOf(Address::class, $address->setCity('Main Town'));
        $this->assertInstanceOf(Address::class, $address->setState('GA'));
        $this->assertInstanceOf(Address::class, $address->setPostalCode('54321'));
        $this->assertInstanceOf(Address::class, $address->setCountry('US'));
        $this->assertInstanceOf(Address::class, $address->setPhone('555-6789'));
        $this->assertInstanceOf(Address::class, $address->setResidential(true));

        $this->assertEquals('Jane', $address->getFirstName());
        $this->assertEquals('Smith', $address->getLastName());
        $this->assertEquals('Acme', $address->getCompany());
        $this->assertEquals('456 Main St', $address->getAddress1());
        $this->assertEquals('Suite 2', $address->getAddress2());
        $this->assertEquals('Main Town', $address->getCity());
        $this->assertEquals('GA', $address->getState());
        $this->assertEquals('54321', $address->getPostalCode());
        $this->assertEquals('US', $address->getCountry());
        $this->assertEquals('555-6789', $address->getPhone());
        $this->assertTrue($address->isResidential());
    }

    public function testHasMethodsReflectEmptiness()
    {
        $address = new Address();
        $this->assertFalse($address->hasFirstName());
        $this->assertFalse($address->hasLastName());
        $this->assertFalse($address->hasCompany());
        $this->assertFalse($address->hasAddress1());
        $this->assertFalse($address->hasAddress2());
        $this->assertFalse($address->hasCity());
        $this->assertFalse($address->hasState());
        $this->assertFalse($address->hasPostalCode());
        $this->assertFalse($address->hasCountry());
        $this->assertFalse($address->hasPhone());

        $address->setFirstName('Jane');
        $this->assertTrue($address->hasFirstName());
    }

    public function testMagicGetSetIssetUnset()
    {
        $address = new Address();

        $address->city = 'Some Town';
        $this->assertEquals('Some Town', $address->city);
        $this->assertTrue(isset($address->city));

        unset($address->city);
        $this->assertNull($address->city);
        $this->assertFalse(isset($address->city));
    }

    public function testMagicAccessorsAliasZipToPostalCode()
    {
        $address = new Address();

        $address->zip = '90210';
        $this->assertEquals('90210', $address->zip);
        $this->assertEquals('90210', $address->postal_code);
        $this->assertTrue(isset($address->zip));

        unset($address->zip);
        $this->assertNull($address->postal_code);
    }

    public function testMagicAccessorsAliasProvinceToState()
    {
        $address = new Address();

        $address->province = 'ON';
        $this->assertEquals('ON', $address->province);
        $this->assertEquals('ON', $address->state);
        $this->assertTrue(isset($address->province));

        unset($address->province);
        $this->assertNull($address->state);
    }

    public function testMagicSetIgnoresUnknownKeys()
    {
        $address = new Address();
        $address->bogus_field = 'whatever';
        $this->assertNull($address->bogus_field);
    }

    public function testArrayAccess()
    {
        $address = new Address();

        $address['city'] = 'Some Town';
        $this->assertEquals('Some Town', $address['city']);
        $this->assertTrue(isset($address['city']));

        unset($address['city']);
        $this->assertFalse(isset($address['city']));
        $this->assertNull($address['city']);
    }

    public function testArrayAccessAliasesZipAndProvince()
    {
        $address = new Address();

        $address['zip'] = '12345';
        $this->assertEquals('12345', $address['postal_code']);

        $address['province'] = 'ON';
        $this->assertEquals('ON', $address['state']);
    }

    public function testToArrayReturnsAllFields()
    {
        $address = new Address([
            'first_name' => 'John',
            'city'       => 'Some Town',
            'zip'        => '12345',
        ]);

        $data = $address->toArray();

        $this->assertIsArray($data);
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('Some Town', $data['city']);
        $this->assertEquals('12345', $data['postal_code']);
        $this->assertArrayHasKey('residential', $data);
        $this->assertFalse($data['residential']);
    }

}
