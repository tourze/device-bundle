<?php

namespace DeviceBundle\Tests\Controller\Admin;

use DeviceBundle\Controller\Admin\DeviceCrudController;
use DeviceBundle\Entity\Device;
use PHPUnit\Framework\TestCase;

class DeviceCrudControllerTest extends TestCase
{
    private DeviceCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new DeviceCrudController();
    }

    public function testGetEntityFqcn_shouldReturnDeviceClass()
    {
        $this->assertEquals(Device::class, DeviceCrudController::getEntityFqcn());
    }

    public function testController_shouldExtendAbstractCrudController()
    {
        $this->assertInstanceOf('Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController', $this->controller);
    }
}
