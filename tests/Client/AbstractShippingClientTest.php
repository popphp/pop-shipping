<?php

namespace Pop\Shipping\Test\Client;

use Pop\Shipping\Test\TestAsset\TestShippingClient;
use Pop\Http\Client;
use PHPUnit\Framework\TestCase;

class AbstractShippingClientTest extends TestCase
{

    public function testConstructorWithNoClient()
    {
        $shippingClient = new TestShippingClient();
        $this->assertFalse($shippingClient->hasClient());
        $this->assertNull($shippingClient->getClient());
    }

    public function testConstructorWithClient()
    {
        $client = new Client('http://localhost/');
        $shippingClient = new TestShippingClient($client);
        $this->assertTrue($shippingClient->hasClient());
        $this->assertSame($client, $shippingClient->getClient());
    }

    public function testSetClient()
    {
        $shippingClient = new TestShippingClient();
        $client = new Client('http://localhost/');
        $this->assertInstanceOf(TestShippingClient::class, $shippingClient->setClient($client));
        $this->assertSame($client, $shippingClient->getClient());
    }

    public function testDefaultsToNotProduction()
    {
        $shippingClient = new TestShippingClient();
        $this->assertFalse($shippingClient->isProduction());
    }

    public function testSetProduction()
    {
        $shippingClient = new TestShippingClient();
        $this->assertInstanceOf(TestShippingClient::class, $shippingClient->setProduction(true));
        $this->assertTrue($shippingClient->isProduction());
    }

    public function testProdAndTestApiUrls()
    {
        $shippingClient = new TestShippingClient();
        $this->assertFalse($shippingClient->hasProdApiUrl());
        $this->assertFalse($shippingClient->hasTestApiUrl());

        $shippingClient->setProdApiUrl('https://api.example.com')
            ->setTestApiUrl('https://sandbox.example.com');

        $this->assertTrue($shippingClient->hasProdApiUrl());
        $this->assertTrue($shippingClient->hasTestApiUrl());
        $this->assertEquals('https://api.example.com', $shippingClient->getProdApiUrl());
        $this->assertEquals('https://sandbox.example.com', $shippingClient->getTestApiUrl());
    }

    public function testGetApiUrlReturnsTestUrlByDefault()
    {
        $shippingClient = new TestShippingClient();
        $shippingClient->setProdApiUrl('https://api.example.com')
            ->setTestApiUrl('https://sandbox.example.com');

        $this->assertEquals('https://sandbox.example.com', $shippingClient->getApiUrl());
    }

    public function testGetApiUrlReturnsProdUrlWhenProduction()
    {
        $shippingClient = new TestShippingClient();
        $shippingClient->setProdApiUrl('https://api.example.com')
            ->setTestApiUrl('https://sandbox.example.com')
            ->setProduction(true);

        $this->assertEquals('https://api.example.com', $shippingClient->getApiUrl());
    }

    public function testProviderApiUrlConstants()
    {
        $this->assertEquals('https://apis.fedex.com', TestShippingClient::FEDEX_PROD_API_URL);
        $this->assertEquals('https://apis-sandbox.fedex.com', TestShippingClient::FEDEX_TEST_API_URL);
        $this->assertEquals('https://onlinetools.ups.com', TestShippingClient::UPS_PROD_API_URL);
        $this->assertEquals('https://wwwcie.ups.com', TestShippingClient::UPS_TEST_API_URL);
        $this->assertEquals('https://api.usps.com', TestShippingClient::USPS_PROD_API_URL);
        $this->assertEquals('https://api-cat.usps.com', TestShippingClient::USPS_TEST_API_URL);
    }

}
