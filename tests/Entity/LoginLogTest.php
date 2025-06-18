<?php

namespace DeviceBundle\Tests\Entity;

use DeviceBundle\Entity\LoginLog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\OperationSystemEnum\Platform;

class LoginLogTest extends TestCase
{
    private LoginLog $loginLog;

    protected function setUp(): void
    {
        $this->loginLog = new LoginLog();
    }

    public function testInitialState_shouldHaveNullValues()
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
        $this->assertNull($this->loginLog->getPhoneModel());
        $this->assertNull($this->loginLog->getNetType());
        $this->assertNull($this->loginLog->getCreateTime());
    }

    public function testSetGetUser_withValidUser_shouldSetAndGet()
    {
        $user = $this->createMock(UserInterface::class);

        $result = $this->loginLog->setUser($user);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($user, $this->loginLog->getUser());
    }

    public function testSetGetUser_withNull_shouldSetNull()
    {
        $this->loginLog->setUser(null);
        $this->assertNull($this->loginLog->getUser());
    }

    public function testSetGetLoginIp_withValidIp_shouldSetAndGet()
    {
        $ip = '192.168.1.1';

        $result = $this->loginLog->setLoginIp($ip);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($ip, $this->loginLog->getLoginIp());
    }

    public function testSetGetLoginIp_withNull_shouldSetNull()
    {
        $this->loginLog->setLoginIp(null);
        $this->assertNull($this->loginLog->getLoginIp());
    }

    public function testSetGetLoginIp_withIPv6_shouldSetAndGet()
    {
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

        $this->loginLog->setLoginIp($ipv6);

        $this->assertSame($ipv6, $this->loginLog->getLoginIp());
    }

    public function testSetGetLoginIp_withLocalhost_shouldSetAndGet()
    {
        $localhost = '127.0.0.1';

        $this->loginLog->setLoginIp($localhost);

        $this->assertSame($localhost, $this->loginLog->getLoginIp());
    }

    public function testSetGetPlatform_withValidPlatform_shouldSetAndGet()
    {
        $platform = Platform::ANDROID;

        $result = $this->loginLog->setPlatform($platform);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($platform, $this->loginLog->getPlatform());
    }

    public function testSetGetPlatform_withNull_shouldSetNull()
    {
        $this->loginLog->setPlatform(null);
        $this->assertNull($this->loginLog->getPlatform());
    }

    public function testSetGetPlatform_withAllPlatformValues_shouldSetAndGet()
    {
        foreach (Platform::cases() as $platform) {
            $this->loginLog->setPlatform($platform);
            $this->assertSame($platform, $this->loginLog->getPlatform());
        }
    }

    public function testSetGetImei_withValidImei_shouldSetAndGet()
    {
        $imei = '123456789012345';

        $result = $this->loginLog->setImei($imei);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($imei, $this->loginLog->getImei());
    }

    public function testSetGetImei_withNull_shouldSetNull()
    {
        $this->loginLog->setImei(null);
        $this->assertNull($this->loginLog->getImei());
    }

    public function testSetGetImei_withEmptyString_shouldSetEmptyString()
    {
        $this->loginLog->setImei('');
        $this->assertSame('', $this->loginLog->getImei());
    }

    public function testSetGetChannel_withValidChannel_shouldSetAndGet()
    {
        $channel = 'app_store';

        $result = $this->loginLog->setChannel($channel);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($channel, $this->loginLog->getChannel());
    }

    public function testSetGetChannel_withNull_shouldSetNull()
    {
        $this->loginLog->setChannel(null);
        $this->assertNull($this->loginLog->getChannel());
    }

    public function testSetGetSystemVersion_withValidVersion_shouldSetAndGet()
    {
        $version = 'iOS 17.2.1';

        $result = $this->loginLog->setSystemVersion($version);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($version, $this->loginLog->getSystemVersion());
    }

    public function testSetGetSystemVersion_withNull_shouldSetNull()
    {
        $this->loginLog->setSystemVersion(null);
        $this->assertNull($this->loginLog->getSystemVersion());
    }

    public function testSetGetVersion_withValidAppVersion_shouldSetAndGet()
    {
        $version = '1.2.3';

        $result = $this->loginLog->setVersion($version);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($version, $this->loginLog->getVersion());
    }

    public function testSetGetVersion_withNull_shouldSetNull()
    {
        $this->loginLog->setVersion(null);
        $this->assertNull($this->loginLog->getVersion());
    }

    public function testSetGetIpCity_withValidCity_shouldSetAndGet()
    {
        $city = '北京';

        $result = $this->loginLog->setIpCity($city);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($city, $this->loginLog->getIpCity());
    }

    public function testSetGetIpCity_withNull_shouldSetNull()
    {
        $this->loginLog->setIpCity(null);
        $this->assertNull($this->loginLog->getIpCity());
    }

    public function testSetGetIpLocation_withValidLocation_shouldSetAndGet()
    {
        $location = 'Beijing, China';

        $result = $this->loginLog->setIpLocation($location);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($location, $this->loginLog->getIpLocation());
    }

    public function testSetGetIpLocation_withNull_shouldSetNull()
    {
        $this->loginLog->setIpLocation(null);
        $this->assertNull($this->loginLog->getIpLocation());
    }

    public function testSetGetPhoneModel_withValidModel_shouldSetAndGet()
    {
        $model = 'iPhone 15 Pro';

        $result = $this->loginLog->setPhoneModel($model);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($model, $this->loginLog->getPhoneModel());
    }

    public function testSetGetPhoneModel_withNull_shouldSetNull()
    {
        $this->loginLog->setPhoneModel(null);
        $this->assertNull($this->loginLog->getPhoneModel());
    }

    public function testSetGetNetType_withValidNetType_shouldSetAndGet()
    {
        $netType = '5G';

        $result = $this->loginLog->setNetType($netType);

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($netType, $this->loginLog->getNetType());
    }

    public function testSetGetNetType_withNull_shouldSetNull()
    {
        $this->loginLog->setNetType(null);
        $this->assertNull($this->loginLog->getNetType());
    }

    public function testSetGetCreateTime_withValidDateTime_shouldSetAndGet()
    {
        $dateTime = new \DateTimeImmutable();

        $this->loginLog->setCreateTime($dateTime);

        $this->assertSame($dateTime, $this->loginLog->getCreateTime());
    }

    public function testSetGetCreateTime_withNull_shouldSetNull()
    {
        $this->loginLog->setCreateTime(null);
        $this->assertNull($this->loginLog->getCreateTime());
    }

    public function testChainedSetters_shouldAllowMethodChaining()
    {
        $user = $this->createMock(UserInterface::class);
        $platform = Platform::IOS;
        $dateTime = new \DateTime();

        $result = $this->loginLog
            ->setUser($user)
            ->setLoginIp('192.168.1.1')
            ->setPlatform($platform)
            ->setImei('123456789012345')
            ->setChannel('app_store')
            ->setSystemVersion('iOS 17.2.1')
            ->setVersion('1.2.3')
            ->setIpCity('北京')
            ->setIpLocation('Beijing, China')
            ->setPhoneModel('iPhone 15 Pro')
            ->setNetType('5G');

        $this->assertSame($this->loginLog, $result);
        $this->assertSame($user, $this->loginLog->getUser());
        $this->assertSame('192.168.1.1', $this->loginLog->getLoginIp());
        $this->assertSame($platform, $this->loginLog->getPlatform());
        $this->assertSame('123456789012345', $this->loginLog->getImei());
        $this->assertSame('app_store', $this->loginLog->getChannel());
        $this->assertSame('iOS 17.2.1', $this->loginLog->getSystemVersion());
        $this->assertSame('1.2.3', $this->loginLog->getVersion());
        $this->assertSame('北京', $this->loginLog->getIpCity());
        $this->assertSame('Beijing, China', $this->loginLog->getIpLocation());
        $this->assertSame('iPhone 15 Pro', $this->loginLog->getPhoneModel());
        $this->assertSame('5G', $this->loginLog->getNetType());
    }
} 