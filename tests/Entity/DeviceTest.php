<?php

namespace DeviceBundle\Tests\Entity;

use DeviceBundle\Entity\Device;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceTest extends TestCase
{
    public function testConstruct_shouldInitializeUsers()
    {
        $device = new Device();
        $this->assertInstanceOf(ArrayCollection::class, $device->getUsers());
        $this->assertCount(0, $device->getUsers());
    }

    public function testToString_withoutId_shouldReturnEmptyString()
    {
        $device = new Device();
        $this->assertSame('', (string)$device);
    }

    public function testToString_withIdAndAttributes_shouldReturnFormattedString()
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $device->setCode('DEVICE001');
        $device->setName('Test Device');

        $this->assertSame('DEVICE001 | Test Device', (string)$device);
    }

    public function testSetGetId()
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $this->assertSame('123456789', $device->getId());
    }

    public function testSetGetCode()
    {
        $device = new Device();
        $device->setCode('DEVICE001');
        $this->assertSame('DEVICE001', $device->getCode());
    }

    public function testSetGetModel()
    {
        $device = new Device();
        $device->setModel('TestModel');
        $this->assertSame('TestModel', $device->getModel());
    }

    public function testSetGetName()
    {
        $device = new Device();
        $device->setName('Test Device');
        $this->assertSame('Test Device', $device->getName());

        $device->setName(null);
        $this->assertNull($device->getName());
    }

    public function testAddRemoveUser()
    {
        $device = new Device();
        $user = $this->createMock(UserInterface::class);

        $device->addUser($user);
        $this->assertCount(1, $device->getUsers());
        $this->assertTrue($device->getUsers()->contains($user));

        $device->removeUser($user);
        $this->assertCount(0, $device->getUsers());
        $this->assertFalse($device->getUsers()->contains($user));
    }

    public function testAddUser_withExistingUser_shouldNotAddDuplicate()
    {
        $device = new Device();
        $user = $this->createMock(UserInterface::class);

        $device->addUser($user);
        $device->addUser($user); // 添加相同的用户第二次

        $this->assertCount(1, $device->getUsers());
    }

    public function testSetGetRegIp()
    {
        $device = new Device();
        $device->setRegIp('127.0.0.1');
        $this->assertSame('127.0.0.1', $device->getRegIp());

        $device->setRegIp(null);
        $this->assertNull($device->getRegIp());
    }

    public function testSetGetValid()
    {
        $device = new Device();
        $this->assertFalse($device->isValid());

        $device->setValid(true);
        $this->assertTrue($device->isValid());

        $device->setValid(null);
        $this->assertNull($device->isValid());
    }

    public function testSetGetCreateTime()
    {
        $device = new Device();
        $now = new \DateTimeImmutable();

        $device->setCreateTime($now);
        $this->assertSame($now, $device->getCreateTime());

        $device->setCreateTime(null);
        $this->assertNull($device->getCreateTime());
    }

    public function testSetGetUpdateTime()
    {
        $device = new Device();
        $now = new \DateTimeImmutable();

        $device->setUpdateTime($now);
        $this->assertSame($now, $device->getUpdateTime());

        $device->setUpdateTime(null);
        $this->assertNull($device->getUpdateTime());
    }

    public function testGetUserCount()
    {
        $device = new Device();
        $this->assertSame(0, $device->getUserCount());

        $user1 = $this->createMock(UserInterface::class);
        $user2 = $this->createMock(UserInterface::class);

        $device->addUser($user1);
        $this->assertSame(1, $device->getUserCount());

        $device->addUser($user2);
        $this->assertSame(2, $device->getUserCount());

        $device->removeUser($user1);
        $this->assertSame(1, $device->getUserCount());
    }

    public function testSetCode_withEmptyString_shouldSetEmptyString()
    {
        $device = new Device();
        $device->setCode('');
        $this->assertSame('', $device->getCode());
    }

    public function testSetCode_withLongString_shouldSetLongString()
    {
        $device = new Device();
        $longCode = str_repeat('A', 120); // 最大长度测试
        $device->setCode($longCode);
        $this->assertSame($longCode, $device->getCode());
    }

    public function testSetModel_withEmptyString_shouldSetEmptyString()
    {
        $device = new Device();
        $device->setModel('');
        $this->assertSame('', $device->getModel());
    }

    public function testSetModel_withLongString_shouldSetLongString()
    {
        $device = new Device();
        $longModel = str_repeat('M', 200); // 最大长度测试
        $device->setModel($longModel);
        $this->assertSame($longModel, $device->getModel());
    }

    public function testSetName_withEmptyString_shouldSetEmptyString()
    {
        $device = new Device();
        $device->setName('');
        $this->assertSame('', $device->getName());
    }

    public function testSetName_withLongString_shouldSetLongString()
    {
        $device = new Device();
        $longName = str_repeat('N', 100); // 最大长度测试
        $device->setName($longName);
        $this->assertSame($longName, $device->getName());
    }

    public function testSetRegIp_withEmptyString_shouldSetEmptyString()
    {
        $device = new Device();
        $device->setRegIp('');
        $this->assertSame('', $device->getRegIp());
    }

    public function testSetRegIp_withIPv6_shouldSetIPv6()
    {
        $device = new Device();
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';
        $device->setRegIp($ipv6);
        $this->assertSame($ipv6, $device->getRegIp());
    }

    public function testSetRegIp_withMaxLength_shouldSetMaxLength()
    {
        $device = new Device();
        $maxLengthIp = str_repeat('1', 45); // 最大长度测试
        $device->setRegIp($maxLengthIp);
        $this->assertSame($maxLengthIp, $device->getRegIp());
    }

    public function testCreateTime_withDifferentDateFormats_shouldWork()
    {
        $device = new Device();

        // 测试不同的日期格式
        $dates = [
            new \DateTime('2024-01-01 00:00:00'),
            new \DateTime('2024-12-31 23:59:59'),
            new \DateTimeImmutable('2024-06-15 12:30:45'),
        ];

        foreach ($dates as $date) {
            $device->setCreateTime($date);
            $this->assertEquals($date, $device->getCreateTime());
        }
    }

    public function testUpdateTime_withDifferentDateFormats_shouldWork()
    {
        $device = new Device();

        // 测试不同的日期格式
        $dates = [
            new \DateTime('2024-01-01 00:00:00'),
            new \DateTime('2024-12-31 23:59:59'),
            new \DateTimeImmutable('2024-06-15 12:30:45'),
        ];

        foreach ($dates as $date) {
            $device->setUpdateTime($date);
            $this->assertEquals($date, $device->getUpdateTime());
        }
    }

    public function testToString_withNullName_shouldReturnCodeOnly()
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $device->setCode('DEVICE001');
        $device->setName(null);

        $this->assertSame('DEVICE001 | ', (string)$device);
    }

    public function testToString_withEmptyName_shouldReturnCodeOnly()
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $device->setCode('DEVICE001');
        $device->setName('');

        $this->assertSame('DEVICE001 | ', (string)$device);
    }

    public function testUserOperations_withLargeNumberOfUsers_shouldWork()
    {
        $device = new Device();
        $users = [];

        // 添加100个用户
        for ($i = 0; $i < 100; $i++) {
            $user = $this->createMock(UserInterface::class);
            $users[] = $user;
            $device->addUser($user);
        }

        $this->assertCount(100, $device->getUsers());
        $this->assertSame(100, $device->getUserCount());

        // 移除所有用户
        foreach ($users as $user) {
            $device->removeUser($user);
        }

        $this->assertCount(0, $device->getUsers());
        $this->assertSame(0, $device->getUserCount());
    }

    public function testRemoveUser_withNonExistentUser_shouldNotThrowException()
    {
        $device = new Device();
        $user1 = $this->createMock(UserInterface::class);
        $user2 = $this->createMock(UserInterface::class);

        $device->addUser($user1);
        $this->assertCount(1, $device->getUsers());

        // 尝试移除不存在的用户
        $device->removeUser($user2);
        $this->assertCount(1, $device->getUsers()); // 应该保持不变
    }

    public function testChainedSetters_shouldAllowCompleteConfiguration()
    {
        $device = new Device();
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        $result = $device
            ->setCode('CHAIN001')
            ->setModel('ChainModel')
            ->setName('ChainName')
            ->setValid(true)
            ->setRegIp('192.168.1.100');

        // 单独设置时间字段，因为它们返回void
        $device->setCreateTime($createTime);
        $device->setUpdateTime($updateTime);

        $this->assertSame($device, $result);
        $this->assertSame('CHAIN001', $device->getCode());
        $this->assertSame('ChainModel', $device->getModel());
        $this->assertSame('ChainName', $device->getName());
        $this->assertTrue($device->isValid());
        $this->assertSame('192.168.1.100', $device->getRegIp());
        $this->assertSame($createTime, $device->getCreateTime());
        $this->assertSame($updateTime, $device->getUpdateTime());
    }
}
