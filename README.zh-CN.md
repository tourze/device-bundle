# DeviceBundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version Require](https://poser.pugx.org/tourze/device-bundle/require/php)](
https://packagist.org/packages/tourze/device-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/device-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/device-bundle)

[![License](https://poser.pugx.org/tourze/device-bundle/license)](
https://packagist.org/packages/tourze/device-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/device-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/device-bundle)
[![Build Status](https://github.com/tourze/device-bundle/workflows/CI/badge.svg)](
https://github.com/tourze/device-bundle/actions)
[![Code Coverage](https://codecov.io/gh/tourze/device-bundle/branch/master/graph/badge.svg)](
https://codecov.io/gh/tourze/device-bundle)

用于管理用户设备和登录日志的 Symfony Bundle，具有全面的追踪功能。

## 目录

- [功能特性](#功能特性)
- [系统要求](#系统要求)
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [使用方法](#使用方法)
- [高级用法](#高级用法)
- [数据库架构](#数据库架构)
- [安全](#安全)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **设备管理**: 使用唯一代码注册和管理用户设备
- **用户-设备关联**: 用户和设备之间的多对多关系
- **登录追踪**: 详细的登录日志，包含 IP、平台、设备信息
- **管理界面**: 内置的 EasyAdmin 控制器用于设备管理
- **数据填充**: 预配置的测试数据用于开发
- **自动清理**: 定时清理旧的登录日志
- **平台检测**: 支持各种平台（iOS、Android 等）

## 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM 3.0+
- EasyAdmin Bundle 4.x

## 安装

```bash
composer require tourze/device-bundle
```

**注意**: 如果您在此 monorepo 环境中工作，Bundle 会自动可用。对于 Symfony Flex 项目，Bundle 将在 `config/bundles.php` 中自动注册。

## 配置

Bundle 提供可选配置来自定义行为：

```yaml
# config/packages/device.yaml
device:
    # 可选：配置登录日志清理
    cleanup:
        enabled: true
        retention_days: 30
    
    # 可选：管理界面设置
    admin:
        enabled: true
        menu_section: "设备管理"
```

## 快速开始

### 1. 注册设备

```php
<?php

use DeviceBundle\Service\DeviceService;
use DeviceBundle\Entity\Device;

class YourController
{
    public function __construct(
        private DeviceService $deviceService
    ) {}
    
    public function registerDevice(): Device
    {
        return $this->deviceService->register(
            model: 'iPhone 15 Pro',
            code: 'ABC123DEF456'
        );
    }
}
```

### 2. 追踪登录事件

```php
<?php

use DeviceBundle\Service\LoginLogService;
use DeviceBundle\Entity\LoginLog;

class AuthController
{
    public function __construct(
        private LoginLogService $loginLogService
    ) {}
    
    public function logUserLogin(User $user, Request $request): LoginLog
    {
        return $this->loginLogService->logLogin(
            user: $user,
            loginIp: $request->getClientIp(),
            loginPlatform: 'web',
            additionalData: [
                'user_agent' => $request->headers->get('User-Agent'),
                'system_version' => 'Web Browser',
            ]
        );  
    }
}
```

## 使用方法

### 设备服务

`DeviceService` 提供设备管理方法：

```php
<?php

use DeviceBundle\Service\DeviceService;

$deviceService = $container->get(DeviceService::class);

// 注册新设备（或获取现有设备）
$device = $deviceService->register('iPhone 15', 'UNIQUE_CODE');

// 通过代码查找设备
$device = $deviceService->findByCode('UNIQUE_CODE');

// 获取用户的所有设备
$devices = $deviceService->getDevicesForUser($user);
```

### 登录日志服务

追踪用户登录活动：

```php
<?php

use DeviceBundle\Service\LoginLogService;
use DeviceBundle\Enum\Platform;

$logService = $container->get(LoginLogService::class);

// 记录登录事件
$loginLog = $logService->logLogin(
    user: $user,
    loginIp: '192.168.1.100',
    loginPlatform: Platform::IOS->value,
    additionalData: [
        'login_imei' => 'DEVICE_IMEI',
        'system_version' => 'iOS 17.0',
        'version' => '1.0.0',
        'device_model' => 'iPhone 15 Pro',
        'net_type' => 'wifi'
    ]
);

// 获取用户最近的登录记录
$recentLogins = $logService->getRecentLoginsForUser($user, limit: 10);
```

### 设备状态管理

监控和更新设备在线状态：

```php
<?php

use DeviceBundle\Service\DeviceStatusManager;

$statusManager = $container->get(DeviceStatusManager::class);

// 更新设备在线状态
$statusManager->updateOnlineStatus($device, isOnline: true);

// 更新最后连接信息
$statusManager->updateLastConnection($device, ip: '192.168.1.100');

// 获取在线设备
$onlineDevices = $statusManager->getOnlineDevices();

// 检查超时设备（离线超过 5 分钟）
$statusManager->checkAndUpdateTimeoutDevices(timeoutSeconds: 300);
```

## 高级用法

### 自定义设备类型

您可以扩展设备类型系统：

```php
<?php

use DeviceBundle\Enum\DeviceType;

// Bundle 支持各种设备类型
$device->setDeviceType(DeviceType::PHONE);
$device->setDeviceType(DeviceType::TABLET);
$device->setDeviceType(DeviceType::DESKTOP);
```

### 硬件信息追踪

更新设备硬件详情：

```php
<?php

use DeviceBundle\Service\DeviceStatusManager;

$statusManager->updateHardwareInfo($device, [
    'deviceType' => 'phone',
    'osVersion' => 'Android 14',
    'brand' => 'Samsung',
    'cpuCores' => 8,
    'memorySize' => '8192',  // MB
    'storageSize' => '256000',  // MB
    'fingerprint' => 'hardware_fingerprint'
]);
```

### 自定义数据填充

创建您自己的设备数据填充：

```php
<?php

namespace App\DataFixtures;

use DeviceBundle\DataFixtures\DeviceFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppDeviceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // 使用预定义设备
        $device1 = $this->getReference(DeviceFixtures::DEVICE_REFERENCE_1);
        
        // 创建自定义关联
        // ... 您的自定义逻辑
    }
    
    public function getDependencies(): array
    {
        return [DeviceFixtures::class];
    }
}
```

### 管理界面自定义

Bundle 提供可自定义的管理控制器：

```php
<?php

// 管理控制器会自动注册
// - DeviceBundle\Controller\Admin\DeviceCrudController
// - DeviceBundle\Controller\Admin\LoginLogCrudController

// 在您的仪表板中自定义：
use DeviceBundle\Controller\Admin\DeviceCrudController;

public function configureMenuItems(): iterable
{
    // ... 其他菜单项
    
    yield MenuItem::linkToCrud('设备', 'fas fa-mobile-alt', Device::class);
    yield MenuItem::linkToCrud('登录日志', 'fas fa-history', LoginLog::class);
}
```

### 数据填充

为开发加载测试数据：

```bash
# 加载所有数据填充
php bin/console doctrine:fixtures:load

# 仅加载设备数据填充
php bin/console doctrine:fixtures:load --group=device

# 追加而不截断
php bin/console doctrine:fixtures:load --append
```

## 数据库架构

### 设备表 (`ims_device`)

- `id` - 雪花 ID
- `code` - 唯一设备标识符
- `model` - 设备型号名称
- `name` - 设备显示名称
- `valid` - 设备有效性状态
- `reg_ip` - 注册 IP 地址
- `create_time` / `update_time` - 时间戳

### 登录日志表 (`device_login_log`)

- `id` - 自增 ID
- `user_id` - 关联用户
- `login_ip` - 登录 IP 地址
- `login_platform` - 平台枚举
- `login_imei` - 设备 IMEI
- `login_channel` - 登录渠道
- `system_version` - 操作系统版本
- `version` - 应用版本
- `ip_city` - IP 位置城市
- `ip_location` - 完整 IP 位置
- `device_model` - 设备型号
- `net_type` - 网络类型
- `create_time` - 日志时间戳

## 安全

### 数据隐私

- **IP地址记录**: 登录IP用于安全追踪存储。对欧盟用户请考虑GDPR合规性。
- **设备指纹**: 硬件指纹存储为带长度验证的TEXT字段。
- **自动清理**: 旧登录日志根据配置的保留期自动清理。

### 输入验证

- 所有实体属性包含适当的验证约束
- 设备代码必须唯一并进行长度验证
- IP地址使用Symfony的IP约束进行验证
- 设备型号使用正则表达式验证以防止恶意输入

### 访问控制

- 管理控制器应受到适当身份验证保护
- 考虑为设备管理实施基于角色的访问控制
- 在管理表单上使用CSRF保护

## 贡献

请查看 [CONTRIBUTING.md](../../CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证。请查看 [许可证文件](LICENSE) 获取更多信息。