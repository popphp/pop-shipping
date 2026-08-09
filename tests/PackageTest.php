<?php

namespace Pop\Shipping\Test;

use Pop\Shipping\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{

    public function testConstructorSetsRequiredDimensions()
    {
        $package = new Package(34, 24, 12, 65);
        $this->assertEquals(34, $package->getWidth());
        $this->assertEquals(24, $package->getHeight());
        $this->assertEquals(12, $package->getDepth());
        $this->assertEquals(65, $package->getWeight());
        $this->assertTrue($package->hasWidth());
        $this->assertTrue($package->hasHeight());
        $this->assertTrue($package->hasDepth());
        $this->assertTrue($package->hasWeight());
    }

    public function testConstructorWithoutValueOrPackagingLeavesThemUnset()
    {
        $package = new Package(34, 24, 12, 65);
        $this->assertFalse($package->hasValue());
        $this->assertFalse($package->hasPackaging());
        $this->assertNull($package->getValue());
        $this->assertNull($package->getPackaging());
    }

    public function testConstructorSetsOptionalValueAndPackaging()
    {
        $package = new Package(34, 24, 12, 65, 1000, 5.50);
        $this->assertEquals(1000, $package->getValue());
        $this->assertEquals(5.50, $package->getPackaging());
        $this->assertTrue($package->hasValue());
        $this->assertTrue($package->hasPackaging());
    }

    public function testDefaultUnits()
    {
        $package = new Package(34, 24, 12, 65);
        $this->assertEquals('IN', $package->getDimensionUnit());
        $this->assertEquals('LB', $package->getWeightUnit());
        $this->assertEquals('USD', $package->getValueUnit());
    }

    public function testSetUnits()
    {
        $package = new Package(34, 24, 12, 65);
        $package->setDimensionUnit('CM')
            ->setWeightUnit('KG')
            ->setValueUnit('EUR');

        $this->assertEquals('CM', $package->getDimensionUnit());
        $this->assertEquals('KG', $package->getWeightUnit());
        $this->assertEquals('EUR', $package->getValueUnit());
    }

    public function testSettersAreFluent()
    {
        $package = new Package(1, 1, 1, 1);
        $this->assertInstanceOf(Package::class, $package->setWidth(2));
        $this->assertInstanceOf(Package::class, $package->setHeight(2));
        $this->assertInstanceOf(Package::class, $package->setDepth(2));
        $this->assertInstanceOf(Package::class, $package->setWeight(2));
        $this->assertInstanceOf(Package::class, $package->setValue(2));
        $this->assertInstanceOf(Package::class, $package->setPackaging(2));
        $this->assertInstanceOf(Package::class, $package->setId('x'));
        $this->assertInstanceOf(Package::class, $package->setName('x'));
        $this->assertInstanceOf(Package::class, $package->setDescription('x'));
        $this->assertInstanceOf(Package::class, $package->setTrackingNumber('x'));
    }

    public function testIdNameDescriptionTrackingNumberDefaultToNull()
    {
        $package = new Package(1, 1, 1, 1);
        $this->assertFalse($package->hasId());
        $this->assertFalse($package->hasName());
        $this->assertFalse($package->hasDescription());
        $this->assertFalse($package->hasTrackingNumber());
        $this->assertNull($package->getId());
        $this->assertNull($package->getName());
        $this->assertNull($package->getDescription());
        $this->assertNull($package->getTrackingNumber());
    }

    public function testSetIdNameDescriptionTrackingNumber()
    {
        $package = new Package(1, 1, 1, 1);
        $package->setId('pkg-1')
            ->setName('Small Box')
            ->setDescription('A small box')
            ->setTrackingNumber('1Z999');

        $this->assertEquals('pkg-1', $package->getId());
        $this->assertEquals('Small Box', $package->getName());
        $this->assertEquals('A small box', $package->getDescription());
        $this->assertEquals('1Z999', $package->getTrackingNumber());
        $this->assertTrue($package->hasId());
        $this->assertTrue($package->hasName());
        $this->assertTrue($package->hasDescription());
        $this->assertTrue($package->hasTrackingNumber());
    }

}
