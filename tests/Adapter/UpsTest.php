<?php

namespace Pop\Shipping\Test\Adapter;

use Pop\Shipping\Auth\Ups as UpsAuth;
use Pop\Shipping\Adapter\Ups as UpsAdapter;
use Pop\Http\Client\Handler\Mock;
use Pop\Http\Client\Response;
use PHPUnit\Framework\TestCase;

class UpsTest extends TestCase
{

    protected function jsonResponse(array $body): Response
    {
        return new Response([
            'code'    => 200,
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
