<?php

namespace Pop\Shipping\Test\Adapter;

use Pop\Shipping\Adapter\Google;
use Pop\Http\Client\Handler\Mock;
use Pop\Http\Client\Response;
use PHPUnit\Framework\TestCase;

class GoogleTest extends TestCase
{

    protected function jsonResponse(array $body): Response
    {
        return new Response([
            'code'    => 200,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($body),
        ]);
    }

    public function testValidateReturnsFalseWithoutSuggestionWhenVerdictIsFix()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse([
            'result' => [
                'verdict' => ['possibleNextAction' => 'FIX'],
            ],
        ]));
        $google->getClient()->setHandler($mock);

        $confirmed = $google->validate([
            'address1'    => '123 Bad St.',
            'city'        => 'Wrong Town',
            'state'       => 'FL',
            'postal_code' => '12345',
        ]);

        $this->assertFalse($confirmed);
        $this->assertNull($google->getSuggestedAddress());
    }

    public function testValidateReturnsTrueWhenVerdictIsAccept()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse([
            'result' => [
                'verdict' => ['possibleNextAction' => 'ACCEPT'],
            ],
        ]));
        $google->getClient()->setHandler($mock);

        $confirmed = $google->validate([
            'address1'    => '123 Main St',
            'city'        => 'Some Town',
            'state'       => 'FL',
            'postal_code' => '12345',
        ]);

        $this->assertTrue($confirmed);
        $this->assertNull($google->getSuggestedAddress());
    }

    public function testValidateBuildsSuggestedAddressWhenVerdictIsConfirm()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse([
            'result' => [
                'verdict'  => ['possibleNextAction' => 'CONFIRM'],
                'uspsData' => ['standardizedAddress' => [
                    'firstAddressLine' => '123 MAIN ST',
                    'city'             => 'SOME TOWN',
                    'state'            => 'FL',
                    'zipCode'          => '12345',
                    'zipCodeExtension' => '6789',
                ]],
            ],
        ]));
        $google->getClient()->setHandler($mock);

        $confirmed = $google->validate([
            'address1'    => '123 Bad St.',
            'city'        => 'Wrong Town',
            'state'       => 'FL',
            'postal_code' => '12345',
        ]);

        $this->assertFalse($confirmed);
        $this->assertNotNull($google->getSuggestedAddress());
        $this->assertEquals('12345-6789', $google->getSuggestedAddress()->getPostalCode());
    }

    public function testValidateBuildsSuggestedAddressWhenVerdictIsConfirmAddSubpremises()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse([
            'result' => [
                'verdict'  => ['possibleNextAction' => 'CONFIRM_ADD_SUBPREMISES'],
                'uspsData' => ['standardizedAddress' => [
                    'firstAddressLine' => '123 MAIN ST',
                    'city'             => 'SOME TOWN',
                    'state'            => 'FL',
                    'zipCode'          => '12345',
                ]],
            ],
        ]));
        $google->getClient()->setHandler($mock);

        $confirmed = $google->validate([
            'address1'    => '123 Main St',
            'city'        => 'Some Town',
            'state'       => 'FL',
            'postal_code' => '12345',
        ]);

        $this->assertFalse($confirmed);
        $this->assertNotNull($google->getSuggestedAddress());
    }

}
