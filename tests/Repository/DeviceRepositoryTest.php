<?php

namespace DeviceBundle\Tests\Repository;

use DeviceBundle\Entity\Device;
use DeviceBundle\Enum\DeviceStatus;
use DeviceBundle\Enum\DeviceType;
use DeviceBundle\Repository\DeviceRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DeviceRepository::class)]
#[RunTestsInSeparateProcesses]
final class DeviceRepositoryTest extends AbstractRepositoryTestCase
{
    private DeviceRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(DeviceRepository::class);
    }

    public function testFindByCodeShouldReturnCorrectDevice(): void
    {
        $device = $this->createTestDevice();

        $result = $this->repository->findOneBy(['code' => $device->getCode()]);

        $this->assertInstanceOf(Device::class, $result);
        $this->assertEquals($device->getCode(), $result->getCode());
    }

    public function testFindByValidStatusShouldReturnCorrectDevices(): void
    {
        $this->clearDatabase();
        $validDevice = $this->createTestDevice(true);
        $invalidDevice = $this->createTestDevice(false);

        $validDevices = $this->repository->findBy(['valid' => true]);
        $invalidDevices = $this->repository->findBy(['valid' => false]);

        $this->assertCount(1, $validDevices);
        $this->assertCount(1, $invalidDevices);
        $this->assertTrue($validDevices[0]->isValid());
        $this->assertFalse($invalidDevices[0]->isValid());
    }

    public function testFindByWithOrderingShouldReturnOrderedResults(): void
    {
        $this->clearDatabase();
        $device1 = $this->createTestDevice(true, 'DEVICE_A');
        $device2 = $this->createTestDevice(true, 'DEVICE_B');

        $ascending = $this->repository->findBy([], ['code' => 'ASC']);
        $descending = $this->repository->findBy([], ['code' => 'DESC']);

        $this->assertEquals('DEVICE_A', $ascending[0]->getCode());
        $this->assertEquals('DEVICE_B', $descending[0]->getCode());
    }

    public function testFindByWithLimitShouldReturnLimitedResults(): void
    {
        $this->clearDatabase();
        $this->createTestDevice();
        $this->createTestDevice();
        $this->createTestDevice();

        $result = $this->repository->findBy([], null, 2);

        $this->assertCount(2, $result);
    }

    public function testCountWhenNoRecordsExistShouldReturnZero(): void
    {
        $this->clearDatabase();

        $count = $this->repository->count();

        $this->assertEquals(0, $count);
    }

    public function testCountWhenRecordsExistShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();
        $this->createTestDevice();
        $this->createTestDevice();

        $count = $this->repository->count();

        $this->assertEquals(2, $count);
    }

    public function testCountWithCriteriaShouldReturnFilteredCount(): void
    {
        $this->clearDatabase();
        $this->createTestDevice(true);
        $this->createTestDevice(false);

        $validCount = $this->repository->count(['valid' => true]);
        $invalidCount = $this->repository->count(['valid' => false]);

        $this->assertEquals(1, $validCount);
        $this->assertEquals(1, $invalidCount);
    }

    public function testSaveShouldPersistDevice(): void
    {
        $device = new Device();
        $device->setCode('SAVE_TEST_' . uniqid());
        $device->setModel('Test Model');
        $device->setRegIp('192.168.1.1');
        $device->setValid(true);

        $this->repository->save($device);

        $this->assertNotNull($device->getId());

        $found = $this->repository->find($device->getId());
        $this->assertInstanceOf(Device::class, $found);
        $this->assertEquals($device->getCode(), $found->getCode());
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $device = new Device();
        $deviceCode = 'NO_FLUSH_TEST_' . uniqid();
        $device->setCode($deviceCode);
        $device->setModel('Test Model');
        $device->setRegIp('192.168.1.1');
        $device->setValid(true);

        $this->repository->save($device, false);

        // EntityManager::flush() 后应该有ID
        self::getEntityManager()->flush();
        $this->assertNotNull($device->getId());

        $found = $this->repository->findOneBy(['code' => $deviceCode]);
        $this->assertInstanceOf(Device::class, $found);
    }

    public function testRemoveShouldDeleteDevice(): void
    {
        $device = $this->createTestDevice();
        $deviceId = $device->getId();

        $this->repository->remove($device);

        $found = $this->repository->find($deviceId);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotDeleteImmediately(): void
    {
        $device = $this->createTestDevice();
        $deviceId = $device->getId();

        $this->repository->remove($device, false);

        $found = $this->repository->find($deviceId);
        $this->assertInstanceOf(Device::class, $found);

        self::getEntityManager()->flush();

        $found = $this->repository->find($deviceId);
        $this->assertNull($found);
    }

    public function testFindByInvalidFieldShouldThrowException(): void
    {
        $this->expectException(UnrecognizedField::class);

        $this->repository->findBy(['nonExistentField' => 'value']);
    }

    public function testFindOneByWithOrderingShouldReturnFirstMatchingEntity(): void
    {
        $this->clearDatabase();
        $device1 = $this->createTestDevice(true, 'DEVICE_A');
        $device2 = $this->createTestDevice(true, 'DEVICE_B');

        $result = $this->repository->findOneBy(['valid' => true], ['code' => 'ASC']);

        $this->assertInstanceOf(Device::class, $result);
        $this->assertEquals('DEVICE_A', $result->getCode());
    }

    public function testFindByNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithName = $this->createTestDevice(true, 'DEVICE_WITH_NAME');
        $deviceWithName->setName('Device Name');
        self::getEntityManager()->flush();

        $deviceWithoutName = $this->createTestDevice(true, 'DEVICE_WITHOUT_NAME');
        $deviceWithoutName->setName(null);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['name' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_NAME', $result[0]->getCode());
    }

    public function testFindByModelNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithModel = $this->createTestDevice(true, 'DEVICE_WITH_MODEL');
        $deviceWithModel->setModel('Some Model');
        self::getEntityManager()->flush();

        $deviceWithoutModel = new Device();
        $deviceWithoutModel->setCode('DEVICE_WITHOUT_MODEL');
        $deviceWithoutModel->setModel(null);
        $deviceWithoutModel->setRegIp('127.0.0.1');
        $deviceWithoutModel->setValid(true);
        self::getEntityManager()->persist($deviceWithoutModel);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['model' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_MODEL', $result[0]->getCode());
    }

    public function testCountNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithName = $this->createTestDevice(true, 'DEVICE_WITH_NAME');
        $deviceWithName->setName('Device Name');
        self::getEntityManager()->flush();

        $deviceWithoutName = $this->createTestDevice(true, 'DEVICE_WITHOUT_NAME');
        $deviceWithoutName->setName(null);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['name' => null]);

        $this->assertEquals(1, $count);
    }

    public function testCountModelNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithModel = $this->createTestDevice(true, 'DEVICE_WITH_MODEL');
        $deviceWithModel->setModel('Some Model');
        self::getEntityManager()->flush();

        $deviceWithoutModel = new Device();
        $deviceWithoutModel->setCode('DEVICE_WITHOUT_MODEL');
        $deviceWithoutModel->setModel(null);
        $deviceWithoutModel->setRegIp('127.0.0.1');
        $deviceWithoutModel->setValid(true);
        self::getEntityManager()->persist($deviceWithoutModel);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['model' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByValidNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithValid = $this->createTestDevice(true, 'DEVICE_VALID');
        $deviceWithValid->setValid(true);
        self::getEntityManager()->flush();

        $deviceWithNullValid = new Device();
        $deviceWithNullValid->setCode('DEVICE_NULL_VALID');
        $deviceWithNullValid->setModel('Test Model');
        $deviceWithNullValid->setRegIp('127.0.0.1');
        $deviceWithNullValid->setValid(null);
        self::getEntityManager()->persist($deviceWithNullValid);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['valid' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_NULL_VALID', $result[0]->getCode());
    }

    public function testCountValidNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithValid = $this->createTestDevice(true, 'DEVICE_VALID');
        $deviceWithValid->setValid(true);
        self::getEntityManager()->flush();

        $deviceWithNullValid = new Device();
        $deviceWithNullValid->setCode('DEVICE_NULL_VALID');
        $deviceWithNullValid->setModel('Test Model');
        $deviceWithNullValid->setRegIp('127.0.0.1');
        $deviceWithNullValid->setValid(null);
        self::getEntityManager()->persist($deviceWithNullValid);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['valid' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByRegIpNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithRegIp = $this->createTestDevice(true, 'DEVICE_WITH_REG_IP');
        $deviceWithRegIp->setRegIp('192.168.1.100');
        self::getEntityManager()->flush();

        $deviceWithNullRegIp = new Device();
        $deviceWithNullRegIp->setCode('DEVICE_NULL_REG_IP');
        $deviceWithNullRegIp->setModel('Test Model');
        $deviceWithNullRegIp->setRegIp(null);
        $deviceWithNullRegIp->setValid(true);
        self::getEntityManager()->persist($deviceWithNullRegIp);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['regIp' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_NULL_REG_IP', $result[0]->getCode());
    }

    public function testCountRegIpNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithRegIp = $this->createTestDevice(true, 'DEVICE_WITH_REG_IP');
        $deviceWithRegIp->setRegIp('192.168.1.100');
        self::getEntityManager()->flush();

        $deviceWithNullRegIp = new Device();
        $deviceWithNullRegIp->setCode('DEVICE_NULL_REG_IP');
        $deviceWithNullRegIp->setModel('Test Model');
        $deviceWithNullRegIp->setRegIp(null);
        $deviceWithNullRegIp->setValid(true);
        self::getEntityManager()->persist($deviceWithNullRegIp);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['regIp' => null]);

        $this->assertEquals(1, $count);
    }

    private function createTestDevice(bool $valid = true, ?string $code = null): Device
    {
        $device = new Device();
        $device->setCode($code ?? 'TEST_' . uniqid());
        $device->setModel('Test Model');
        $device->setName('Test Device');
        $device->setRegIp('127.0.0.1');
        $device->setValid($valid);

        self::getEntityManager()->persist($device);
        self::getEntityManager()->flush();

        return $device;
    }

    public function testFindOneByWithComplexSortingShouldReturnCorrectDevice(): void
    {
        $this->clearDatabase();
        $device1 = $this->createTestDevice(true, 'DEVICE_A');
        $device1->setModel('Model Z');
        self::getEntityManager()->flush();

        $device2 = $this->createTestDevice(true, 'DEVICE_B');
        $device2->setModel('Model A');
        self::getEntityManager()->flush();

        $device3 = $this->createTestDevice(true, 'DEVICE_C');
        $device3->setModel('Model M');
        self::getEntityManager()->flush();

        // 测试多字段排序
        $result = $this->repository->findOneBy(['valid' => true], ['model' => 'ASC', 'code' => 'DESC']);

        $this->assertInstanceOf(Device::class, $result);
        $this->assertEquals('Model A', $result->getModel());
    }

    public function testFindByDeviceTypeNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithType = $this->createTestDevice(true, 'DEVICE_WITH_TYPE');
        $deviceWithType->setDeviceType(DeviceType::PHONE);
        self::getEntityManager()->flush();

        $deviceWithoutType = new Device();
        $deviceWithoutType->setCode('DEVICE_WITHOUT_TYPE');
        $deviceWithoutType->setModel('Test Model');
        $deviceWithoutType->setRegIp('127.0.0.1');
        $deviceWithoutType->setValid(true);
        $deviceWithoutType->setDeviceType(null);
        self::getEntityManager()->persist($deviceWithoutType);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['deviceType' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_TYPE', $result[0]->getCode());
    }

    public function testCountDeviceTypeNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithType = $this->createTestDevice(true, 'DEVICE_WITH_TYPE');
        $deviceWithType->setDeviceType(DeviceType::PHONE);
        self::getEntityManager()->flush();

        $deviceWithoutType = new Device();
        $deviceWithoutType->setCode('DEVICE_WITHOUT_TYPE');
        $deviceWithoutType->setModel('Test Model');
        $deviceWithoutType->setRegIp('127.0.0.1');
        $deviceWithoutType->setValid(true);
        $deviceWithoutType->setDeviceType(null);
        self::getEntityManager()->persist($deviceWithoutType);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['deviceType' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByOsVersionNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithOsVersion = $this->createTestDevice(true, 'DEVICE_WITH_OS_VERSION');
        $deviceWithOsVersion->setOsVersion('Android 12');
        self::getEntityManager()->flush();

        $deviceWithoutOsVersion = new Device();
        $deviceWithoutOsVersion->setCode('DEVICE_WITHOUT_OS_VERSION');
        $deviceWithoutOsVersion->setModel('Test Model');
        $deviceWithoutOsVersion->setRegIp('127.0.0.1');
        $deviceWithoutOsVersion->setValid(true);
        $deviceWithoutOsVersion->setOsVersion(null);
        self::getEntityManager()->persist($deviceWithoutOsVersion);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['osVersion' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_OS_VERSION', $result[0]->getCode());
    }

    public function testCountOsVersionNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithOsVersion = $this->createTestDevice(true, 'DEVICE_WITH_OS_VERSION');
        $deviceWithOsVersion->setOsVersion('Android 12');
        self::getEntityManager()->flush();

        $deviceWithoutOsVersion = new Device();
        $deviceWithoutOsVersion->setCode('DEVICE_WITHOUT_OS_VERSION');
        $deviceWithoutOsVersion->setModel('Test Model');
        $deviceWithoutOsVersion->setRegIp('127.0.0.1');
        $deviceWithoutOsVersion->setValid(true);
        $deviceWithoutOsVersion->setOsVersion(null);
        self::getEntityManager()->persist($deviceWithoutOsVersion);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['osVersion' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByBrandNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithBrand = $this->createTestDevice(true, 'DEVICE_WITH_BRAND');
        $deviceWithBrand->setBrand('Samsung');
        self::getEntityManager()->flush();

        $deviceWithoutBrand = new Device();
        $deviceWithoutBrand->setCode('DEVICE_WITHOUT_BRAND');
        $deviceWithoutBrand->setModel('Test Model');
        $deviceWithoutBrand->setRegIp('127.0.0.1');
        $deviceWithoutBrand->setValid(true);
        $deviceWithoutBrand->setBrand(null);
        self::getEntityManager()->persist($deviceWithoutBrand);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['brand' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_BRAND', $result[0]->getCode());
    }

    public function testCountBrandNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithBrand = $this->createTestDevice(true, 'DEVICE_WITH_BRAND');
        $deviceWithBrand->setBrand('Samsung');
        self::getEntityManager()->flush();

        $deviceWithoutBrand = new Device();
        $deviceWithoutBrand->setCode('DEVICE_WITHOUT_BRAND');
        $deviceWithoutBrand->setModel('Test Model');
        $deviceWithoutBrand->setRegIp('127.0.0.1');
        $deviceWithoutBrand->setValid(true);
        $deviceWithoutBrand->setBrand(null);
        self::getEntityManager()->persist($deviceWithoutBrand);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['brand' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByLastOnlineTimeNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithLastOnlineTime = $this->createTestDevice(true, 'DEVICE_WITH_LAST_ONLINE_TIME');
        $deviceWithLastOnlineTime->setLastOnlineTime(new \DateTimeImmutable());
        self::getEntityManager()->flush();

        $deviceWithoutLastOnlineTime = new Device();
        $deviceWithoutLastOnlineTime->setCode('DEVICE_WITHOUT_LAST_ONLINE_TIME');
        $deviceWithoutLastOnlineTime->setModel('Test Model');
        $deviceWithoutLastOnlineTime->setRegIp('127.0.0.1');
        $deviceWithoutLastOnlineTime->setValid(true);
        $deviceWithoutLastOnlineTime->setLastOnlineTime(null);
        self::getEntityManager()->persist($deviceWithoutLastOnlineTime);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['lastOnlineTime' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_LAST_ONLINE_TIME', $result[0]->getCode());
    }

    public function testCountLastOnlineTimeNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithLastOnlineTime = $this->createTestDevice(true, 'DEVICE_WITH_LAST_ONLINE_TIME');
        $deviceWithLastOnlineTime->setLastOnlineTime(new \DateTimeImmutable());
        self::getEntityManager()->flush();

        $deviceWithoutLastOnlineTime = new Device();
        $deviceWithoutLastOnlineTime->setCode('DEVICE_WITHOUT_LAST_ONLINE_TIME');
        $deviceWithoutLastOnlineTime->setModel('Test Model');
        $deviceWithoutLastOnlineTime->setRegIp('127.0.0.1');
        $deviceWithoutLastOnlineTime->setValid(true);
        $deviceWithoutLastOnlineTime->setLastOnlineTime(null);
        self::getEntityManager()->persist($deviceWithoutLastOnlineTime);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['lastOnlineTime' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByLastIpNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithLastIp = $this->createTestDevice(true, 'DEVICE_WITH_LAST_IP');
        $deviceWithLastIp->setLastIp('192.168.1.100');
        self::getEntityManager()->flush();

        $deviceWithoutLastIp = new Device();
        $deviceWithoutLastIp->setCode('DEVICE_WITHOUT_LAST_IP');
        $deviceWithoutLastIp->setModel('Test Model');
        $deviceWithoutLastIp->setRegIp('127.0.0.1');
        $deviceWithoutLastIp->setValid(true);
        $deviceWithoutLastIp->setLastIp(null);
        self::getEntityManager()->persist($deviceWithoutLastIp);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['lastIp' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_LAST_IP', $result[0]->getCode());
    }

    public function testCountLastIpNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithLastIp = $this->createTestDevice(true, 'DEVICE_WITH_LAST_IP');
        $deviceWithLastIp->setLastIp('192.168.1.100');
        self::getEntityManager()->flush();

        $deviceWithoutLastIp = new Device();
        $deviceWithoutLastIp->setCode('DEVICE_WITHOUT_LAST_IP');
        $deviceWithoutLastIp->setModel('Test Model');
        $deviceWithoutLastIp->setRegIp('127.0.0.1');
        $deviceWithoutLastIp->setValid(true);
        $deviceWithoutLastIp->setLastIp(null);
        self::getEntityManager()->persist($deviceWithoutLastIp);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['lastIp' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByFingerprintNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithFingerprint = $this->createTestDevice(true, 'DEVICE_WITH_FINGERPRINT');
        $deviceWithFingerprint->setFingerprint('device_fingerprint_hash');
        self::getEntityManager()->flush();

        $deviceWithoutFingerprint = new Device();
        $deviceWithoutFingerprint->setCode('DEVICE_WITHOUT_FINGERPRINT');
        $deviceWithoutFingerprint->setModel('Test Model');
        $deviceWithoutFingerprint->setRegIp('127.0.0.1');
        $deviceWithoutFingerprint->setValid(true);
        $deviceWithoutFingerprint->setFingerprint(null);
        self::getEntityManager()->persist($deviceWithoutFingerprint);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['fingerprint' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_FINGERPRINT', $result[0]->getCode());
    }

    public function testCountFingerprintNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithFingerprint = $this->createTestDevice(true, 'DEVICE_WITH_FINGERPRINT');
        $deviceWithFingerprint->setFingerprint('device_fingerprint_hash');
        self::getEntityManager()->flush();

        $deviceWithoutFingerprint = new Device();
        $deviceWithoutFingerprint->setCode('DEVICE_WITHOUT_FINGERPRINT');
        $deviceWithoutFingerprint->setModel('Test Model');
        $deviceWithoutFingerprint->setRegIp('127.0.0.1');
        $deviceWithoutFingerprint->setValid(true);
        $deviceWithoutFingerprint->setFingerprint(null);
        self::getEntityManager()->persist($deviceWithoutFingerprint);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['fingerprint' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByRemarkNullFieldValueShouldReturnMatchingDevices(): void
    {
        $this->clearDatabase();
        $deviceWithRemark = $this->createTestDevice(true, 'DEVICE_WITH_REMARK');
        $deviceWithRemark->setRemark('This is a test device');
        self::getEntityManager()->flush();

        $deviceWithoutRemark = new Device();
        $deviceWithoutRemark->setCode('DEVICE_WITHOUT_REMARK');
        $deviceWithoutRemark->setModel('Test Model');
        $deviceWithoutRemark->setRegIp('127.0.0.1');
        $deviceWithoutRemark->setValid(true);
        $deviceWithoutRemark->setRemark(null);
        self::getEntityManager()->persist($deviceWithoutRemark);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['remark' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('DEVICE_WITHOUT_REMARK', $result[0]->getCode());
    }

    public function testCountRemarkNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $deviceWithRemark = $this->createTestDevice(true, 'DEVICE_WITH_REMARK');
        $deviceWithRemark->setRemark('This is a test device');
        self::getEntityManager()->flush();

        $deviceWithoutRemark = new Device();
        $deviceWithoutRemark->setCode('DEVICE_WITHOUT_REMARK');
        $deviceWithoutRemark->setModel('Test Model');
        $deviceWithoutRemark->setRegIp('127.0.0.1');
        $deviceWithoutRemark->setValid(true);
        $deviceWithoutRemark->setRemark(null);
        self::getEntityManager()->persist($deviceWithoutRemark);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['remark' => null]);

        $this->assertEquals(1, $count);
    }

    private function clearDatabase(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM DeviceBundle\Entity\Device')->execute();
        self::getEntityManager()->clear();
    }

    /**
     * @return Device
     */
    protected function createNewEntity(): object
    {
        $entity = new Device();

        // 设置基本字段
        $entity->setCode('TEST_' . uniqid());
        $entity->setModel('Test Model');
        $entity->setName('Test Device ' . uniqid());
        $entity->setRegIp('127.0.0.1');
        $entity->setValid(true);
        $entity->setStatus(DeviceStatus::ONLINE);
        $entity->setDeviceType(DeviceType::PHONE);

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository&DeviceRepository
    {
        return $this->repository;
    }
}
