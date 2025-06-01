<?php

namespace DeviceBundle\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Repository\DeviceRepository;
use Doctrine\ORM\EntityManagerInterface;

class DeviceService
{
    public function __construct(
        private readonly DeviceRepository $deviceRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    /**
     * 注册设备
     */
    public function register(string $model, string $code): Device
    {
        $device = $this->deviceRepository->findOneBy(['code' => $code]);
        if (!$device) {
            $device = new Device();
            $device->setCode($code);
            $device->setModel($model);
            $this->entityManager->persist($device);
            $this->entityManager->flush();
        }

        return $device;
    }
}
