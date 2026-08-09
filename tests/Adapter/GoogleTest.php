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
        $this->assertEquals('123 Main St', $google->getSuggestedAddress()->getAddress1());
        $this->assertEquals('Some Town', $google->getSuggestedAddress()->getCity());
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
                    'firstAddressLine' => '123 N MAIN ST',
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
        $this->assertEquals('123 N Main St', $google->getSuggestedAddress()->getAddress1());
        $this->assertEquals('Some Town', $google->getSuggestedAddress()->getCity());
    }

    public function testValidateBuildsSuggestedAddressWithNoLeadingSpaceForPoBox()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse([
            'result' => [
                'verdict'  => ['possibleNextAction' => 'CONFIRM'],
                'uspsData' => ['standardizedAddress' => [
                    'firstAddressLine' => 'PO BOX 1234',
                    'city'             => 'SOME TOWN',
                    'state'            => 'FL',
                    'zipCode'          => '12345',
                ]],
            ],
        ]));
        $google->getClient()->setHandler($mock);

        $confirmed = $google->validate([
            'address1'    => 'PO Box 1234',
            'city'        => 'Some Town',
            'state'       => 'FL',
            'postal_code' => '12345',
        ]);

        $this->assertFalse($confirmed);
        $this->assertNotNull($google->getSuggestedAddress());
        $this->assertEquals('PO Box 1234', $google->getSuggestedAddress()->getAddress1());
    }

    public function testValidateThrowsWithoutAnOriginalAddress()
    {
        $google = new Google('FAKE_KEY');

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $this->expectExceptionMessage('Error: No original address was provided.');

        $google->validate();
    }

    public function testValidateThrowsWithoutAPostalCode()
    {
        $google = new Google('FAKE_KEY');

        $this->expectException(\Pop\Shipping\Adapter\Exception::class);
        $this->expectExceptionMessage('Error: The original address postal code is required.');

        $google->validate(['address1' => '123 Main St', 'city' => 'Some Town', 'state' => 'FL']);
    }

    public function testValidateWithMalformedResponseStaysUnconfirmedWithNoException()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse(['result' => []]));
        $google->getClient()->setHandler($mock);

        $confirmed = $google->validate(['address1' => '123 Main St', 'postal_code' => '12345']);

        $this->assertFalse($confirmed);
        $this->assertNull($google->getSuggestedAddress());
    }

    public function testApiKeyAccessors()
    {
        $google = new Google('FAKE_KEY');
        $this->assertTrue($google->hasApiKey());
        $this->assertEquals('FAKE_KEY', $google->getApiKey());

        $google->setApiKey('OTHER_KEY');
        $this->assertEquals('OTHER_KEY', $google->getApiKey());
    }

    public function testClientAccessors()
    {
        $google = new Google('FAKE_KEY');
        $this->assertTrue($google->hasClient());
        $this->assertNotNull($google->getClient());
    }

    public function testGetApiUrl()
    {
        $google = new Google('FAKE_KEY');
        $this->assertEquals('https://addressvalidation.googleapis.com/v1:validateAddress?key=', $google->getApiUrl());
    }

    public function testValidateIncludesAddress2InRequestWhenPresent()
    {
        $google = new Google('FAKE_KEY');
        $mock   = new Mock();
        $mock->queue($this->jsonResponse(['result' => ['verdict' => ['possibleNextAction' => 'ACCEPT']]]));
        $google->getClient()->setHandler($mock);

        $google->validate([
            'address1'    => '123 Main St',
            'address2'    => 'Apt 4',
            'postal_code' => '12345',
        ]);

        $data = json_decode($mock->getLastRequest()->getDataContent(), true);
        $this->assertEquals(['123 Main St', 'Apt 4'], $data['address']['addressLines']);
    }

    public function testOriginalAndSuggestedAddressAccessorsDefaultEmpty()
    {
        $google = new Google('FAKE_KEY');
        $this->assertFalse($google->hasOriginalAddress());
        $this->assertNull($google->getOriginalAddress());
        $this->assertFalse($google->hasSuggestedAddress());
        $this->assertNull($google->getSuggestedAddress());
        $this->assertFalse($google->isConfirmed());
        $this->assertEquals([], $google->getResponse());
    }

    public function testSetOriginalAddressAcceptsAddressInstance()
    {
        $google  = new Google('FAKE_KEY');
        $address = new \Pop\Shipping\Address(['postal_code' => '12345']);

        $google->setOriginalAddress($address);

        $this->assertTrue($google->hasOriginalAddress());
        $this->assertSame($address, $google->getOriginalAddress());
    }

}
