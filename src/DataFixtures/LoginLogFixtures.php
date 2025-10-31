<?php

declare(strict_types=1);

namespace DeviceBundle\DataFixtures;

use Carbon\CarbonImmutable;
use DeviceBundle\Entity\LoginLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\OperationSystemEnum\Platform;

#[When(env: 'test')]
#[When(env: 'dev')]
class LoginLogFixtures extends Fixture
{
    public const LOGIN_LOG_REFERENCE_1 = 'login-log-1';
    public const LOGIN_LOG_REFERENCE_2 = 'login-log-2';
    public const LOGIN_LOG_REFERENCE_3 = 'login-log-3';

    public function load(ObjectManager $manager): void
    {
        // 创建简单的测试登录记录，不关联具体用户
        $loginLog1 = new LoginLog();
        $loginLog1->setLoginIp('192.168.1.100');
        $loginLog1->setPlatform(Platform::IOS);
        $loginLog1->setImei('86012345678901234');
        $loginLog1->setChannel('app_store');
        $loginLog1->setSystemVersion('iOS 17.1.1');
        $loginLog1->setVersion('1.2.0');
        $loginLog1->setIpCity('北京');
        $loginLog1->setIpLocation('北京市朝阳区');
        $loginLog1->setDeviceModel('iPhone 15 Pro');
        $loginLog1->setNetType('WiFi');
        $loginLog1->setCreateTime(CarbonImmutable::now()->modify('-2 hours'));
        $manager->persist($loginLog1);

        $loginLog2 = new LoginLog();
        $loginLog2->setLoginIp('192.168.1.101');
        $loginLog2->setPlatform(Platform::ANDROID);
        $loginLog2->setImei('86012345678901235');
        $loginLog2->setChannel('google_play');
        $loginLog2->setSystemVersion('Android 14');
        $loginLog2->setVersion('1.2.0');
        $loginLog2->setIpCity('上海');
        $loginLog2->setIpLocation('上海市浦东新区');
        $loginLog2->setDeviceModel('Samsung Galaxy S24');
        $loginLog2->setNetType('5G');
        $loginLog2->setCreateTime(CarbonImmutable::now()->modify('-1 hour'));
        $manager->persist($loginLog2);

        $loginLog3 = new LoginLog();
        $loginLog3->setLoginIp('10.0.0.50');
        $loginLog3->setPlatform(Platform::WINDOWS);
        $loginLog3->setChannel('web');
        $loginLog3->setSystemVersion('Windows 11');
        $loginLog3->setVersion('1.1.5');
        $loginLog3->setIpCity('深圳');
        $loginLog3->setIpLocation('广东省深圳市南山区');
        $loginLog3->setDeviceModel('Chrome Browser');
        $loginLog3->setNetType('宽带');
        $loginLog3->setCreateTime(CarbonImmutable::now()->modify('-1 week'));
        $manager->persist($loginLog3);

        // 创建一些随机的登录记录
        $faker = Factory::create('zh_CN');
        for ($i = 1; $i <= 5; ++$i) {
            $loginLog = new LoginLog();
            $loginLog->setLoginIp($faker->ipv4());
            /** @var Platform $platform */
            $platform = $faker->randomElement(Platform::cases());
            $loginLog->setPlatform($platform);
            $loginLog->setImei($faker->numerify('860############'));
            /** @var string $channel */
            $channel = $faker->randomElement(['app_store', 'google_play', 'xiaomi_store', 'huawei_store']);
            $loginLog->setChannel($channel);
            /** @var string $systemVersion */
            $systemVersion = $faker->randomElement([
                'iOS 17.1.1', 'iOS 16.7.2',
                'Android 14', 'Android 13', 'Android 12',
            ]);
            $loginLog->setSystemVersion($systemVersion);
            /** @var string $version */
            $version = $faker->randomElement(['1.2.0', '1.1.9', '1.1.8']);
            $loginLog->setVersion($version);
            $loginLog->setIpCity($faker->city());
            $loginLog->setIpLocation($faker->address());
            /** @var string $deviceModel */
            $deviceModel = $faker->randomElement([
                'iPhone 15 Pro', 'iPhone 14', 'iPhone 13',
                'Samsung Galaxy S24', 'Samsung Galaxy S23',
                'Xiaomi 14 Pro', 'Huawei Mate 60 Pro',
            ]);
            $loginLog->setDeviceModel($deviceModel);
            /** @var string $netType */
            $netType = $faker->randomElement(['WiFi', '5G', '4G', '宽带']);
            $loginLog->setNetType($netType);
            $loginLog->setCreateTime(CarbonImmutable::now()->modify('-' . rand(1, 72) . ' hours'));
            $manager->persist($loginLog);
        }

        $manager->flush();

        // 添加引用以便其他 Fixture 使用
        $this->addReference(self::LOGIN_LOG_REFERENCE_1, $loginLog1);
        $this->addReference(self::LOGIN_LOG_REFERENCE_2, $loginLog2);
        $this->addReference(self::LOGIN_LOG_REFERENCE_3, $loginLog3);
    }
}
