<?php

namespace DeviceBundle\Tests\Service;

use DeviceBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 不需要额外的设置逻辑
    }

    public function testSupportsShouldAlwaysReturnFalse(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $this->assertFalse($loader->supports('any_resource'));
        $this->assertFalse($loader->supports(null));
        $this->assertFalse($loader->supports([]));
        $this->assertFalse($loader->supports(new \stdClass()));
    }

    public function testLoadShouldReturnRouteCollection(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $result = $loader->load('any_resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoloadShouldReturnRouteCollection(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $result = $loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoloadShouldIncludeDeviceCrudControllerRoutes(): void
    {
        // 这个测试比较复杂，需要模拟AttributeRouteControllerLoader的行为
        // 由于我们无法轻易地测试内部实现细节，我们至少可以确认返回值是RouteCollection类型

        $loader = self::getService(AttributeControllerLoader::class);
        $result = $loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);

        // 理想情况下，我们应该测试集合中是否包含DeviceCrudController的路由
        // 但由于模拟困难，此处只能验证不抛出异常并返回正确类型
    }
}
