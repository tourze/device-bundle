<?php

namespace DeviceBundle\Tests\Service;

use DeviceBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface $linkGenerator;

    protected function onSetUp(): void
    {
        // 创建匿名类实现
        $this->linkGenerator = new class implements LinkGeneratorInterface {
            public function getCurdListPage(string $entityClass): string
            {
                return '/admin/device';
            }

            public function extractEntityFqcn(string $crudController): string
            {
                return 'DeviceBundle\Entity\Device';
            }

            public function setDashboard(mixed $dashboard): void
            {
                // 空实现，满足接口要求
            }
        };

        // 将服务注入容器
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);

        // 从容器获取服务
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testInvokeShouldBeCallable(): void
    {
        // AdminMenu实现了__invoke方法，所以是可调用的
        $reflection = new \ReflectionClass(AdminMenu::class);
        $this->assertTrue($reflection->hasMethod('__invoke'));
    }

    public function testInvokeWithBasicItemShouldNotThrowException(): void
    {
        // 使用一个简单的测试替身而不是复杂的ItemInterface实现
        // 这样可以避免实现所有复杂的接口方法
        $this->expectNotToPerformAssertions();

        // 使用 PHPUnit Mock 避免复杂的匿名类实现
        $item = $this->createMock(ItemInterface::class);

        // 测试只是确保调用不会抛出异常
        try {
            ($this->adminMenu)($item);
        } catch (\Throwable $e) {
            self::fail('调用AdminMenu不应抛出异常: ' . $e->getMessage());
        }
    }
}
