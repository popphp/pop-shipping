<?php

namespace Pop\Shipping\Test;

use Pop\Shipping\Shipping;
use Pop\Shipping\Package;
use Pop\Shipping\Test\TestAsset\TestAdapter;
use PHPUnit\Framework\TestCase;

class ShippingTest extends TestCase
{

    public function testConstructorWithAdapter()
    {
        $shipping = new Shipping(new TestAdapter());
        $this->assertInstanceOf(Shipping::class, $shipping);
        $this->assertInstanceOf(TestAdapter::class, $shipping->getAdapter());
        $this->assertTrue($shipping->hasAdapter());
    }

    public function testConstructorWithAdapterAndPackages()
    {
        $package1 = new Package(1, 1, 1, 1);
        $package2 = new Package(2, 2, 2, 2);

        $shipping = new Shipping(new TestAdapter(), $package1, $package2);

        $this->assertCount(2, $shipping->getPackages());
    }

    public function testConstructorWithAdapterAndTrackingNumberStrings()
    {
        $shipping = new Shipping(new TestAdapter(), '1Z999', '1Z888');

        $this->assertCount(2, $shipping->getTrackingNumbers());
        $this->assertTrue($shipping->hasTrackingNumber('1Z999'));
    }

    public function testConstructorWithArraysOfPackagesAndTrackingNumbers()
    {
        $shipping = new Shipping(new TestAdapter(), [new Package(1, 1, 1, 1), new Package(2, 2, 2, 2)], ['1Z999', '1Z888']);

        $this->assertCount(2, $shipping->getPackages());
        $this->assertCount(2, $shipping->getTrackingNumbers());
    }

    public function testConstructorWithNoAdapterDoesNotThrow()
    {
        $shipping = new Shipping();
        $this->assertInstanceOf(Shipping::class, $shipping);
        $this->assertFalse($shipping->hasAdapter());
    }

    public function testSetAdapterIsFluent()
    {
        $shipping = new Shipping();
        $this->assertInstanceOf(Shipping::class, $shipping->setAdapter(new TestAdapter()));
        $this->assertTrue($shipping->hasAdapter());
    }

    public function testAddPackageAndAddPackagesDelegateToAdapter()
    {
        $shipping = new Shipping(new TestAdapter());

        $shipping->addPackage(new Package(1, 1, 1, 1));
        $this->assertCount(1, $shipping->getPackages());

        $shipping->addPackages([new Package(2, 2, 2, 2), new Package(3, 3, 3, 3)]);
        $this->assertCount(3, $shipping->getPackages());
        $this->assertTrue($shipping->hasPackages());
    }

    public function testGetPackageDelegatesToAdapter()
    {
        $shipping = new Shipping(new TestAdapter());
        $package  = (new Package(1, 1, 1, 1))->setId('pkg-1');

        $shipping->addPackage($package);

        $this->assertSame($package, $shipping->getPackage('pkg-1'));
        $this->assertTrue($shipping->hasPackage('pkg-1'));
        $this->assertNull($shipping->getPackage('nonexistent'));
        $this->assertFalse($shipping->hasPackage('nonexistent'));
    }

    public function testAddTrackingNumbersDelegateToAdapter()
    {
        $shipping = new Shipping(new TestAdapter());

        $shipping->addTrackingNumber('1Z999');
        $shipping->addTrackingNumbers(['1Z888', '1Z777']);

        $this->assertCount(3, $shipping->getTrackingNumbers());
        $this->assertTrue($shipping->hasTrackingNumbers());
        $this->assertTrue($shipping->hasTrackingNumber('1Z888'));
        $this->assertFalse($shipping->hasTrackingNumber('nonexistent'));
    }

    public function testShipToAndShipFromDelegateToAdapter()
    {
        $shipping = new Shipping(new TestAdapter());

        $this->assertFalse($shipping->hasShipTo());
        $this->assertFalse($shipping->hasShipFrom());

        $shipping->setShipTo(['city' => 'Some Town']);
        $shipping->setShipFrom(['city' => 'Main Town']);

        $this->assertTrue($shipping->hasShipTo());
        $this->assertTrue($shipping->hasShipFrom());
        $this->assertEquals('Some Town', $shipping->getShipTo()->getCity());
        $this->assertEquals('Main Town', $shipping->getShipFrom()->getCity());
    }

    public function testResponseRatesAndErrorStateDelegateToAdapter()
    {
        $shipping = new Shipping(new TestAdapter());

        $this->assertFalse($shipping->hasResponse());
        $this->assertNull($shipping->getResponse());
        $this->assertFalse($shipping->hasRates());
        $this->assertEquals([], $shipping->getRates());
        $this->assertFalse($shipping->hasErrorCode());
        $this->assertNull($shipping->getErrorCode());
        $this->assertFalse($shipping->hasErrorMessage());
        $this->assertNull($shipping->getErrorMessage());
        $this->assertFalse($shipping->isSuccess());
    }

    public function testFetchRatesAndGetTrackingDelegateToAdapter()
    {
        $shipping = new Shipping(new TestAdapter());

        // TestAdapter's fetchRates()/getTracking() are stubs that always return [].
        $this->assertEquals([], $shipping->fetchRates());
        $this->assertEquals([], $shipping->getTracking('1Z999'));
    }

    /**
     * Every delegate method is `$this->adapter?->method()`, so with no adapter set every one of
     * them must gracefully return null rather than throw - this is the documented contract for
     * the facade. hasAdapter() is the one exception: it checks $this->adapter directly (not via
     * ?->) so it correctly returns false, never null.
     */
    public function testAllDelegateMethodsAreNullSafeWithNoAdapterSet()
    {
        $shipping = new Shipping();

        $this->assertNull($shipping->getAdapter());
        $this->assertNull($shipping->getPackages());
        $this->assertNull($shipping->hasPackages());
        $this->assertNull($shipping->getPackage('anything'));
        $this->assertNull($shipping->hasPackage('anything'));
        $this->assertNull($shipping->getTrackingNumbers());
        $this->assertNull($shipping->hasTrackingNumbers());
        $this->assertNull($shipping->hasTrackingNumber('1Z999'));
        $this->assertNull($shipping->getShipTo());
        $this->assertNull($shipping->hasShipTo());
        $this->assertNull($shipping->getShipFrom());
        $this->assertNull($shipping->hasShipFrom());
        $this->assertNull($shipping->getResponse());
        $this->assertNull($shipping->hasResponse());
        $this->assertNull($shipping->isSuccess());
        $this->assertNull($shipping->getErrorCode());
        $this->assertNull($shipping->hasErrorCode());
        $this->assertNull($shipping->getErrorMessage());
        $this->assertNull($shipping->hasErrorMessage());
        $this->assertNull($shipping->getRates());
        $this->assertNull($shipping->hasRates());
        $this->assertNull($shipping->fetchRates());
        $this->assertNull($shipping->getTracking('1Z999'));

        $this->assertFalse($shipping->hasAdapter());
    }

}
