<?php

namespace Pop\Shipping\Test\Adapter;

use Pop\Shipping\Test\TestAsset\TestAdapter;
use Pop\Shipping\Test\TestAsset\TestAuthClient;
use Pop\Shipping\Address;
use Pop\Shipping\Package;
use PHPUnit\Framework\TestCase;

class AbstractAdapterTest extends TestCase
{

    public function testAddPackageAutoAssignsIdWhenMissing()
    {
        $adapter = new TestAdapter();
        $package = new Package(1, 1, 1, 1);

        $adapter->addPackage($package);

        $this->assertTrue($package->hasId());
        $this->assertTrue($adapter->hasPackage($package->getId()));
    }

    public function testAddPackageKeepsExplicitId()
    {
        $adapter = new TestAdapter();
        $package = (new Package(1, 1, 1, 1))->setId('pkg-1');

        $adapter->addPackage($package);

        $this->assertEquals('pkg-1', $package->getId());
        $this->assertSame($package, $adapter->getPackage('pkg-1'));
    }

    public function testAddPackagesAddsMultiple()
    {
        $adapter = new TestAdapter();
        $adapter->addPackages([
            (new Package(1, 1, 1, 1))->setId('a'),
            (new Package(2, 2, 2, 2))->setId('b'),
        ]);

        $this->assertCount(2, $adapter->getPackages());
        $this->assertTrue($adapter->hasPackage('a'));
        $this->assertTrue($adapter->hasPackage('b'));
    }

    public function testHasPackagesAndGetPackageDefaults()
    {
        $adapter = new TestAdapter();
        $this->assertFalse($adapter->hasPackages());
        $this->assertEquals([], $adapter->getPackages());
        $this->assertNull($adapter->getPackage('nonexistent'));
        $this->assertFalse($adapter->hasPackage('nonexistent'));
    }

    public function testAddTrackingNumberDedups()
    {
        $adapter = new TestAdapter();
        $adapter->addTrackingNumber('1Z999');
        $adapter->addTrackingNumber('1Z999');

        $this->assertCount(1, $adapter->getTrackingNumbers());
    }

    public function testAddTrackingNumberSplitsCommaSeparatedString()
    {
        $adapter = new TestAdapter();
        $adapter->addTrackingNumber('1Z999, 1Z888,1Z777');

        $this->assertCount(3, $adapter->getTrackingNumbers());
        $this->assertTrue($adapter->hasTrackingNumber('1Z999'));
        $this->assertTrue($adapter->hasTrackingNumber('1Z888'));
        $this->assertTrue($adapter->hasTrackingNumber('1Z777'));
    }

    public function testAddTrackingNumbersArray()
    {
        $adapter = new TestAdapter();
        $adapter->addTrackingNumbers(['1Z999', '1Z888']);

        $this->assertCount(2, $adapter->getTrackingNumbers());
    }

    public function testHasTrackingNumbersDefaults()
    {
        $adapter = new TestAdapter();
        $this->assertFalse($adapter->hasTrackingNumbers());
        $this->assertFalse($adapter->hasTrackingNumber('1Z999'));
        $this->assertEquals([], $adapter->getTrackingNumbers());
    }

    public function testSetShipToAcceptsArrayOrAddress()
    {
        $adapter = new TestAdapter();
        $this->assertFalse($adapter->hasShipTo());

        $adapter->setShipTo(['city' => 'Some Town']);
        $this->assertTrue($adapter->hasShipTo());
        $this->assertInstanceOf(Address::class, $adapter->getShipTo());
        $this->assertEquals('Some Town', $adapter->getShipTo()->getCity());

        $address = new Address(['city' => 'Other Town']);
        $adapter->setShipTo($address);
        $this->assertSame($address, $adapter->getShipTo());
    }

    public function testSetShipFromAcceptsArrayOrAddress()
    {
        $adapter = new TestAdapter();
        $this->assertFalse($adapter->hasShipFrom());

        $adapter->setShipFrom(['city' => 'Main Town']);
        $this->assertTrue($adapter->hasShipFrom());
        $this->assertEquals('Main Town', $adapter->getShipFrom()->getCity());
    }

    public function testResponseRatesAndErrorStateDefaults()
    {
        $adapter = new TestAdapter();

        $this->assertFalse($adapter->hasResponse());
        $this->assertNull($adapter->getResponse());
        $this->assertFalse($adapter->hasRates());
        $this->assertEquals([], $adapter->getRates());
        $this->assertFalse($adapter->hasErrorCode());
        $this->assertNull($adapter->getErrorCode());
        $this->assertFalse($adapter->hasErrorMessage());
        $this->assertNull($adapter->getErrorMessage());
        $this->assertFalse($adapter->isSuccess());
    }

    public function testShipTypeAccessors()
    {
        $adapter = new TestAdapter();
        $this->assertFalse($adapter->hasShipType());

        $adapter->setShipType('DROPOFF_AT_FEDEX_LOCATION');

        $this->assertTrue($adapter->hasShipType());
        $this->assertEquals('DROPOFF_AT_FEDEX_LOCATION', $adapter->getShipType());
    }

    public function testRatesAndTrackingApiUrlAccessors()
    {
        $adapter = new TestAdapter();
        $this->assertFalse($adapter->hasRatesApiUrl());
        $this->assertFalse($adapter->hasTrackingApiUrl());

        $adapter->setRatesApiUrl('/rates')->setTrackingApiUrl('/tracking');

        $this->assertTrue($adapter->hasRatesApiUrl());
        $this->assertTrue($adapter->hasTrackingApiUrl());
        $this->assertEquals('/rates', $adapter->getRatesApiUrl());
        $this->assertEquals('/tracking', $adapter->getTrackingApiUrl());
    }

    public function testUserAgentDefaultAndSetter()
    {
        $adapter = new TestAdapter();
        $this->assertEquals('popphp/pop-shipping 4.0.0', $adapter->getUserAgent());

        $adapter->setUserAgent('my-app/1.0');
        $this->assertEquals('my-app/1.0', $adapter->getUserAgent());
    }

    public function testAuthClientAccessors()
    {
        $adapter    = new TestAdapter();
        $authClient = new TestAuthClient();

        $this->assertFalse($adapter->hasAuthClient());

        $adapter->setAuthClient($authClient);

        $this->assertTrue($adapter->hasAuthClient());
        $this->assertSame($authClient, $adapter->getAuthClient());
    }

    public function testCreateAdapterWithoutValidTokenHasNoHttpClient()
    {
        $authClient = new TestAuthClient();
        $adapter    = TestAdapter::createAdapter($authClient);

        $this->assertFalse($adapter->hasClient());
        $this->assertSame($authClient, $adapter->getAuthClient());
    }

    public function testCreateAdapterCopiesProductionFlag()
    {
        $authClient = new TestAuthClient();
        $authClient->setProduction(true);

        $adapter = TestAdapter::createAdapter($authClient);

        $this->assertTrue($adapter->isProduction());
    }

    public function testCreateAdapterWithValidTokenWiresBearerClient()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123', 'token_type' => 'bearer', 'expires_in' => 3600]);

        $adapter = TestAdapter::createAdapter($authClient);

        $this->assertTrue($adapter->hasClient());
    }

}
