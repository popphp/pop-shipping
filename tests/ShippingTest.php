<?php

namespace Pop\Shipping\Test;

use Pop\Shipping\Shipping;
use Pop\Shipping\Test\TestAsset\TestAdapter;
use PHPUnit\Framework\TestCase;

class ShippingTest extends TestCase
{

    public function testConstructor()
    {
        $shipping = new Shipping(new TestAdapter());
        $this->assertInstanceOf('Pop\Shipping\Shipping', $shipping);
        $this->assertInstanceOf('Pop\Shipping\Test\TestAsset\TestAdapter', $shipping->getAdapter());
    }

}
