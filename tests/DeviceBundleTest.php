<?php

namespace DeviceBundle\Tests;

use DeviceBundle\DeviceBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DeviceBundleTest extends TestCase
{
    public function testBundle_shouldExtendSymfonyBundle()
    {
        $bundle = new DeviceBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testGetPath_shouldReturnValidPath()
    {
        $bundle = new DeviceBundle();
        $path = $bundle->getPath();
        $this->assertDirectoryExists($path);
        $this->assertStringContainsString('device-bundle', $path);
    }
}
