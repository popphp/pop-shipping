<?php

namespace Pop\Shipping\Test\Address;

use Pop\Shipping\Address\AddressParser;
use PHPUnit\Framework\TestCase;

class AddressParserTest extends TestCase
{

    public function testConstructingWithDataDoesNotThrow()
    {
        $parser = new AddressParser('123 Main St, Some Town, FL 12345');
        $this->assertInstanceOf(AddressParser::class, $parser);
    }

    public function testSetDataReturnsFluentInstance()
    {
        $parser = new AddressParser();
        $result = $parser->setData('123 Main St');
        $this->assertSame($parser, $result);
    }

}
