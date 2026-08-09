<?php

namespace Pop\Shipping\Test\Adapter;

use Pop\Shipping\Auth\Ups as UpsAuth;
use Pop\Shipping\Adapter\Ups as UpsAdapter;
use Pop\Shipping\Package;
use Pop\Http\Client\Handler\Mock;
use Pop\Http\Client\Response;
use PHPUnit\Framework\TestCase;

class UpsTest extends TestCase
{

    protected function jsonResponse(array $body, int $code = 200): Response
    {
        return new Response([
            'code'    => $code,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($body),
        ]);
    }

    protected function createAuthenticatedAdapter(): UpsAdapter
    {
        $authClient = UpsAuth::createAuthClient('id', 'secret', 'account123');
        $authMock   = new Mock();
        $authMock->queue($this->jsonResponse([
            'access_token' => 'tok',
            'token_type'   => 'bearer',
            'expires_in'   => 3600,
        ]));
        $authClient->getClient()->setHandler($authMock);
        $authClient->authenticate();

        return UpsAdapter::createAdapter($authClient);
    }

    protected function withAddressesAndPackage(UpsAdapter $adapter): UpsAdapter
    {
        $adapter->setShipFrom(['address1' => '456 Main St', 'city' => 'Main Town', 'state' => 'GA', 'zip' => '54321']);
        $adapter->setShipTo(['address1' => '123 Main St', 'city' => 'Some Town', 'state' => 'FL', 'zip' => '12345']);
        $adapter->addPackage(new Package(34, 24, 12, 65, 1000));

        return $adapter;
    }

    public function testFetchRatesThrowsWithoutClient()
    {
        $adapter = new UpsAdapter();
        $adapter->setShipFrom(['zip' => '54321'])->setShipTo(['zip' => '12345']);

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $adapter->fetchRates();
    }

    public function testFetchRatesBuildsExpectedRequestAndMapsServiceNames()
    {
        $adapter = $this->withAddressesAndPackage($this->createAuthenticatedAdapter());

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'RateResponse' => ['RatedShipment' => [
                ['Service' => ['Code' => '03'], 'TotalCharges' => ['MonetaryValue' => '18.20']],
                ['Service' => ['Code' => '01'], 'TotalCharges' => ['MonetaryValue' => '55.00']],
            ]],
        ]));
        $adapter->getClient()->setHandler($mock);

        $rates = $adapter->fetchRates();

        $this->assertCount(2, $rates);
        $this->assertEquals('18.20', $rates[0]['totalCharge']);
        $this->assertEquals('UPS', $rates[0]['service']);
        $this->assertEquals('Ground', $rates[0]['serviceName']);
        $this->assertEquals('Next Day Air', $rates[1]['serviceName']);

        $request = $mock->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString('/api/rating/v2403/Shop', $request->getUriAsString());
        $this->assertNotNull($request->getHeaderValueAsString('transId'));

        $data = json_decode($request->getDataContent(), true);
        $this->assertEquals('SHOP', $data['RateRequest']['Request']['RequestOption']);
        $this->assertEquals('456 Main St', $data['RateRequest']['Shipment']['Shipper']['Address']['AddressLine'][0]);
        $this->assertEquals(1, $data['RateRequest']['Shipment']['NumOfPieces']);
    }

    public function testFetchRatesIncludesAddress2WhenPresent()
    {
        $adapter = $this->createAuthenticatedAdapter();
        $adapter->setShipFrom(['address1' => '456 Main St', 'address2' => 'Suite 2', 'zip' => '54321']);
        $adapter->setShipTo(['address1' => '123 Main St', 'address2' => 'Apt 4', 'zip' => '12345']);
        $adapter->addPackage(new Package(34, 24, 12, 65));

        $mock = new Mock();
        $mock->queue($this->jsonResponse(['RateResponse' => ['RatedShipment' => []]]));
        $adapter->getClient()->setHandler($mock);

        $adapter->fetchRates();

        $data = json_decode($mock->getLastRequest()->getDataContent(), true);
        $this->assertEquals('Suite 2', $data['RateRequest']['Shipment']['Shipper']['Address']['AddressLine']);
        $this->assertEquals('Apt 4', $data['RateRequest']['Shipment']['ShipTo']['Address']['AddressLine']);
    }

    public function testFetchRatesMapsWeightUnitDescriptionsForOzAndKg()
    {
        $adapter = $this->createAuthenticatedAdapter();
        $adapter->setShipFrom(['zip' => '54321']);
        $adapter->setShipTo(['zip' => '12345']);
        $adapter->addPackage((new Package(1, 1, 1, 8))->setDimensionUnit('OZ'));
        $adapter->addPackage((new Package(1, 1, 1, 2))->setDimensionUnit('KG'));

        $mock = new Mock();
        $mock->queue($this->jsonResponse(['RateResponse' => ['RatedShipment' => []]]));
        $adapter->getClient()->setHandler($mock);

        $adapter->fetchRates();

        $data     = json_decode($mock->getLastRequest()->getDataContent(), true);
        $packages = $data['RateRequest']['Shipment']['Package'];

        $this->assertEquals('Ounces', $packages[0]['PackageWeight']['UnitOfMeasurement']['Description']);
        $this->assertEquals('Kilograms', $packages[1]['PackageWeight']['UnitOfMeasurement']['Description']);
    }

    public function testFetchRatesWithUnmappedServiceCodeYieldsNullServiceName()
    {
        $adapter = $this->withAddressesAndPackage($this->createAuthenticatedAdapter());

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'RateResponse' => ['RatedShipment' => [
                ['Service' => ['Code' => '99'], 'TotalCharges' => ['MonetaryValue' => '10.00']],
            ]],
        ]));
        $adapter->getClient()->setHandler($mock);

        $rates = $adapter->fetchRates();

        $this->assertNull($rates[0]['serviceName']);
    }

    public function testFetchRatesPopulatesErrorCodeAndMessageOnFailure()
    {
        $adapter = $this->withAddressesAndPackage($this->createAuthenticatedAdapter());

        $mock = new Mock();
        $mock->queue($this->jsonResponse(
            ['response' => ['errors' => [['code' => '111285', 'message' => 'Invalid address']]]],
            400
        ));
        $adapter->getClient()->setHandler($mock);

        $rates = $adapter->fetchRates();

        $this->assertEquals([], $rates);
        $this->assertEquals(400, $adapter->getErrorCode());
        $this->assertEquals('Invalid address (111285)', $adapter->getErrorMessage());
        $this->assertFalse($adapter->isSuccess());
    }

    public function testGetTrackingThrowsWithoutClient()
    {
        $adapter = new UpsAdapter();

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $adapter->getTracking('1Z999');
    }

    public function testGetTrackingThrowsWithoutTrackingNumbers()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $adapter->getTracking();
    }

    public function testGetTrackingParsesActivitySortedOldestFirst()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'trackResponse' => ['shipment' => [[
                'inquiryNumber' => '1Z999',
                'package' => [['activity' => [
                    ['date' => '20260102', 'time' => '090000', 'status' => ['statusCode' => 'DL', 'type' => 'D', 'description' => 'Delivered']],
                    ['date' => '20260101', 'time' => '120000', 'status' => ['statusCode' => 'IT', 'type' => 'X', 'description' => 'In transit']],
                ]]],
            ]]],
        ]));
        $adapter->getClient()->setHandler($mock);

        $results = $adapter->getTracking('1Z999');

        $this->assertCount(2, $results['1Z999']);
        $this->assertEquals('In transit', $results['1Z999'][0]['eventDescription']);
        $this->assertEquals('2026-01-01 12:00:00', $results['1Z999'][0]['dateTime']);
        $this->assertEquals('Delivered', $results['1Z999'][1]['eventDescription']);
    }

    public function testGetTrackingReturnsWarningMessageStringWhenNoActivity()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'trackResponse' => ['shipment' => [[
                'inquiryNumber' => '1Z777',
                'warnings'      => [['message' => 'No tracking information available']],
            ]]],
        ]));
        $adapter->getClient()->setHandler($mock);

        $results = $adapter->getTracking('1Z777');

        $this->assertEquals('No tracking information available', $results['1Z777']);
    }

    public function testGetTrackingSendsAUniqueTransIdPerTrackingNumber()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'trackResponse' => ['shipment' => [[
                'inquiryNumber' => '1Z999',
                'warnings'      => [['message' => 'n/a']],
            ]]],
        ]));
        $mock->queue($this->jsonResponse([
            'trackResponse' => ['shipment' => [[
                'inquiryNumber' => '1Z888',
                'warnings'      => [['message' => 'n/a']],
            ]]],
        ]));
        $adapter->getClient()->setHandler($mock);

        $adapter->getTracking(['1Z999', '1Z888']);

        $requests = $mock->getRequests();
        $this->assertCount(2, $requests);
        $this->assertNotEquals(
            $requests[0]->getHeaderValueAsString('transId'),
            $requests[1]->getHeaderValueAsString('transId')
        );
    }

}
