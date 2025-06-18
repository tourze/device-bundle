<?php

namespace DeviceBundle\Tests\DataFixtures;

use DeviceBundle\DataFixtures\DeviceFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use PHPUnit\Framework\TestCase;

class DeviceFixturesTest extends TestCase
{
    private DeviceFixtures $fixtures;

    protected function setUp(): void
    {
        $this->fixtures = new DeviceFixtures();
    }

    public function testFixtures_shouldExtendDoctrineFixture()
    {
        $this->assertInstanceOf(Fixture::class, $this->fixtures);
    }

    public function testFixtures_shouldHaveCorrectReferenceConstants()
    {
        $this->assertTrue(defined('DeviceBundle\DataFixtures\DeviceFixtures::DEVICE_REFERENCE_1'));
        $this->assertTrue(defined('DeviceBundle\DataFixtures\DeviceFixtures::DEVICE_REFERENCE_2'));
        $this->assertTrue(defined('DeviceBundle\DataFixtures\DeviceFixtures::DEVICE_REFERENCE_3'));

        $this->assertEquals('device-1', DeviceFixtures::DEVICE_REFERENCE_1);
        $this->assertEquals('device-2', DeviceFixtures::DEVICE_REFERENCE_2);
        $this->assertEquals('device-3', DeviceFixtures::DEVICE_REFERENCE_3);
    }

    public function testConstants_shouldHaveCorrectValues()
    {
        $reflectionClass = new \ReflectionClass(DeviceFixtures::class);
        
        $constants = $reflectionClass->getConstants();
        
        $this->assertArrayHasKey('DEVICE_REFERENCE_1', $constants);
        $this->assertArrayHasKey('DEVICE_REFERENCE_2', $constants);
        $this->assertArrayHasKey('DEVICE_REFERENCE_3', $constants);

    }

    public function testLoadMethod_shouldExist()
    {
        $this->assertTrue(method_exists(DeviceFixtures::class, 'load'));
        
        $reflection = new \ReflectionMethod(DeviceFixtures::class, 'load');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('manager', $parameters[0]->getName());
    }
} 