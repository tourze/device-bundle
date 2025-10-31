<?php

namespace DeviceBundle\Tests\Repository;

use DeviceBundle\Entity\LoginLog;
use DeviceBundle\Repository\LoginLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\OperationSystemEnum\Platform;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(LoginLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class LoginLogRepositoryTest extends AbstractRepositoryTestCase
{
    private LoginLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(LoginLogRepository::class);
    }

    public function testFindByUserShouldReturnUserLoginLogs(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1');
        $user2 = $this->createNormalUser('user2');

        $loginLog1 = $this->createTestLoginLogForUser($user1);
        $loginLog2 = $this->createTestLoginLogForUser($user2);

        $user1Logs = $this->repository->findBy(['user' => $user1]);
        $user2Logs = $this->repository->findBy(['user' => $user2]);

        $this->assertCount(1, $user1Logs);
        $this->assertCount(1, $user2Logs);
        $this->assertEquals($user1, $user1Logs[0]->getUser());
        $this->assertEquals($user2, $user2Logs[0]->getUser());
    }

    public function testFindByPlatformShouldReturnPlatformSpecificLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $iosLog = $this->createTestLoginLogWithPlatform($user, Platform::IOS);
        $androidLog = $this->createTestLoginLogWithPlatform($user, Platform::ANDROID);

        $iosLogs = $this->repository->findBy(['platform' => Platform::IOS]);
        $androidLogs = $this->repository->findBy(['platform' => Platform::ANDROID]);

        $this->assertCount(1, $iosLogs);
        $this->assertCount(1, $androidLogs);
        $this->assertEquals(Platform::IOS, $iosLogs[0]->getPlatform());
        $this->assertEquals(Platform::ANDROID, $androidLogs[0]->getPlatform());
    }

    public function testFindByLoginIpShouldReturnIpSpecificLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $testIp = '192.168.1.100';
        $loginLog = $this->createTestLoginLogWithIp($user, $testIp);

        $ipLogs = $this->repository->findBy(['loginIp' => $testIp]);

        $this->assertCount(1, $ipLogs);
        $this->assertEquals($testIp, $ipLogs[0]->getLoginIp());
    }

    public function testFindByNullValuesShouldReturnLogsWithNullFields(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $loginLog = $this->createTestLoginLogWithNullFields($user);

        $nullChannelLogs = $this->repository->findBy(['channel' => null]);
        $nullImeiLogs = $this->repository->findBy(['imei' => null]);

        $this->assertGreaterThanOrEqual(1, count($nullChannelLogs));
        $this->assertGreaterThanOrEqual(1, count($nullImeiLogs));
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
        $this->createTestLoginLog();
        $this->createTestLoginLog();

        $count = $this->repository->count();

        $this->assertEquals(2, $count);
    }

    public function testCountWithNullCriteriaShouldReturnNullFieldCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $this->createTestLoginLogWithNullFields($user);
        $this->createTestLoginLog();

        $nullChannelCount = $this->repository->count(['channel' => null]);

        $this->assertGreaterThanOrEqual(1, $nullChannelCount);
    }

    public function testSaveShouldPersistLoginLog(): void
    {
        $user = $this->createNormalUser();

        $loginLog = new LoginLog();
        $loginLog->setUser($user);
        $loginLog->setLoginIp('192.168.1.99');
        $loginLog->setPlatform(Platform::IOS);
        $loginLog->setDeviceModel('iPhone 15');

        $this->repository->save($loginLog);

        $this->assertNotNull($loginLog->getId());

        $found = $this->repository->find($loginLog->getId());
        $this->assertInstanceOf(LoginLog::class, $found);
        $this->assertEquals($loginLog->getLoginIp(), $found->getLoginIp());
    }

    public function testRemoveShouldDeleteLoginLog(): void
    {
        $loginLog = $this->createTestLoginLog();
        $loginLogId = $loginLog->getId();

        $this->repository->remove($loginLog);

        $found = $this->repository->find($loginLogId);
        $this->assertNull($found);
    }

    public function testFindLastByUserShouldReturnLatestLog(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $oldLog = $this->createTestLoginLogForUser($user);
        sleep(1);
        $newLog = $this->createTestLoginLogForUser($user);

        $lastLog = $this->repository->findLastByUser($user);

        $this->assertInstanceOf(LoginLog::class, $lastLog);
        $this->assertEquals($newLog->getId(), $lastLog->getId());
    }

    public function testFindLastByUserWithNoLogsShouldReturnNull(): void
    {
        $user = $this->createNormalUser();

        $lastLog = $this->repository->findLastByUser($user);

        $this->assertNull($lastLog);
    }

    public function testFindOneByWithOrderingShouldReturnFirstMatchingEntity(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();
        $log1 = $this->createTestLoginLogWithIp($user, '192.168.1.1');
        $log2 = $this->createTestLoginLogWithIp($user, '192.168.1.2');

        $result = $this->repository->findOneBy(['user' => $user], ['loginIp' => 'ASC']);

        $this->assertInstanceOf(LoginLog::class, $result);
        $this->assertEquals('192.168.1.1', $result->getLoginIp());
    }

    public function testFindOneByWithDescendingOrderShouldReturnLastEntity(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $log1 = $this->createTestLoginLogWithIp($user, '192.168.1.10');
        $log2 = $this->createTestLoginLogWithIp($user, '192.168.1.20');
        $log3 = $this->createTestLoginLogWithIp($user, '192.168.1.30');

        $result = $this->repository->findOneBy(['user' => $user], ['loginIp' => 'DESC']);

        $this->assertInstanceOf(LoginLog::class, $result);
        $this->assertEquals('192.168.1.30', $result->getLoginIp());
    }

    public function testFindOneByWithMultipleOrderingFieldsShouldRespectPriority(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        // 创建不同平台和IP的记录
        $log1 = $this->createTestLoginLogWithIp($user, '192.168.1.100');
        $log1->setPlatform(Platform::ANDROID);
        $log1->setImei('ANDROID_001');

        $log2 = $this->createTestLoginLogWithIp($user, '192.168.1.200');
        $log2->setPlatform(Platform::ANDROID);
        $log2->setImei('ANDROID_002');

        $log3 = $this->createTestLoginLogWithIp($user, '192.168.1.50');
        $log3->setPlatform(Platform::IOS);
        $log3->setImei('IOS_001');

        self::getEntityManager()->flush();

        // 先按平台排序（升序），再按IP排序（降序）
        $result = $this->repository->findOneBy(
            ['user' => $user],
            ['platform' => 'ASC', 'loginIp' => 'DESC']
        );

        $this->assertInstanceOf(LoginLog::class, $result);
        $this->assertEquals(Platform::ANDROID, $result->getPlatform());
        $this->assertEquals('192.168.1.200', $result->getLoginIp());
    }

    public function testFindOneByWithCreateTimeOrderingShouldReturnCorrectEntity(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $log1 = $this->createTestLoginLogForUser($user);
        $log1->setCreateTime(new \DateTimeImmutable('2024-01-01 10:00:00'));

        $log2 = $this->createTestLoginLogForUser($user);
        $log2->setCreateTime(new \DateTimeImmutable('2024-01-01 12:00:00'));

        $log3 = $this->createTestLoginLogForUser($user);
        $log3->setCreateTime(new \DateTimeImmutable('2024-01-01 08:00:00'));

        self::getEntityManager()->flush();

        // 获取最早的记录
        $earliest = $this->repository->findOneBy(['user' => $user], ['createTime' => 'ASC']);

        $this->assertInstanceOf(LoginLog::class, $earliest);
        $this->assertEquals('2024-01-01 08:00:00', $earliest->getCreateTime()?->format('Y-m-d H:i:s'));

        // 获取最晚的记录
        $latest = $this->repository->findOneBy(['user' => $user], ['createTime' => 'DESC']);

        $this->assertInstanceOf(LoginLog::class, $latest);
        $this->assertEquals('2024-01-01 12:00:00', $latest->getCreateTime()?->format('Y-m-d H:i:s'));
    }

    public function testFindByUserAssociationShouldReturnUserLogs(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1@test.com');
        $user2 = $this->createNormalUser('user2@test.com');

        $log1 = $this->createTestLoginLogForUser($user1);
        $log2 = $this->createTestLoginLogForUser($user2);

        $user1Logs = $this->repository->findBy(['user' => $user1]);
        $user2Logs = $this->repository->findBy(['user' => $user2]);

        $this->assertCount(1, $user1Logs);
        $this->assertCount(1, $user2Logs);
        $this->assertEquals($user1, $user1Logs[0]->getUser());
        $this->assertEquals($user2, $user2Logs[0]->getUser());
    }

    public function testCountUserAssociationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1@test.com');
        $user2 = $this->createNormalUser('user2@test.com');

        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user2);

        $user1Count = $this->repository->count(['user' => $user1]);
        $user2Count = $this->repository->count(['user' => $user2]);

        $this->assertEquals(2, $user1Count);
        $this->assertEquals(1, $user2Count);
    }

    public function testFindByUserNullAssociationShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithUser = $this->createTestLoginLogForUser($user);

        $logWithoutUser = new LoginLog();
        $logWithoutUser->setUser(null);
        $logWithoutUser->setLoginIp('192.168.1.100');
        $logWithoutUser->setPlatform(Platform::IOS);
        $logWithoutUser->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutUser);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['user' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getUser());
    }

    public function testCountUserNullAssociationShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithUser = $this->createTestLoginLogForUser($user);

        $logWithoutUser = new LoginLog();
        $logWithoutUser->setUser(null);
        $logWithoutUser->setLoginIp('192.168.1.100');
        $logWithoutUser->setPlatform(Platform::IOS);
        $logWithoutUser->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutUser);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['user' => null]);

        $this->assertEquals(1, $count);
    }

    public function testCountByAssociationUserShouldReturnCorrectNumber(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1@test.com');
        $user2 = $this->createNormalUser('user2@test.com');

        // 为 user1 创建 4 个登录记录
        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user1);

        // 为 user2 创建 2 个登录记录
        $this->createTestLoginLogForUser($user2);
        $this->createTestLoginLogForUser($user2);

        $user1Count = $this->repository->count(['user' => $user1]);
        $user2Count = $this->repository->count(['user' => $user2]);

        $this->assertEquals(4, $user1Count);
        $this->assertEquals(2, $user2Count);
    }

    public function testFindOneByAssociationUserShouldReturnMatchingEntity(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1@test.com');
        $user2 = $this->createNormalUser('user2@test.com');

        $log1 = $this->createTestLoginLogForUser($user1);
        $log2 = $this->createTestLoginLogForUser($user2);

        $result1 = $this->repository->findOneBy(['user' => $user1]);
        $result2 = $this->repository->findOneBy(['user' => $user2]);

        $this->assertInstanceOf(LoginLog::class, $result1);
        $this->assertInstanceOf(LoginLog::class, $result2);
        $this->assertEquals($user1, $result1->getUser());
        $this->assertEquals($user2, $result2->getUser());
        $this->assertEquals($log1->getId(), $result1->getId());
        $this->assertEquals($log2->getId(), $result2->getId());
    }

    public function testFindByLoginIpNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithIp = $this->createTestLoginLogWithIp($user, '192.168.1.100');

        $logWithoutIp = new LoginLog();
        $logWithoutIp->setUser($user);
        $logWithoutIp->setLoginIp(null);
        $logWithoutIp->setPlatform(Platform::IOS);
        $logWithoutIp->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutIp);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['loginIp' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getLoginIp());
    }

    public function testCountLoginIpNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithIp = $this->createTestLoginLogWithIp($user, '192.168.1.100');

        $logWithoutIp = new LoginLog();
        $logWithoutIp->setUser($user);
        $logWithoutIp->setLoginIp(null);
        $logWithoutIp->setPlatform(Platform::IOS);
        $logWithoutIp->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutIp);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['loginIp' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByPlatformNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithPlatform = $this->createTestLoginLogWithPlatform($user, Platform::IOS);

        $logWithoutPlatform = new LoginLog();
        $logWithoutPlatform->setUser($user);
        $logWithoutPlatform->setLoginIp('192.168.1.200');
        $logWithoutPlatform->setPlatform(null);
        $logWithoutPlatform->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutPlatform);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['platform' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getPlatform());
    }

    public function testCountPlatformNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithPlatform = $this->createTestLoginLogWithPlatform($user, Platform::IOS);

        $logWithoutPlatform = new LoginLog();
        $logWithoutPlatform->setUser($user);
        $logWithoutPlatform->setLoginIp('192.168.1.200');
        $logWithoutPlatform->setPlatform(null);
        $logWithoutPlatform->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutPlatform);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['platform' => null]);

        $this->assertEquals(1, $count);
    }

    private function createTestLoginLog(): LoginLog
    {
        $user = $this->createNormalUser();

        return $this->createTestLoginLogForUser($user);
    }

    private function createTestLoginLogForUser(UserInterface $user): LoginLog
    {
        $loginLog = new LoginLog();
        $loginLog->setUser($user);
        $loginLog->setLoginIp('192.168.1.' . rand(1, 254));
        $loginLog->setPlatform(Platform::IOS);
        $loginLog->setImei('IMEI_' . uniqid());
        $loginLog->setChannel('app_store');
        $loginLog->setSystemVersion('17.0');
        $loginLog->setVersion('1.0.0');
        $loginLog->setIpCity('北京');
        $loginLog->setIpLocation('北京市朝阳区');
        $loginLog->setDeviceModel('iPhone 15 Pro');
        $loginLog->setNetType('WiFi');

        self::getEntityManager()->persist($loginLog);
        self::getEntityManager()->flush();

        return $loginLog;
    }

    private function createTestLoginLogWithPlatform(UserInterface $user, Platform $platform): LoginLog
    {
        $loginLog = new LoginLog();
        $loginLog->setUser($user);
        $loginLog->setLoginIp('192.168.1.50');
        $loginLog->setPlatform($platform);
        $loginLog->setDeviceModel(Platform::IOS === $platform ? 'iPhone 15' : 'Samsung Galaxy S24');

        self::getEntityManager()->persist($loginLog);
        self::getEntityManager()->flush();

        return $loginLog;
    }

    private function createTestLoginLogWithIp(UserInterface $user, string $ip): LoginLog
    {
        $loginLog = new LoginLog();
        $loginLog->setUser($user);
        $loginLog->setLoginIp($ip);
        $loginLog->setPlatform(Platform::IOS);
        $loginLog->setDeviceModel('iPhone 15 Pro');

        self::getEntityManager()->persist($loginLog);
        self::getEntityManager()->flush();

        return $loginLog;
    }

    private function createTestLoginLogWithNullFields(UserInterface $user): LoginLog
    {
        $loginLog = new LoginLog();
        $loginLog->setUser($user);
        $loginLog->setLoginIp('192.168.1.200');
        $loginLog->setPlatform(Platform::IOS);
        $loginLog->setDeviceModel('iPhone 15 Pro');

        self::getEntityManager()->persist($loginLog);
        self::getEntityManager()->flush();

        return $loginLog;
    }

    public function testFindOneByWithComplexSortingShouldReturnCorrectLog(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $log1 = $this->createTestLoginLogWithIp($user, '192.168.1.100');
        $log1->setPlatform(Platform::ANDROID);
        self::getEntityManager()->flush();

        $log2 = $this->createTestLoginLogWithIp($user, '192.168.1.101');
        $log2->setPlatform(Platform::IOS);
        self::getEntityManager()->flush();

        $log3 = $this->createTestLoginLogWithIp($user, '192.168.1.102');
        $log3->setPlatform(Platform::ANDROID);
        self::getEntityManager()->flush();

        // 测试多字段排序
        $result = $this->repository->findOneBy(['user' => $user], ['platform' => 'ASC', 'loginIp' => 'DESC']);

        $this->assertInstanceOf(LoginLog::class, $result);
        $this->assertEquals(Platform::ANDROID, $result->getPlatform());
        $this->assertEquals('192.168.1.102', $result->getLoginIp());
    }

    public function testFindByUserAssociationWithMultipleUsersShouldReturnCorrectLogs(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1@test.com');
        $user2 = $this->createNormalUser('user2@test.com');

        // 创建多个用户的登录记录
        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user1);
        $this->createTestLoginLogForUser($user2);
        $this->createTestLoginLogForUser($user2);
        $this->createTestLoginLogForUser($user2);

        $user1Logs = $this->repository->findBy(['user' => $user1]);
        $user2Logs = $this->repository->findBy(['user' => $user2]);

        $this->assertCount(2, $user1Logs);
        $this->assertCount(3, $user2Logs);

        // 验证返回的记录都属于正确的用户
        foreach ($user1Logs as $log) {
            $this->assertEquals($user1, $log->getUser());
        }
        foreach ($user2Logs as $log) {
            $this->assertEquals($user2, $log->getUser());
        }
    }

    public function testFindByUserAssociationWithComplexCriteriaShouldReturnFilteredLogs(): void
    {
        $this->clearDatabase();
        $user1 = $this->createNormalUser('user1@test.com');
        $user2 = $this->createNormalUser('user2@test.com');

        // 为不同用户创建不同平台的登录记录
        $log1 = $this->createTestLoginLogWithPlatform($user1, Platform::IOS);
        $log2 = $this->createTestLoginLogWithPlatform($user1, Platform::ANDROID);
        $log3 = $this->createTestLoginLogWithPlatform($user2, Platform::IOS);

        // 查询特定用户和平台的记录
        $iosLogsForUser1 = $this->repository->findBy([
            'user' => $user1,
            'platform' => Platform::IOS,
        ]);

        $this->assertCount(1, $iosLogsForUser1);
        $this->assertEquals(Platform::IOS, $iosLogsForUser1[0]->getPlatform());
        $this->assertEquals($user1, $iosLogsForUser1[0]->getUser());
    }

    public function testFindByUserAssociationWithOrderingShouldReturnOrderedLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        // 创建多个登录记录，时间不同
        $log1 = $this->createTestLoginLogForUser($user);
        $log1->setCreateTime(new \DateTimeImmutable('2024-01-01 10:00:00'));

        $log2 = $this->createTestLoginLogForUser($user);
        $log2->setCreateTime(new \DateTimeImmutable('2024-01-01 12:00:00'));

        $log3 = $this->createTestLoginLogForUser($user);
        $log3->setCreateTime(new \DateTimeImmutable('2024-01-01 11:00:00'));

        self::getEntityManager()->flush();

        // 按时间升序查询
        $ascendingLogs = $this->repository->findBy(
            ['user' => $user],
            ['createTime' => 'ASC']
        );

        $this->assertCount(3, $ascendingLogs);
        $this->assertEquals('2024-01-01 10:00:00', $ascendingLogs[0]->getCreateTime()?->format('Y-m-d H:i:s'));
        $this->assertEquals('2024-01-01 12:00:00', $ascendingLogs[2]->getCreateTime()?->format('Y-m-d H:i:s'));
    }

    public function testFindByChannelNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithChannel = $this->createTestLoginLogForUser($user);

        $logWithoutChannel = new LoginLog();
        $logWithoutChannel->setUser($user);
        $logWithoutChannel->setLoginIp('192.168.1.100');
        $logWithoutChannel->setPlatform(Platform::IOS);
        $logWithoutChannel->setChannel(null);
        $logWithoutChannel->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutChannel);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['channel' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getChannel());
    }

    public function testCountChannelNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithChannel = $this->createTestLoginLogForUser($user);

        $logWithoutChannel = new LoginLog();
        $logWithoutChannel->setUser($user);
        $logWithoutChannel->setLoginIp('192.168.1.100');
        $logWithoutChannel->setPlatform(Platform::IOS);
        $logWithoutChannel->setChannel(null);
        $logWithoutChannel->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutChannel);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['channel' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindBySystemVersionNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithSystemVersion = $this->createTestLoginLogForUser($user);

        $logWithoutSystemVersion = new LoginLog();
        $logWithoutSystemVersion->setUser($user);
        $logWithoutSystemVersion->setLoginIp('192.168.1.100');
        $logWithoutSystemVersion->setPlatform(Platform::IOS);
        $logWithoutSystemVersion->setSystemVersion(null);
        $logWithoutSystemVersion->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutSystemVersion);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['systemVersion' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getSystemVersion());
    }

    public function testCountSystemVersionNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithSystemVersion = $this->createTestLoginLogForUser($user);

        $logWithoutSystemVersion = new LoginLog();
        $logWithoutSystemVersion->setUser($user);
        $logWithoutSystemVersion->setLoginIp('192.168.1.100');
        $logWithoutSystemVersion->setPlatform(Platform::IOS);
        $logWithoutSystemVersion->setSystemVersion(null);
        $logWithoutSystemVersion->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutSystemVersion);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['systemVersion' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByVersionNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithVersion = $this->createTestLoginLogForUser($user);

        $logWithoutVersion = new LoginLog();
        $logWithoutVersion->setUser($user);
        $logWithoutVersion->setLoginIp('192.168.1.100');
        $logWithoutVersion->setPlatform(Platform::IOS);
        $logWithoutVersion->setVersion(null);
        $logWithoutVersion->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutVersion);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['version' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getVersion());
    }

    public function testCountVersionNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithVersion = $this->createTestLoginLogForUser($user);

        $logWithoutVersion = new LoginLog();
        $logWithoutVersion->setUser($user);
        $logWithoutVersion->setLoginIp('192.168.1.100');
        $logWithoutVersion->setPlatform(Platform::IOS);
        $logWithoutVersion->setVersion(null);
        $logWithoutVersion->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutVersion);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['version' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByIpCityNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithIpCity = $this->createTestLoginLogForUser($user);

        $logWithoutIpCity = new LoginLog();
        $logWithoutIpCity->setUser($user);
        $logWithoutIpCity->setLoginIp('192.168.1.100');
        $logWithoutIpCity->setPlatform(Platform::IOS);
        $logWithoutIpCity->setIpCity(null);
        $logWithoutIpCity->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutIpCity);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['ipCity' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getIpCity());
    }

    public function testCountIpCityNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithIpCity = $this->createTestLoginLogForUser($user);

        $logWithoutIpCity = new LoginLog();
        $logWithoutIpCity->setUser($user);
        $logWithoutIpCity->setLoginIp('192.168.1.100');
        $logWithoutIpCity->setPlatform(Platform::IOS);
        $logWithoutIpCity->setIpCity(null);
        $logWithoutIpCity->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutIpCity);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['ipCity' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByIpLocationNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithIpLocation = $this->createTestLoginLogForUser($user);

        $logWithoutIpLocation = new LoginLog();
        $logWithoutIpLocation->setUser($user);
        $logWithoutIpLocation->setLoginIp('192.168.1.100');
        $logWithoutIpLocation->setPlatform(Platform::IOS);
        $logWithoutIpLocation->setIpLocation(null);
        $logWithoutIpLocation->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutIpLocation);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['ipLocation' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getIpLocation());
    }

    public function testCountIpLocationNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithIpLocation = $this->createTestLoginLogForUser($user);

        $logWithoutIpLocation = new LoginLog();
        $logWithoutIpLocation->setUser($user);
        $logWithoutIpLocation->setLoginIp('192.168.1.100');
        $logWithoutIpLocation->setPlatform(Platform::IOS);
        $logWithoutIpLocation->setIpLocation(null);
        $logWithoutIpLocation->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutIpLocation);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['ipLocation' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByDeviceModelNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithDeviceModel = $this->createTestLoginLogForUser($user);

        $logWithoutDeviceModel = new LoginLog();
        $logWithoutDeviceModel->setUser($user);
        $logWithoutDeviceModel->setLoginIp('192.168.1.100');
        $logWithoutDeviceModel->setPlatform(Platform::IOS);
        $logWithoutDeviceModel->setDeviceModel(null);
        self::getEntityManager()->persist($logWithoutDeviceModel);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['deviceModel' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getDeviceModel());
    }

    public function testCountDeviceModelNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithDeviceModel = $this->createTestLoginLogForUser($user);

        $logWithoutDeviceModel = new LoginLog();
        $logWithoutDeviceModel->setUser($user);
        $logWithoutDeviceModel->setLoginIp('192.168.1.100');
        $logWithoutDeviceModel->setPlatform(Platform::IOS);
        $logWithoutDeviceModel->setDeviceModel(null);
        self::getEntityManager()->persist($logWithoutDeviceModel);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['deviceModel' => null]);

        $this->assertEquals(1, $count);
    }

    public function testFindByNetTypeNullFieldValueShouldReturnMatchingLogs(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithNetType = $this->createTestLoginLogForUser($user);

        $logWithoutNetType = new LoginLog();
        $logWithoutNetType->setUser($user);
        $logWithoutNetType->setLoginIp('192.168.1.100');
        $logWithoutNetType->setPlatform(Platform::IOS);
        $logWithoutNetType->setNetType(null);
        $logWithoutNetType->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutNetType);
        self::getEntityManager()->flush();

        $result = $this->repository->findBy(['netType' => null]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertNull($result[0]->getNetType());
    }

    public function testCountNetTypeNullFieldValueShouldReturnCorrectCount(): void
    {
        $this->clearDatabase();
        $user = $this->createNormalUser();

        $logWithNetType = $this->createTestLoginLogForUser($user);

        $logWithoutNetType = new LoginLog();
        $logWithoutNetType->setUser($user);
        $logWithoutNetType->setLoginIp('192.168.1.100');
        $logWithoutNetType->setPlatform(Platform::IOS);
        $logWithoutNetType->setNetType(null);
        $logWithoutNetType->setDeviceModel('iPhone 15');
        self::getEntityManager()->persist($logWithoutNetType);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['netType' => null]);

        $this->assertEquals(1, $count);
    }

    private function clearDatabase(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM DeviceBundle\Entity\LoginLog')->execute();
        self::getEntityManager()->clear();
    }

    protected function createNewEntity(): object
    {
        $entity = new LoginLog();

        // 设置基本字段
        $entity->setLoginIp('192.168.1.' . rand(1, 254));
        $entity->setPlatform(Platform::WINDOWS);

        return $entity;
    }

    protected function getRepository(): ServiceEntityRepository&LoginLogRepository
    {
        return $this->repository;
    }
}
