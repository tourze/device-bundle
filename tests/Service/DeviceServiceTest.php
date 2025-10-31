<?php

namespace DeviceBundle\Tests\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Repository\DeviceRepository;
use DeviceBundle\Service\DeviceService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DeviceService::class)]
final class DeviceServiceTest extends TestCase
{
    public function testRegisterNewDevice(): void
    {
        $model = 'iPhone 15';
        $code = 'ABC123';

        // 使用具体类 DeviceRepository 是必要的，因为：
        // 1. DeviceService 构造函数明确要求此具体类型
        // 2. 该类没有对应的接口可以替代
        // 3. 这是 Doctrine Repository 的标准使用模式
        $deviceRepository = $this->createMock(DeviceRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $deviceRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn(null)
        ;

        $entityManager->expects($this->once())
            ->method('persist')
            ->with(self::isInstanceOf(Device::class))
        ;

        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $service = new DeviceService($deviceRepository, $entityManager);
        $device = $service->register($model, $code);

        $this->assertEquals($code, $device->getCode());
        $this->assertEquals($model, $device->getModel());
    }

    public function testRegisterExistingDevice(): void
    {
        $model = 'iPhone 15';
        $code = 'ABC123';

        $existingDevice = new Device();
        $existingDevice->setCode($code);
        $existingDevice->setModel('iPhone 14');

        // 使用具体类 DeviceRepository 是必要的，因为：
        // 1. DeviceService 构造函数明确要求此具体类型
        // 2. 该类没有对应的接口可以替代
        // 3. 这是 Doctrine Repository 的标准使用模式
        $deviceRepository = $this->createMock(DeviceRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $deviceRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn($existingDevice)
        ;

        $entityManager->expects($this->never())
            ->method('persist')
        ;

        $entityManager->expects($this->never())
            ->method('flush')
        ;

        $service = new DeviceService($deviceRepository, $entityManager);
        $device = $service->register($model, $code);

        $this->assertSame($existingDevice, $device);
        $this->assertEquals('iPhone 14', $device->getModel());
    }
}
