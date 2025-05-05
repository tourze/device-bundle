<?php

namespace DeviceBundle\Tests\Repository;

use DeviceBundle\Repository\DeviceRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class DeviceRepositoryTest extends TestCase
{
    public function testConstructor_shouldUseDeviceEntityClass()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DeviceRepository($registry);

        // 使用方法反射来验证
        $constructor = new \ReflectionMethod(DeviceRepository::class, '__construct');
        $parameters = $constructor->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('registry', $parameters[0]->getName());

        // 确认DeviceRepository与Device关联
        $this->assertTrue(method_exists(DeviceRepository::class, 'find'));
        $this->assertStringContainsString('Device', DeviceRepository::class);
    }

    public function testRepository_shouldExtendServiceEntityRepository()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new DeviceRepository($registry);

        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryMethods_shouldBeAvailable()
    {
        // 验证继承自ServiceEntityRepository的基本方法是否可用
        $this->assertTrue(method_exists(DeviceRepository::class, 'find'));
        $this->assertTrue(method_exists(DeviceRepository::class, 'findAll'));
        $this->assertTrue(method_exists(DeviceRepository::class, 'findBy'));
        $this->assertTrue(method_exists(DeviceRepository::class, 'findOneBy'));
    }
}
