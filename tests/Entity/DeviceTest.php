<?php

namespace DeviceBundle\Tests\Entity;

use DeviceBundle\Entity\Device;
use DeviceBundle\Enum\DeviceStatus;
use DeviceBundle\Enum\DeviceType;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Device::class)]
final class DeviceTest extends AbstractEntityTestCase
{
    /**
     * 创建 UserInterface 的测试实现
     *
     * @param non-empty-string $userIdentifier 用户标识符
     * @param array<string> $roles 用户角色数组
     */
    private function createUserForTesting(string $userIdentifier = 'test-user', array $roles = ['ROLE_USER']): UserInterface
    {
        return new class($userIdentifier, $roles) implements UserInterface {
            /**
             * @param non-empty-string $userIdentifier
             * @param array<string> $roles
             */
            public function __construct(
                private readonly string $userIdentifier,
                private readonly array $roles,
            ) {
            }

            /** @return non-empty-string */
            public function getUserIdentifier(): string
            {
                return $this->userIdentifier;
            }

            /** @return array<string> */
            public function getRoles(): array
            {
                return $this->roles;
            }

            public function eraseCredentials(): void
            {
                // 空实现 - stub 不需要真正的凭据管理
            }
        };
    }

    protected function createEntity(): object
    {
        return new Device();
    }

    public function testConstructShouldInitializeUsers(): void
    {
        $device = new Device();
        $this->assertInstanceOf(ArrayCollection::class, $device->getUsers());
        $this->assertCount(0, $device->getUsers());
    }

    public function testToStringWithoutIdShouldReturnEmptyString(): void
    {
        $device = new Device();
        $this->assertSame('', (string) $device);
    }

    public function testToStringWithIdAndAttributesShouldReturnFormattedString(): void
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $device->setCode('DEVICE001');
        $device->setName('Test Device');

        $this->assertSame('DEVICE001 | Test Device', (string) $device);
    }

    public function testSetGetId(): void
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $this->assertSame('123456789', $device->getId());
    }

    public function testSetGetCode(): void
    {
        $device = new Device();
        $device->setCode('DEVICE001');
        $this->assertSame('DEVICE001', $device->getCode());
    }

    public function testSetGetModel(): void
    {
        $device = new Device();
        $device->setModel('TestModel');
        $this->assertSame('TestModel', $device->getModel());
    }

    public function testSetGetName(): void
    {
        $device = new Device();
        $device->setName('Test Device');
        $this->assertSame('Test Device', $device->getName());

        $device->setName(null);
        $this->assertNull($device->getName());
    }

    public function testAddRemoveUser(): void
    {
        $device = new Device();
        $user = $this->createUserForTesting('test_user');

        $device->addUser($user);
        $this->assertCount(1, $device->getUsers());
        $this->assertTrue($device->getUsers()->contains($user));

        $device->removeUser($user);
        $this->assertCount(0, $device->getUsers());
        $this->assertFalse($device->getUsers()->contains($user));
    }

    public function testAddUserWithExistingUserShouldNotAddDuplicate(): void
    {
        $device = new Device();
        $user = $this->createUserForTesting('test_user');

        $device->addUser($user);
        $device->addUser($user); // 添加相同的用户第二次

        $this->assertCount(1, $device->getUsers());
    }

    public function testSetGetRegIp(): void
    {
        $device = new Device();
        $device->setRegIp('127.0.0.1');
        $this->assertSame('127.0.0.1', $device->getRegIp());

        $device->setRegIp(null);
        $this->assertNull($device->getRegIp());
    }

    public function testSetGetValid(): void
    {
        $device = new Device();
        $this->assertFalse($device->isValid());

        $device->setValid(true);
        $this->assertTrue($device->isValid());

        $device->setValid(null);
        $this->assertNull($device->isValid());
    }

    public function testSetGetCreateTime(): void
    {
        $device = new Device();
        $now = new \DateTimeImmutable();

        $device->setCreateTime($now);
        $this->assertSame($now, $device->getCreateTime());

        $device->setCreateTime(null);
        $this->assertNull($device->getCreateTime());
    }

    public function testSetGetUpdateTime(): void
    {
        $device = new Device();
        $now = new \DateTimeImmutable();

        $device->setUpdateTime($now);
        $this->assertSame($now, $device->getUpdateTime());

        $device->setUpdateTime(null);
        $this->assertNull($device->getUpdateTime());
    }

    public function testGetUserCount(): void
    {
        $device = new Device();
        $this->assertSame(0, $device->getUserCount());

        $user1 = $this->createUserForTesting('test_user_1');
        $user2 = $this->createUserForTesting('test_user_2');

        $device->addUser($user1);
        $this->assertSame(1, $device->getUserCount());

        $device->addUser($user2);
        $this->assertSame(2, $device->getUserCount());

        $device->removeUser($user1);
        $this->assertSame(1, $device->getUserCount());
    }

    public function testSetCodeWithEmptyStringShouldSetEmptyString(): void
    {
        $device = new Device();
        $device->setCode('');
        $this->assertSame('', $device->getCode());
    }

    public function testSetCodeWithLongStringShouldSetLongString(): void
    {
        $device = new Device();
        $longCode = str_repeat('A', 120); // 最大长度测试
        $device->setCode($longCode);
        $this->assertSame($longCode, $device->getCode());
    }

    public function testSetModelWithEmptyStringShouldSetEmptyString(): void
    {
        $device = new Device();
        $device->setModel('');
        $this->assertSame('', $device->getModel());
    }

    public function testSetModelWithLongStringShouldSetLongString(): void
    {
        $device = new Device();
        $longModel = str_repeat('M', 200); // 最大长度测试
        $device->setModel($longModel);
        $this->assertSame($longModel, $device->getModel());
    }

    public function testSetNameWithEmptyStringShouldSetEmptyString(): void
    {
        $device = new Device();
        $device->setName('');
        $this->assertSame('', $device->getName());
    }

    public function testSetNameWithLongStringShouldSetLongString(): void
    {
        $device = new Device();
        $longName = str_repeat('N', 100); // 最大长度测试
        $device->setName($longName);
        $this->assertSame($longName, $device->getName());
    }

    public function testSetRegIpWithEmptyStringShouldSetEmptyString(): void
    {
        $device = new Device();
        $device->setRegIp('');
        $this->assertSame('', $device->getRegIp());
    }

    public function testSetRegIpWithIPv6ShouldSetIPv6(): void
    {
        $device = new Device();
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';
        $device->setRegIp($ipv6);
        $this->assertSame($ipv6, $device->getRegIp());
    }

    public function testSetRegIpWithMaxLengthShouldSetMaxLength(): void
    {
        $device = new Device();
        $maxLengthIp = str_repeat('1', 45); // 最大长度测试
        $device->setRegIp($maxLengthIp);
        $this->assertSame($maxLengthIp, $device->getRegIp());
    }

    public function testCreateTimeWithDifferentDateFormatsShouldWork(): void
    {
        $device = new Device();

        // 测试不同的日期格式
        $dates = [
            new \DateTimeImmutable('2024-01-01 00:00:00'),
            new \DateTimeImmutable('2024-12-31 23:59:59'),
            new \DateTimeImmutable('2024-06-15 12:30:45'),
        ];

        foreach ($dates as $date) {
            $device->setCreateTime($date);
            $this->assertEquals($date, $device->getCreateTime());
        }
    }

    public function testUpdateTimeWithDifferentDateFormatsShouldWork(): void
    {
        $device = new Device();

        // 测试不同的日期格式
        $dates = [
            new \DateTimeImmutable('2024-01-01 00:00:00'),
            new \DateTimeImmutable('2024-12-31 23:59:59'),
            new \DateTimeImmutable('2024-06-15 12:30:45'),
        ];

        foreach ($dates as $date) {
            $device->setUpdateTime($date);
            $this->assertEquals($date, $device->getUpdateTime());
        }
    }

    public function testToStringWithNullNameShouldReturnCodeOnly(): void
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $device->setCode('DEVICE001');
        $device->setName(null);

        $this->assertSame('DEVICE001 | ', (string) $device);
    }

    public function testToStringWithEmptyNameShouldReturnCodeOnly(): void
    {
        $device = new Device();
        $reflection = new \ReflectionClass(Device::class);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($device, '123456789');

        $device->setCode('DEVICE001');
        $device->setName('');

        $this->assertSame('DEVICE001 | ', (string) $device);
    }

    public function testUserOperationsWithLargeNumberOfUsersShouldWork(): void
    {
        $device = new Device();
        $users = [];

        // 添加100个用户
        for ($i = 0; $i < 100; ++$i) {
            $user = $this->createUserForTesting('test_user_' . $i);
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

    public function testRemoveUserWithNonExistentUserShouldNotThrowException(): void
    {
        $device = new Device();
        $user1 = $this->createUserForTesting('test_user_1');
        $user2 = $this->createUserForTesting('test_user_2');

        $device->addUser($user1);
        $this->assertCount(1, $device->getUsers());

        // 尝试移除不存在的用户
        $device->removeUser($user2);
        $this->assertCount(1, $device->getUsers()); // 应该保持不变
    }

    public function testChainedSettersShouldAllowCompleteConfiguration(): void
    {
        $device = new Device();
        $createTime = new \DateTimeImmutable();
        $updateTime = new \DateTimeImmutable();

        // 由于setter方法现在返回void，不再支持链式调用
        $device->setCode('CHAIN001');
        $device->setModel('ChainModel');
        $device->setName('ChainName');
        $device->setValid(true);
        $device->setRegIp('192.168.1.100');
        $device->setCreateTime($createTime);
        $device->setUpdateTime($updateTime);

        // 验证所有属性都已正确设置
        $this->assertSame('CHAIN001', $device->getCode());
        $this->assertSame('ChainModel', $device->getModel());
        $this->assertSame('ChainName', $device->getName());
        $this->assertTrue($device->isValid());
        $this->assertSame('192.168.1.100', $device->getRegIp());
        $this->assertSame($createTime, $device->getCreateTime());
        $this->assertSame($updateTime, $device->getUpdateTime());
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        yield 'code' => ['code', 'TEST001'];
        yield 'model' => ['model', 'Test Model'];
        yield 'name' => ['name', 'Test Device'];
        yield 'valid' => ['valid', true];
        yield 'regIp' => ['regIp', '192.168.1.1'];
        yield 'deviceType' => ['deviceType', DeviceType::PHONE];
        yield 'osVersion' => ['osVersion', 'iOS 15.0'];
        yield 'brand' => ['brand', 'Apple'];
        yield 'status' => ['status', DeviceStatus::ONLINE];
        yield 'lastOnlineTime' => ['lastOnlineTime', new \DateTimeImmutable()];
        yield 'lastIp' => ['lastIp', '192.168.1.2'];
        yield 'fingerprint' => ['fingerprint', 'test-fingerprint'];
        yield 'cpuCores' => ['cpuCores', 8];
        yield 'memorySize' => ['memorySize', '8192'];
        yield 'storageSize' => ['storageSize', '256000'];
        yield 'remark' => ['remark', 'Test remark'];
    }
}
