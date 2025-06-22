<?php

namespace DeviceBundle\Tests\Service;

use DeviceBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;

    protected function setUp(): void
    {        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($linkGenerator);
    }

    public function testConstruct_shouldAcceptLinkGenerator()
    {        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $adminMenu = new AdminMenu($linkGenerator);

        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testAdminMenu_shouldImplementMenuProviderInterface()
    {
        $this->assertInstanceOf('Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface', $this->adminMenu);
    }

    public function testInvoke_shouldBeCallable()
    {
        // AdminMenu实现了__invoke方法，所以是可调用的
        $reflection = new \ReflectionClass(AdminMenu::class);
        $this->assertTrue($reflection->hasMethod('__invoke'));
    }

    public function testInvoke_withBasicItem_shouldNotThrowException()
    {
        $item = $this->createMock(ItemInterface::class);
        $deviceMenuItem = $this->createMock(ItemInterface::class);
        $subMenuItem = $this->createMock(ItemInterface::class);
        
        // 模拟getChild方法：第一次返回null，第二次返回设备菜单项
        $item->method('getChild')
            ->with('设备管理')
            ->willReturnOnConsecutiveCalls(null, $deviceMenuItem);
        
        // 模拟addChild方法返回设备菜单项
        $item->method('addChild')->with('设备管理')->willReturn($deviceMenuItem);
        
        // 模拟设备菜单项的addChild方法
        $deviceMenuItem->method('addChild')->willReturn($subMenuItem);
        
        // 模拟子菜单项的方法链
        $subMenuItem->method('setUri')->willReturnSelf();
        $subMenuItem->method('setAttribute')->willReturnSelf();
        
        // 基本测试确保方法调用不会抛出异常
        try {
            ($this->adminMenu)($item);
            $this->assertTrue(true); // 如果没有异常抛出，则测试通过
        } catch (\Throwable $e) {
            $this->fail('调用AdminMenu不应抛出异常: ' . $e->getMessage());
        }
    }

    public function testAdminMenu_shouldHaveConstructorDependency()
    {
        $reflection = new \ReflectionClass(AdminMenu::class);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertEquals('linkGenerator', $parameters[0]->getName());
    }
} 