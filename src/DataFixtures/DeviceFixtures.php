<?php

namespace DeviceBundle\DataFixtures;

use DeviceBundle\Entity\Device;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class DeviceFixtures extends Fixture
{
    // 使用常量定义引用名称
    public const DEVICE_REFERENCE_1 = 'device-1';
    public const DEVICE_REFERENCE_2 = 'device-2';
    public const DEVICE_REFERENCE_3 = 'device-3';

    public function load(ObjectManager $manager): void
    {
        // 创建第一个测试设备
        $device1 = new Device();
        $device1->setCode('DEV001');
        $device1->setModel('iPhone 14 Pro');
        $device1->setName('测试设备1');
        $device1->setValid(true);
        $device1->setRegIp('192.168.1.1');

        $manager->persist($device1);

        // 创建第二个测试设备
        $device2 = new Device();
        $device2->setCode('DEV002');
        $device2->setModel('Samsung Galaxy S23');
        $device2->setName('测试设备2');
        $device2->setValid(true);
        $device2->setRegIp('192.168.1.2');

        $manager->persist($device2);

        // 创建第三个测试设备（无效状态）
        $device3 = new Device();
        $device3->setCode('DEV003');
        $device3->setModel('Xiaomi 13 Pro');
        $device3->setName('测试设备3');
        $device3->setValid(false);
        $device3->setRegIp('192.168.1.3');

        $manager->persist($device3);

        $manager->flush();

        // 添加引用以便其他 Fixture 使用
        $this->addReference(self::DEVICE_REFERENCE_1, $device1);
        $this->addReference(self::DEVICE_REFERENCE_2, $device2);
        $this->addReference(self::DEVICE_REFERENCE_3, $device3);
    }
}
