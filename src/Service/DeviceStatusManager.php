<?php

namespace DeviceBundle\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Enum\DeviceStatus;
use DeviceBundle\Enum\DeviceType;
use DeviceBundle\Repository\DeviceRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeviceStatusManager implements DeviceStatusManagerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DeviceRepository $deviceRepository,
    ) {
    }

    public function updateOnlineStatus(Device $device, bool $isOnline): void
    {
        $device->setStatus($isOnline ? DeviceStatus::ONLINE : DeviceStatus::OFFLINE);
        if ($isOnline) {
            $device->setLastOnlineTime(new \DateTimeImmutable());
        }

        $this->entityManager->persist($device);
        $this->entityManager->flush();
    }

    public function updateLastConnection(Device $device, string $ip): void
    {
        $device->setLastIp($ip);
        $device->setLastOnlineTime(new \DateTimeImmutable());

        $this->entityManager->persist($device);
        $this->entityManager->flush();
    }

    public function getOnlineDevices(): array
    {
        return $this->deviceRepository->findBy([
            'status' => DeviceStatus::ONLINE,
            'valid' => true,
        ]);
    }

    public function getOfflineDevices(): array
    {
        return $this->deviceRepository->findBy([
            'status' => DeviceStatus::OFFLINE,
            'valid' => true,
        ]);
    }

    public function checkAndUpdateTimeoutDevices(int $timeoutSeconds = 300): void
    {
        $threshold = new \DateTimeImmutable(sprintf('-%d seconds', $timeoutSeconds));

        $qb = $this->deviceRepository->createQueryBuilder('d');
        $qb->update()
            ->set('d.status', ':offline')
            ->where('d.status = :online')
            ->andWhere('d.lastOnlineTime < :threshold')
            ->setParameter('offline', DeviceStatus::OFFLINE->value)
            ->setParameter('online', DeviceStatus::ONLINE->value)
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param array<string, mixed> $info
     */
    public function updateHardwareInfo(Device $device, array $info): void
    {
        $this->updateDeviceType($device, $info);
        $this->updateStringFields($device, $info);
        $this->updateNumericFields($device, $info);

        $this->entityManager->persist($device);
        $this->entityManager->flush();
    }

    /**
     * @param array<string, mixed> $info
     */
    private function updateDeviceType(Device $device, array $info): void
    {
        if (isset($info['deviceType']) && is_string($info['deviceType'])) {
            $device->setDeviceType(DeviceType::tryFrom($info['deviceType']));
        }
    }

    /**
     * @param array<string, mixed> $info
     */
    private function updateStringFields(Device $device, array $info): void
    {
        if (isset($info['osVersion']) && is_string($info['osVersion'])) {
            $device->setOsVersion($info['osVersion']);
        }

        if (isset($info['brand']) && is_string($info['brand'])) {
            $device->setBrand($info['brand']);
        }

        if (isset($info['fingerprint']) && is_string($info['fingerprint'])) {
            $device->setFingerprint($info['fingerprint']);
        }
    }

    /**
     * @param array<string, mixed> $info
     */
    private function updateNumericFields(Device $device, array $info): void
    {
        if (isset($info['cpuCores']) && is_numeric($info['cpuCores'])) {
            $device->setCpuCores((int) $info['cpuCores']);
        }

        if (isset($info['memorySize']) && (is_string($info['memorySize']) || is_numeric($info['memorySize']))) {
            $device->setMemorySize((string) $info['memorySize']);
        }

        if (isset($info['storageSize']) && (is_string($info['storageSize']) || is_numeric($info['storageSize']))) {
            $device->setStorageSize((string) $info['storageSize']);
        }
    }
}
