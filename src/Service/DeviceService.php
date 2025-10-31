<?php

namespace DeviceBundle\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Repository\DeviceRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class DeviceService
{
    public function __construct(
        private DeviceRepository $deviceRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 注册设备
     */
    public function register(string $model, string $code): Device
    {
        $device = $this->deviceRepository->findOneBy(['code' => $code]);
        if (null === $device) {
            $device = new Device();
            $device->setCode($code);
            $device->setModel($model);
            $this->entityManager->persist($device);
            $this->entityManager->flush();
        }

        return $device;
    }
}
