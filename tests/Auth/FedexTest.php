<?php

namespace Pop\Shipping\Test\Auth;

use Pop\Shipping\Auth\Fedex;
use Pop\Http\Client\Handler\Mock;
use Pop\Http\Client\Response;
use PHPUnit\Framework\TestCase;

class FedexTest extends TestCase
{

    protected function jsonResponse(array $body): Response
    {
        return new Response([
            'code'    => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($body),
        ]);
    }

    public function testCreateAuthClientSetsAccountNumber()
    {
        $authClient = Fedex::createAuthClient('client-id', 'secret', 'account123');
        $this->assertEquals('account123', $authClient->getAccountNumber());
    }

    public function testCreateAuthClientDefaultsToNotProduction()
    {
        $authClient = Fedex::createAuthClient('client-id', 'secret', 'account123');
        $this->assertFalse($authClient->isProduction());
        $this->assertEquals('https://apis-sandbox.fedex.com', $authClient->getApiUrl());
    }

    public function testCreateAuthClientHonorsProdFlag()
    {
        $authClient = Fedex::createAuthClient('client-id', 'secret', 'account123', true);
        $this->assertTrue($authClient->isProduction());
        $this->assertEquals('https://apis.fedex.com', $authClient->getApiUrl());
    }

    public function testCreateAuthClientWiresClientWithCredentials()
    {
        $authClient = Fedex::createAuthClient('my-client-id', 'my-secret', 'account123');
        $client     = $authClient->getClient();

        $this->assertNotNull($client);
        $this->assertEquals('client_credentials', $client->getData('grant_type'));
        $this->assertEquals('my-client-id', $client->getData('client_id'));
        $this->assertEquals('my-secret', $client->getData('client_secret'));
    }

    public function testAuthenticateSendsCredentialsAndLoadsToken()
    {
        $mock = new Mock();
        $mock->queue($this->jsonResponse(['access_token' => 'tok123', 'token_type' => 'bearer', 'expires_in' => 3600]));

        $authClient = Fedex::createAuthClient('my-client-id', 'my-secret', 'account123');
        $authClient->getClient()->setHandler($mock);

        $authClient->authenticate();

        $this->assertTrue($authClient->hasAuthToken());
        $this->assertEquals('tok123', $authClient->getAuthToken());

        $request = $mock->getLastRequest();
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https://apis-sandbox.fedex.com/oauth/token', $request->getUriAsString());
    }

}
