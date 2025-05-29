<?php

namespace DeviceBundle\Tests\Controller\Admin;

use DeviceBundle\Controller\Admin\LoginLogCrudController;
use DeviceBundle\Entity\LoginLog;
use PHPUnit\Framework\TestCase;

class LoginLogCrudControllerTest extends TestCase
{
    private LoginLogCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new LoginLogCrudController();
    }

    public function testGetEntityFqcn_shouldReturnLoginLogClass()
    {
        $this->assertEquals(LoginLog::class, LoginLogCrudController::getEntityFqcn());
    }

    public function testController_shouldExtendAbstractCrudController()
    {
        $this->assertInstanceOf('EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController', $this->controller);
    }
}
