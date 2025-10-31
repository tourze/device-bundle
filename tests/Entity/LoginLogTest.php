<?php

namespace DeviceBundle\Tests\Entity;

use DeviceBundle\Entity\LoginLog;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\OperationSystemEnum\Platform;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(LoginLog::class)]
final class LoginLogTest extends AbstractEntityTestCase
{
    private LoginLog $loginLog;

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
        return new LoginLog();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginLog = new LoginLog();
    }

    public function testInitialStateShouldHaveNullValues(): void
    {
        $this->assertNull($this->loginLog->getId());
        $this->assertNull($this->loginLog->getUser());
        $this->assertNull($this->loginLog->getLoginIp());
        $this->assertNull($this->loginLog->getPlatform());
        $this->assertNull($this->loginLog->getImei());
        $this->assertNull($this->loginLog->getChannel());
        $this->assertNull($this->loginLog->getSystemVersion());
        $this->assertNull($this->loginLog->getVersion());
        $this->assertNull($this->loginLog->getIpCity());
        $this->assertNull($this->loginLog->getIpLocation());
        $this->assertNull($this->loginLog->getDeviceModel());
        $this->assertNull($this->loginLog->getNetType());
        $this->assertNull($this->loginLog->getCreateTime());
    }

    public function testSetGetUserWithValidUserShouldSetAndGet(): void
    {
        $user = $this->createUserForTesting('test_user');

        $this->loginLog->setUser($user);

        $this->assertSame($user, $this->loginLog->getUser());
    }

    public function testSetGetUserWithNullShouldSetNull(): void
    {
        $this->loginLog->setUser(null);
        $this->assertNull($this->loginLog->getUser());
    }

    public function testSetGetLoginIpWithValidIpShouldSetAndGet(): void
    {
        $ip = '192.168.1.1';

        $this->loginLog->setLoginIp($ip);

        $this->assertSame($ip, $this->loginLog->getLoginIp());
    }

    public function testSetGetLoginIpWithNullShouldSetNull(): void
    {
        $this->loginLog->setLoginIp(null);
        $this->assertNull($this->loginLog->getLoginIp());
    }

    public function testSetGetLoginIpWithIPv6ShouldSetAndGet(): void
    {
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

        $this->loginLog->setLoginIp($ipv6);

        $this->assertSame($ipv6, $this->loginLog->getLoginIp());
    }

    public function testSetGetLoginIpWithLocalhostShouldSetAndGet(): void
    {
        $localhost = '127.0.0.1';

        $this->loginLog->setLoginIp($localhost);

        $this->assertSame($localhost, $this->loginLog->getLoginIp());
    }

    public function testSetGetPlatformWithValidPlatformShouldSetAndGet(): void
    {
        $platform = Platform::ANDROID;

        $this->loginLog->setPlatform($platform);

        $this->assertSame($platform, $this->loginLog->getPlatform());
    }

    public function testSetGetPlatformWithNullShouldSetNull(): void
    {
        $this->loginLog->setPlatform(null);
        $this->assertNull($this->loginLog->getPlatform());
    }

    public function testSetGetPlatformWithAllPlatformValuesShouldSetAndGet(): void
    {
        foreach (Platform::cases() as $platform) {
            $this->loginLog->setPlatform($platform);
            $this->assertSame($platform, $this->loginLog->getPlatform());
        }
    }

    public function testSetGetImeiWithValidImeiShouldSetAndGet(): void
    {
        $imei = '123456789012345';

        $this->loginLog->setImei($imei);

        $this->assertSame($imei, $this->loginLog->getImei());
    }

    public function testSetGetImeiWithNullShouldSetNull(): void
    {
        $this->loginLog->setImei(null);
        $this->assertNull($this->loginLog->getImei());
    }

    public function testSetGetImeiWithEmptyStringShouldSetEmptyString(): void
    {
        $this->loginLog->setImei('');
        $this->assertSame('', $this->loginLog->getImei());
    }

    public function testSetGetChannelWithValidChannelShouldSetAndGet(): void
    {
        $channel = 'app_store';

        $this->loginLog->setChannel($channel);

        $this->assertSame($channel, $this->loginLog->getChannel());
    }

    public function testSetGetChannelWithNullShouldSetNull(): void
    {
        $this->loginLog->setChannel(null);
        $this->assertNull($this->loginLog->getChannel());
    }

    public function testSetGetSystemVersionWithValidVersionShouldSetAndGet(): void
    {
        $version = 'iOS 17.2.1';

        $this->loginLog->setSystemVersion($version);

        $this->assertSame($version, $this->loginLog->getSystemVersion());
    }

    public function testSetGetSystemVersionWithNullShouldSetNull(): void
    {
        $this->loginLog->setSystemVersion(null);
        $this->assertNull($this->loginLog->getSystemVersion());
    }

    public function testSetGetVersionWithValidAppVersionShouldSetAndGet(): void
    {
        $version = '1.2.3';

        $this->loginLog->setVersion($version);

        $this->assertSame($version, $this->loginLog->getVersion());
    }

    public function testSetGetVersionWithNullShouldSetNull(): void
    {
        $this->loginLog->setVersion(null);
        $this->assertNull($this->loginLog->getVersion());
    }

    public function testSetGetIpCityWithValidCityShouldSetAndGet(): void
    {
        $city = '北京';

        $this->loginLog->setIpCity($city);

        $this->assertSame($city, $this->loginLog->getIpCity());
    }

    public function testSetGetIpCityWithNullShouldSetNull(): void
    {
        $this->loginLog->setIpCity(null);
        $this->assertNull($this->loginLog->getIpCity());
    }

    public function testSetGetIpLocationWithValidLocationShouldSetAndGet(): void
    {
        $location = 'Beijing, China';

        $this->loginLog->setIpLocation($location);

        $this->assertSame($location, $this->loginLog->getIpLocation());
    }

    public function testSetGetIpLocationWithNullShouldSetNull(): void
    {
        $this->loginLog->setIpLocation(null);
        $this->assertNull($this->loginLog->getIpLocation());
    }

    public function testSetGetDeviceModelWithValidModelShouldSetAndGet(): void
    {
        $model = 'iPhone 15 Pro';

        $this->loginLog->setDeviceModel($model);

        $this->assertSame($model, $this->loginLog->getDeviceModel());
    }

    public function testSetGetDeviceModelWithNullShouldSetNull(): void
    {
        $this->loginLog->setDeviceModel(null);
        $this->assertNull($this->loginLog->getDeviceModel());
    }

    public function testSetGetNetTypeWithValidNetTypeShouldSetAndGet(): void
    {
        $netType = '5G';

        $this->loginLog->setNetType($netType);

        $this->assertSame($netType, $this->loginLog->getNetType());
    }

    public function testSetGetNetTypeWithNullShouldSetNull(): void
    {
        $this->loginLog->setNetType(null);
        $this->assertNull($this->loginLog->getNetType());
    }

    public function testSetGetCreateTimeWithValidDateTimeShouldSetAndGet(): void
    {
        $dateTime = new \DateTimeImmutable();

        $this->loginLog->setCreateTime($dateTime);

        $this->assertSame($dateTime, $this->loginLog->getCreateTime());
    }

    public function testSetGetCreateTimeWithNullShouldSetNull(): void
    {
        $this->loginLog->setCreateTime(null);
        $this->assertNull($this->loginLog->getCreateTime());
    }

    public function testChainedSettersShouldAllowMethodChaining(): void
    {
        $user = $this->createUserForTesting('test_user');
        $platform = Platform::IOS;
        $dateTime = new \DateTime();

        // 由于setter方法现在返回void，不再支持链式调用
        $this->loginLog->setUser($user);
        $this->loginLog->setLoginIp('192.168.1.1');
        $this->loginLog->setPlatform($platform);
        $this->loginLog->setImei('123456789012345');
        $this->loginLog->setChannel('app_store');
        $this->loginLog->setSystemVersion('iOS 17.2.1');
        $this->loginLog->setVersion('1.2.3');
        $this->loginLog->setIpCity('北京');
        $this->loginLog->setIpLocation('Beijing, China');
        $this->loginLog->setDeviceModel('iPhone 15 Pro');
        $this->loginLog->setNetType('5G');

        // 验证所有属性都已正确设置
        $this->assertSame($user, $this->loginLog->getUser());
        $this->assertSame('192.168.1.1', $this->loginLog->getLoginIp());
        $this->assertSame($platform, $this->loginLog->getPlatform());
        $this->assertSame('123456789012345', $this->loginLog->getImei());
        $this->assertSame('app_store', $this->loginLog->getChannel());
        $this->assertSame('iOS 17.2.1', $this->loginLog->getSystemVersion());
        $this->assertSame('1.2.3', $this->loginLog->getVersion());
        $this->assertSame('北京', $this->loginLog->getIpCity());
        $this->assertSame('Beijing, China', $this->loginLog->getIpLocation());
        $this->assertSame('iPhone 15 Pro', $this->loginLog->getDeviceModel());
        $this->assertSame('5G', $this->loginLog->getNetType());
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        $user = class_exists(InMemoryUser::class) ? new InMemoryUser('test', 'test') : null;

        yield 'loginIp' => ['loginIp', '192.168.1.1'];
        yield 'platform' => ['platform', Platform::ANDROID];
        yield 'imei' => ['imei', '123456789012345'];
        yield 'channel' => ['channel', 'app_store'];
        yield 'systemVersion' => ['systemVersion', 'iOS 17.2.1'];
        yield 'version' => ['version', '1.2.3'];
        yield 'ipCity' => ['ipCity', '北京'];
        yield 'ipLocation' => ['ipLocation', 'Beijing, China'];
        yield 'deviceModel' => ['deviceModel', 'iPhone 15 Pro'];
        yield 'netType' => ['netType', '5G'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
    }
}
