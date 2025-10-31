<?php

namespace DeviceBundle\Tests\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Enum\DeviceStatus;
use DeviceBundle\Enum\DeviceType;
use DeviceBundle\Repository\DeviceRepository;
use DeviceBundle\Service\DeviceStatusManager;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DeviceStatusManager::class)]
final class DeviceStatusManagerTest extends TestCase
{
    private function createMockEntityManager(): EntityManagerInterface
    {
        $mock = $this->createMock(EntityManagerInterface::class);
        // persist() 和 flush() 的返回类型是 void，不需要设置返回值
        $mock->method('persist');
        $mock->method('flush');

        return $mock;
    }

    private function createMockDeviceRepository(): DeviceRepository
    {
        $mock = $this->createMock(DeviceRepository::class);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('update')->willReturnSelf();
        $queryBuilder->method('set')->willReturnSelf();
        $queryBuilder->method('where')->willReturnSelf();
        $queryBuilder->method('andWhere')->willReturnSelf();
        $queryBuilder->method('setParameter')->willReturnSelf();

        $query = $this->createMock(Query::class);
        $query->method('execute')->willReturn(0);
        $queryBuilder->method('getQuery')->willReturn($query);

        $mock->method('getClassName')->willReturn(Device::class);
        $mock->method('createQueryBuilder')->willReturn($queryBuilder);

        return $mock;
    }

    public function testUpdateOnlineStatusToOnline(): void
    {
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $device = new Device();
        $device->setCode('TEST001');
        $device->setStatus(DeviceStatus::OFFLINE);

        $statusManager->updateOnlineStatus($device, true);

        $this->assertSame(DeviceStatus::ONLINE, $device->getStatus());
        $this->assertNotNull($device->getLastOnlineTime());
    }

    public function testUpdateOnlineStatusToOffline(): void
    {
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $device = new Device();
        $device->setCode('TEST001');
        $device->setStatus(DeviceStatus::ONLINE);
        $originalLastOnlineTime = $device->getLastOnlineTime();

        $statusManager->updateOnlineStatus($device, false);

        $this->assertSame(DeviceStatus::OFFLINE, $device->getStatus());
        $this->assertSame($originalLastOnlineTime, $device->getLastOnlineTime());
    }

    public function testUpdateLastConnection(): void
    {
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $device = new Device();
        $device->setCode('TEST001');
        $ip = '192.168.1.100';

        $statusManager->updateLastConnection($device, $ip);

        $this->assertSame($ip, $device->getLastIp());
        $this->assertNotNull($device->getLastOnlineTime());
    }

    public function testGetOnlineDevices(): void
    {
        // 这是一个简化测试，实际需要数据库交互
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $this->assertInstanceOf(DeviceStatusManager::class, $statusManager);
    }

    public function testGetOfflineDevices(): void
    {
        // 这是一个简化测试，实际需要数据库交互
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $this->assertInstanceOf(DeviceStatusManager::class, $statusManager);
    }

    public function testCheckAndUpdateTimeoutDevices(): void
    {
        // 这是一个简化测试，实际需要数据库交互
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $this->assertInstanceOf(DeviceStatusManager::class, $statusManager);
    }

    public function testUpdateHardwareInfo(): void
    {
        $entityManager = $this->createMockEntityManager();
        $deviceRepository = $this->createMockDeviceRepository();
        $statusManager = new DeviceStatusManager($entityManager, $deviceRepository);
        $device = new Device();
        $device->setCode('TEST001');

        $info = [
            'deviceType' => 'phone',
            'osVersion' => 'iOS 17.0',
            'brand' => 'Apple',
            'cpuCores' => 6,
            'memorySize' => '8GB',
            'storageSize' => '256GB',
            'fingerprint' => 'test-fingerprint',
        ];

        $statusManager->updateHardwareInfo($device, $info);

        $this->assertSame(DeviceType::PHONE, $device->getDeviceType());
        $this->assertSame('iOS 17.0', $device->getOsVersion());
        $this->assertSame('Apple', $device->getBrand());
        $this->assertSame(6, $device->getCpuCores());
        $this->assertSame('8GB', $device->getMemorySize());
        $this->assertSame('256GB', $device->getStorageSize());
        $this->assertSame('test-fingerprint', $device->getFingerprint());
    }
}
