<?php

namespace Pop\Shipping\Test;

use Pop\Shipping\Shipping;
use Pop\Shipping\Test\TestAsset\TestAdapter;

class ShippingTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $shipping = new Shipping(new TestAdapter());
        $this->assertInstanceOf('Pop\Shipping\Shipping', $shipping);
        $this->assertInstanceOf('Pop\Shipping\Test\TestAsset\TestAdapter', $shipping->adapter());
    }

    public function testSend()
    {
        $shipping = new Shipping(new TestAdapter());
        $shipping->shipTo([
            'address' => '123 Main St.'
        ]);
        $shipping->shipFrom([
            'address' => '456 Main St.'
        ]);
        $shipping->setDimensions([
            'width' => '12'
        ], 'IN');
        $shipping->setWeight(10, 'LB');
        $shipping->send();
        $this->assertTrue($shipping->isSuccess());
        $this->assertFalse($shipping->isError());
        $this->assertEquals('Success', $shipping->getResponse());
        $this->assertEquals(1, $shipping->getResponseCode());
        $this->assertEquals('OK', $shipping->getResponseMessage());
        $this->assertEquals('10.05', $shipping->getRates()['Express']);
    }

}