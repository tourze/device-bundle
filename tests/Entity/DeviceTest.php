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
        $now = new \DateTime();

        $device->setCreateTime($now);
        $this->assertSame($now, $device->getCreateTime());

        $device->setCreateTime(null);
        $this->assertNull($device->getCreateTime());
    }

    public function testSetGetUpdateTime()
    {
        $device = new Device();
        $now = new \DateTime();

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
}
