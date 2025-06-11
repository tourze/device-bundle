<?php

namespace DeviceBundle\Tests\DependencyInjection;

use DeviceBundle\DependencyInjection\DeviceExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class DeviceExtensionTest extends TestCase
{
    private DeviceExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new DeviceExtension();
        $this->container = new ContainerBuilder();
    }

    public function testExtension_shouldExtendSymfonyExtension()
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function testLoad_shouldNotThrowException()
    {
        // 仅测试加载过程不抛出异常
        $configs = [];

        try {
            $this->extension->load($configs, $this->container);
            $this->assertTrue(true); // 如果没有异常抛出，则测试通过
        } catch  (\Throwable $e) {
            $this->fail('加载扩展时不应抛出异常: ' . $e->getMessage());
        }
    }

    public function testLoad_shouldAddServiceDefinitions()
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 测试是否有服务定义添加到容器中
        $definitions = $this->container->getDefinitions();

        // 检查至少有一些定义被添加
        $this->assertGreaterThan(0, count($definitions));

        // 检查是否添加了控制器资源
        $found = false;
        foreach ($definitions as $id => $definition) {
            if (strpos($id, 'DeviceBundle\\Controller\\') === 0) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            // 检查服务是否通过其他方式加载
            $this->assertNotEmpty($this->container->getResources(), '容器应该加载了一些资源');
        }
    }
}
