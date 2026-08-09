<?php

namespace Pop\Shipping\Test\Adapter;

use Pop\Shipping\Auth\Fedex as FedexAuth;
use Pop\Shipping\Adapter\Fedex as FedexAdapter;
use Pop\Shipping\Package;
use Pop\Http\Client\Handler\Mock;
use Pop\Http\Client\Response;
use PHPUnit\Framework\TestCase;

class FedexTest extends TestCase
{

    protected function jsonResponse(array $body, int $code = 200): Response
    {
        return new Response([
            'code'    => $code,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($body),
        ]);
    }

    protected function createAuthenticatedAdapter(): FedexAdapter
    {
        $authClient = FedexAuth::createAuthClient('id', 'secret', 'account123');
        $authMock   = new Mock();
        $authMock->queue($this->jsonResponse(['access_token' => 'tok', 'token_type' => 'bearer', 'expires_in' => 3600]));
        $authClient->getClient()->setHandler($authMock);
        $authClient->authenticate();

        return FedexAdapter::createAdapter($authClient);
    }

    protected function withAddressesAndPackage(FedexAdapter $adapter): FedexAdapter
    {
        $adapter->setShipFrom(['address1' => '456 Main St', 'city' => 'Main Town', 'state' => 'GA', 'zip' => '54321']);
        $adapter->setShipTo(['address1' => '123 Main St', 'city' => 'Some Town', 'state' => 'FL', 'zip' => '12345', 'residential' => true]);
        $adapter->addPackage(new Package(34, 24, 12, 65, 1000));

        return $adapter;
    }

    public function testFetchRatesThrowsWithoutClient()
    {
        $adapter = new FedexAdapter();
        $adapter->setShipFrom(['zip' => '54321'])->setShipTo(['zip' => '12345']);

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $adapter->fetchRates();
    }

    public function testFetchRatesBuildsExpectedRequestAndReturnsSortedRates()
    {
        $adapter = $this->withAddressesAndPackage($this->createAuthenticatedAdapter());

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'output' => [
                'rateReplyDetails' => [
                    [
                        'serviceType' => 'PRIORITY_OVERNIGHT',
                        'serviceName' => 'FedEx Priority Overnight',
                        'ratedShipmentDetails' => [['totalNetCharge' => 88.10]],
                    ],
                    [
                        'serviceType' => 'FEDEX_GROUND',
                        'serviceName' => 'FedEx Ground',
                        'ratedShipmentDetails' => [['totalNetCharge' => 24.55]],
                    ],
                ],
            ],
        ]));
        $adapter->getClient()->setHandler($mock);

        $rates = $adapter->fetchRates();

        $this->assertCount(2, $rates);
        $this->assertEquals('24.55', $rates[0]['totalCharge']);
        $this->assertEquals('Fedex', $rates[0]['service']);
        $this->assertEquals('FEDEX_GROUND', $rates[0]['serviceType']);
        $this->assertEquals('88.10', $rates[1]['totalCharge']);

        $request  = $mock->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertStringContainsString('/rate/v1/rates/quotes', $request->getUriAsString());

        $data = json_decode($request->getDataContent(), true);
        $this->assertEquals('account123', $data['accountNumber']['value']);
        $this->assertEquals('456 Main St', $data['requestedShipment']['shipper']['address']['streetLines'][0]);
        $this->assertEquals('123 Main St', $data['requestedShipment']['recipient']['address']['streetLines'][0]);
        $this->assertTrue($data['requestedShipment']['recipient']['address']['residential']);
        $this->assertEquals(65, $data['requestedShipment']['requestedPackageLineItems'][0]['weight']['value']);
    }

    public function testFetchRatesPopulatesErrorCodeAndMessageOnFailure()
    {
        $adapter = $this->withAddressesAndPackage($this->createAuthenticatedAdapter());

        $mock = new Mock();
        $mock->queue($this->jsonResponse(
            ['errors' => [['code' => 'INVALID.INPUT.EXCEPTION', 'message' => 'Bad address']]],
            400
        ));
        $adapter->getClient()->setHandler($mock);

        $rates = $adapter->fetchRates();

        $this->assertEquals([], $rates);
        $this->assertEquals(400, $adapter->getErrorCode());
        $this->assertEquals('Bad address (INVALID.INPUT.EXCEPTION)', $adapter->getErrorMessage());
        $this->assertFalse($adapter->isSuccess());
    }

    public function testFetchRatesIncludesAddress2WhenPresent()
    {
        $adapter = $this->createAuthenticatedAdapter();
        $adapter->setShipFrom(['address1' => '456 Main St', 'address2' => 'Suite 2', 'zip' => '54321']);
        $adapter->setShipTo(['address1' => '123 Main St', 'address2' => 'Apt 4', 'zip' => '12345']);
        $adapter->addPackage(new Package(34, 24, 12, 65));

        $mock = new Mock();
        $mock->queue($this->jsonResponse(['output' => ['rateReplyDetails' => []]]));
        $adapter->getClient()->setHandler($mock);

        $adapter->fetchRates();

        $data = json_decode($mock->getLastRequest()->getDataContent(), true);
        $this->assertEquals(
            ['456 Main St', 'Suite 2'],
            $data['requestedShipment']['shipper']['address']['streetLines']
        );
        $this->assertEquals(
            ['123 Main St', 'Apt 4'],
            $data['requestedShipment']['recipient']['address']['streetLines']
        );
    }

    public function testFetchRatesParsesRawStringErrorResponse()
    {
        $adapter = $this->withAddressesAndPackage($this->createAuthenticatedAdapter());

        // No Content-Type header, so pop-http can't content-negotiate and getParsedResponse()
        // hands back the raw body string instead of an array - exercising the adapter's
        // is_string($parsedResponse) json_decode fallback in its error-handling path.
        $mock = new Mock();
        $mock->queue(new Response([
            'code' => 500,
            'body' => json_encode(['errors' => [['code' => 'SERVER.ERROR', 'message' => 'Server error']]]),
        ]));
        $adapter->getClient()->setHandler($mock);

        $rates = $adapter->fetchRates();

        $this->assertEquals([], $rates);
        $this->assertEquals('Server error (SERVER.ERROR)', $adapter->getErrorMessage());
    }

    public function testGetTrackingThrowsWithoutClient()
    {
        $adapter = new FedexAdapter();

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $adapter->getTracking('1234567890');
    }

    public function testGetTrackingThrowsWithoutTrackingNumbers()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $adapter->getTracking();
    }

    public function testGetTrackingSendsOnePostPerTrackingNumberAndSortsEventsOldestFirst()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $mock = new Mock();
        $mock->queue($this->jsonResponse([
            'output' => ['completeTrackResults' => [[
                'trackingNumber' => '1111',
                'trackResults' => [[
                    'scanEvents' => [
                        ['derivedStatus' => 'Delivered', 'eventType' => 'DL', 'eventDescription' => 'Delivered', 'date' => '2026-01-02T09:00:00'],
                        ['derivedStatus' => 'In transit', 'eventType' => 'IT', 'eventDescription' => 'In transit', 'date' => '2026-01-01T12:00:00'],
                    ],
                ]],
            ]]],
        ]));
        $mock->queue($this->jsonResponse([
            'output' => ['completeTrackResults' => [[
                'trackingNumber' => '2222',
                'trackResults' => [['scanEvents' => [
                    ['derivedStatus' => 'Delivered', 'eventType' => 'DL', 'eventDescription' => 'Delivered', 'date' => '2026-01-03T09:00:00'],
                ]]],
            ]]],
        ]));
        $adapter->getClient()->setHandler($mock);

        $results = $adapter->getTracking(['1111', '2222']);

        $this->assertCount(2, $mock->getRequests());
        $this->assertArrayHasKey('1111', $results);
        $this->assertArrayHasKey('2222', $results);

        // Sorted oldest-first within the tracking number's own events.
        $this->assertEquals('In transit', $results['1111'][0]['status']);
        $this->assertEquals('Delivered', $results['1111'][1]['status']);

        $firstRequestData = json_decode($mock->getRequests()[0]->getDataContent(), true);
        $this->assertEquals('1111', $firstRequestData['trackingInfo'][0]['trackingNumberInfo']['trackingNumber']);
        $this->assertTrue($firstRequestData['includeDetailedScans']);
    }

    public function testGetTrackingReturnsEmptyArrayWhenNoResponsesSucceed()
    {
        $adapter = $this->createAuthenticatedAdapter();

        $mock = new Mock();
        $mock->queue($this->jsonResponse(['errors' => [['code' => 'NOT.FOUND', 'message' => 'Not found']]], 404));
        $adapter->getClient()->setHandler($mock);

        $results = $adapter->getTracking('9999');

        $this->assertEquals([], $results);
    }

}
