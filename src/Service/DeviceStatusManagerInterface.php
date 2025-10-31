<?php

namespace DeviceBundle\Service;

use DeviceBundle\Entity\Device;

interface DeviceStatusManagerInterface
{
    /**
     * 更新设备在线状态
     */
    public function updateOnlineStatus(Device $device, bool $isOnline): void;

    /**
     * 更新最后连接信息
     */
    public function updateLastConnection(Device $device, string $ip): void;

    /**
     * 获取所有在线设备
     *
     * @return Device[]
     */
    public function getOnlineDevices(): array;

    /**
     * 获取所有离线设备
     *
     * @return Device[]
     */
    public function getOfflineDevices(): array;

    /**
     * 检查并更新超时设备状态
     */
    public function checkAndUpdateTimeoutDevices(int $timeoutSeconds = 300): void;

    /**
     * 更新设备硬件信息
     * @param array<string, mixed> $info
     */
    public function updateHardwareInfo(Device $device, array $info): void;
}
