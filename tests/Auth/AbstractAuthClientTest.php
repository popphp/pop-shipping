<?php

namespace Pop\Shipping\Test\Auth;

use Pop\Shipping\Test\TestAsset\TestAuthClient;
use Pop\Http\Client;
use Pop\Http\Client\Handler\Mock;
use Pop\Http\Client\Response;
use PHPUnit\Framework\TestCase;

class AbstractAuthClientTest extends TestCase
{

    protected function jsonResponse(array $body): Response
    {
        return new Response([
            'code'    => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($body),
        ]);
    }

    public function testSaveTokenDataToFileWithoutPriorTokenDataTriggersNoWarnings()
    {
        $tokenFile  = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');
        $authClient = new TestAuthClient();

        $warnings = [];
        set_error_handler(function (int $errno, string $errstr) use (&$warnings): bool {
            $warnings[] = $errstr;
            return true;
        }, E_WARNING);

        try {
            $authClient->saveTokenDataToFile($tokenFile);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $warnings);

        unlink($tokenFile);
    }

    public function testAuthApiUrlAccessors()
    {
        $authClient = new TestAuthClient();
        $this->assertFalse($authClient->hasAuthApiUrl());
        $this->assertNull($authClient->getAuthApiUrl());

        $authClient->setAuthApiUrl('/oauth/token');

        $this->assertTrue($authClient->hasAuthApiUrl());
        $this->assertEquals('/oauth/token', $authClient->getAuthApiUrl());
    }

    public function testAccountNumberAccessors()
    {
        $authClient = new TestAuthClient();
        $this->assertFalse($authClient->hasAccountNumber());
        $this->assertNull($authClient->getAccountNumber());

        $authClient->setAccountNumber('account123');

        $this->assertTrue($authClient->hasAccountNumber());
        $this->assertEquals('account123', $authClient->getAccountNumber());
    }

    public function testLoadTokenDataWithExpiresIn()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123', 'token_type' => 'bearer', 'expires_in' => 3600]);

        $this->assertTrue($authClient->hasTokenData());
        $this->assertEquals('tok123', $authClient->getAuthToken());
        $this->assertEquals('bearer', $authClient->getTokenType());
        $this->assertTrue($authClient->hasTokenType());
        $this->assertTrue($authClient->hasExpiration());
        $this->assertTrue($authClient->hasAuthToken());
        $this->assertFalse($authClient->isExpired());
        $this->assertGreaterThan(3590, $authClient->willExpireIn());
        $this->assertLessThanOrEqual(3600, $authClient->willExpireIn());
    }

    public function testLoadTokenDataWithAbsoluteExpiration()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123', 'token_type' => 'bearer', 'expiration' => time() + 100]);

        $this->assertTrue($authClient->hasAuthToken());
        $this->assertGreaterThan(90, $authClient->willExpireIn());
        $this->assertLessThanOrEqual(100, $authClient->willExpireIn());
    }

    public function testGetTokenDataWithAndWithoutKey()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123', 'token_type' => 'bearer']);

        $this->assertEquals('tok123', $authClient->getTokenData('access_token'));
        $this->assertNull($authClient->getTokenData('nonexistent'));
        $this->assertEquals(['access_token' => 'tok123', 'token_type' => 'bearer'], $authClient->getTokenData());
    }

    public function testHasTokenDataIsFalseBeforeLoad()
    {
        $authClient = new TestAuthClient();
        $this->assertFalse($authClient->hasTokenData());
    }

    public function testIsExpiredWhenExpirationInPast()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123', 'expiration' => time() - 100]);

        $this->assertTrue($authClient->isExpired());
        $this->assertFalse($authClient->hasAuthToken());
        $this->assertNull($authClient->getAuthToken());
        $this->assertEquals(0, $authClient->willExpireIn());
    }

    public function testHasExpirationIsFalseWithoutExpirationData()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123']);

        $this->assertFalse($authClient->hasExpiration());
        $this->assertFalse($authClient->isExpired());
        $this->assertNull($authClient->getExpiration());
    }

    public function testHasTokenDataFile()
    {
        $authClient = new TestAuthClient();
        $tokenFile  = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');

        $this->assertTrue($authClient->hasTokenDataFile($tokenFile));
        $this->assertFalse($authClient->hasTokenDataFile($tokenFile . '-does-not-exist'));

        unlink($tokenFile);
    }

    public function testLoadTokenDataFromFileWhenFileDoesNotExist()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenDataFromFile('/tmp/pop-shipping-does-not-exist-' . uniqid());

        $this->assertFalse($authClient->hasTokenData());
    }

    public function testLoadTokenDataFromFileWhenFileExists()
    {
        $tokenFile = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');
        file_put_contents($tokenFile, json_encode(['access_token' => 'tok123', 'token_type' => 'bearer', 'expiration' => time() + 3600]));

        $authClient = new TestAuthClient();
        $authClient->loadTokenDataFromFile($tokenFile);

        $this->assertTrue($authClient->hasAuthToken());
        $this->assertEquals('tok123', $authClient->getAuthToken());

        unlink($tokenFile);
    }

    public function testSaveTokenDataToFileWithTokenDataArgument()
    {
        $tokenFile  = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');
        $authClient = new TestAuthClient();

        $authClient->saveTokenDataToFile($tokenFile, ['access_token' => 'tok123', 'token_type' => 'bearer', 'expires_in' => 3600]);

        $saved = json_decode(file_get_contents($tokenFile), true);
        $this->assertEquals('tok123', $saved['access_token']);
        $this->assertEquals('bearer', $saved['token_type']);
        $this->assertArrayHasKey('expiration', $saved);

        unlink($tokenFile);
    }

    public function testAuthenticateThrowsWithoutHttpClient()
    {
        $authClient = new TestAuthClient();

        $this->expectException(\Pop\Shipping\Auth\Exception::class);
        $authClient->authenticate();
    }

    public function testAuthenticateSuccessLoadsTokenDataAndPersistsToFile()
    {
        $mock = new Mock();
        $mock->queue($this->jsonResponse(['access_token' => 'tok123', 'token_type' => 'bearer', 'expires_in' => 3600]));

        $client = new Client('http://localhost/oauth/token', ['method' => 'POST']);
        $client->setHandler($mock);

        $authClient = new TestAuthClient($client);
        $tokenFile  = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');

        $authClient->authenticate($tokenFile);

        $this->assertTrue($authClient->hasAuthToken());
        $this->assertEquals('tok123', $authClient->getAuthToken());

        $saved = json_decode(file_get_contents($tokenFile), true);
        $this->assertEquals('tok123', $saved['access_token']);

        unlink($tokenFile);
    }

    public function testFetchAuthTokenLoadsFromFileWhenNoTokenInMemory()
    {
        $tokenFile = tempnam(sys_get_temp_dir(), 'pop-shipping-token-');
        file_put_contents($tokenFile, json_encode(['access_token' => 'from-file', 'token_type' => 'bearer', 'expiration' => time() + 3600]));

        $authClient = new TestAuthClient();
        $token      = $authClient->fetchAuthToken($tokenFile);

        $this->assertEquals('from-file', $token);

        unlink($tokenFile);
    }

    public function testFetchAuthTokenReturnsCachedTokenWithoutReauthenticating()
    {
        $authClient = new TestAuthClient();
        $authClient->loadTokenData(['access_token' => 'tok123', 'token_type' => 'bearer', 'expires_in' => 3600]);

        $token = $authClient->fetchAuthToken();

        $this->assertEquals('tok123', $token);
    }

    public function testFetchAuthTokenReauthenticatesWhenWithinBuffer()
    {
        $mock = new Mock();
        $mock->queue($this->jsonResponse(['access_token' => 'fresh-token', 'token_type' => 'bearer', 'expires_in' => 3600]));

        $client = new Client('http://localhost/oauth/token', ['method' => 'POST']);
        $client->setHandler($mock);

        $authClient = new TestAuthClient($client);
        $authClient->loadTokenData(['access_token' => 'stale-token', 'token_type' => 'bearer', 'expires_in' => 5]);

        $token = $authClient->fetchAuthToken(null, 10);

        $this->assertEquals('fresh-token', $token);
    }

    public function testRefreshClearsStateAndReauthenticates()
    {
        $mock = new Mock();
        $mock->queue($this->jsonResponse(['access_token' => 'refreshed-token', 'token_type' => 'bearer', 'expires_in' => 3600]));

        $client = new Client('http://localhost/oauth/token', ['method' => 'POST']);
        $client->setHandler($mock);

        $authClient = new TestAuthClient($client);
        $authClient->loadTokenData(['access_token' => 'old-token', 'token_type' => 'bearer', 'expires_in' => 3600]);

        $authClient->refresh();

        $this->assertEquals('refreshed-token', $authClient->getAuthToken());
    }

}
