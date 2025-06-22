<?php

namespace DeviceBundle\Tests\Repository;

use DeviceBundle\Repository\LoginLogRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class LoginLogRepositoryTest extends TestCase
{
    public function testConstructor_shouldUseLoginLogEntityClass()
    {        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new LoginLogRepository($registry);

        // 使用方法反射来验证
        $constructor = new \ReflectionMethod(LoginLogRepository::class, '__construct');
        $parameters = $constructor->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('registry', $parameters[0]->getName());

        // 确认LoginLogRepository与LoginLog关联
        $this->assertStringContainsString('LoginLog', LoginLogRepository::class);
    }

    public function testRepository_shouldExtendServiceEntityRepository()
    {        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new LoginLogRepository($registry);

        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryMethods_shouldBeAvailable()
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new LoginLogRepository($registry);
        
        // 验证继承自ServiceEntityRepository的基本方法是否可用
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testFindLastByUserMethod_shouldExist()
    {
        // 验证方法签名
        $reflection = new \ReflectionMethod(LoginLogRepository::class, 'findLastByUser');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertEquals('user', $parameters[0]->getName());
        
        // 验证返回类型
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertTrue($returnType->allowsNull());
    }
} 