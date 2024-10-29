<?php

namespace Pop\Shipping\Test;

use Pop\Shipping\Shipping;
use PHPUnit\Framework\TestCase;

class ShippingTest extends TestCase
{

    public function testConstructor()
    {
        $shipping = new Shipping();
        $this->assertInstanceOf('Pop\Shipping\Shipping', $shipping);
    }

}
