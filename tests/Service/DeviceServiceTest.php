<?php

namespace DeviceBundle\Test\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Repository\DeviceRepository;
use DeviceBundle\Service\DeviceService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeviceServiceTest extends TestCase
{
    private DeviceService $service;
    private DeviceRepository&MockObject $deviceRepository;
    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        $this->deviceRepository = $this->createMock(DeviceRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->service = new DeviceService(
            $this->deviceRepository,
            $this->entityManager
        );
    }

    public function testRegisterNewDevice(): void
    {
        $model = 'iPhone 15';
        $code = 'ABC123';
        
        $this->deviceRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn(null);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Device::class));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        $device = $this->service->register($model, $code);
        
        $this->assertInstanceOf(Device::class, $device);
        $this->assertEquals($code, $device->getCode());
        $this->assertEquals($model, $device->getModel());
    }
    
    public function testRegisterExistingDevice(): void
    {
        $model = 'iPhone 15';
        $code = 'ABC123';
        
        $existingDevice = new Device();
        $existingDevice->setCode($code);
        $existingDevice->setModel('iPhone 14');
        
        $this->deviceRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $code])
            ->willReturn($existingDevice);
            
        $this->entityManager->expects($this->never())
            ->method('persist');
            
        $this->entityManager->expects($this->never())
            ->method('flush');
            
        $device = $this->service->register($model, $code);
        
        $this->assertSame($existingDevice, $device);
        $this->assertEquals('iPhone 14', $device->getModel());
    }
}