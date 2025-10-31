<?php

namespace DeviceBundle\Tests\DependencyInjection;

use DeviceBundle\DependencyInjection\DeviceExtension;
use DeviceBundle\Repository\DeviceRepository;
use DeviceBundle\Repository\LoginLogRepository;
use DeviceBundle\Service\DeviceService;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
#[CoversClass(DeviceExtension::class)]
final class DeviceExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private DeviceExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new DeviceExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testLoadServices(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        $this->assertTrue($this->container->hasDefinition(DeviceService::class));
        $this->assertTrue($this->container->hasDefinition(DeviceRepository::class));
        $this->assertTrue($this->container->hasDefinition(LoginLogRepository::class));
    }

    public function testInstanceOfExtension(): void
    {
        $this->assertInstanceOf(AutoExtension::class, $this->extension);
    }
}
