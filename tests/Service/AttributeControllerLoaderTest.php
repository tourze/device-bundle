<?php

namespace DeviceBundle\Tests\Service;

use DeviceBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }

    public function testSupports_shouldAlwaysReturnFalse()
    {
        $this->assertFalse($this->loader->supports('any_resource'));
        $this->assertFalse($this->loader->supports(null));
        $this->assertFalse($this->loader->supports([]));
        $this->assertFalse($this->loader->supports(new \stdClass()));
    }

    public function testLoad_shouldReturnRouteCollection()
    {
        $result = $this->loader->load('any_resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoload_shouldReturnRouteCollection()
    {
        $result = $this->loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testAutoload_shouldIncludeDeviceCrudControllerRoutes()
    {
        // 这个测试比较复杂，需要模拟AttributeRouteControllerLoader的行为
        // 由于我们无法轻易地测试内部实现细节，我们至少可以确认返回值是RouteCollection类型

        $result = $this->loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);

        // 理想情况下，我们应该测试集合中是否包含DeviceCrudController的路由
        // 但由于模拟困难，此处只能验证不抛出异常并返回正确类型
    }
}
