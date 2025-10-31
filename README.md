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

A Symfony bundle for managing user devices and login logs with comprehensive tracking
capabilities.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Usage](#usage)
- [Advanced Usage](#advanced-usage)
- [Database Schema](#database-schema)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Device Management**: Register and manage user devices with unique codes
- **User-Device Association**: Many-to-many relationship between users and devices
- **Login Tracking**: Detailed login logs with IP, platform, device info
- **Admin Interface**: Built-in EasyAdmin controllers for device management
- **Data Fixtures**: Pre-configured test data for development
- **Automatic Cleanup**: Scheduled cleanup of old login logs
- **Platform Detection**: Support for various platforms (iOS, Android, etc.)

## Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher
- Doctrine ORM 3.0+
- EasyAdmin Bundle 4.x

## Installation

```bash
composer require tourze/device-bundle
```

**Note**: If you're working in this monorepo environment, the bundle is automatically available. For Symfony Flex projects, the bundle will be automatically registered in `config/bundles.php`.

## Configuration

The bundle provides optional configuration for customizing behavior:

```yaml
# config/packages/device.yaml
device:
    # Optional: Configure login log cleanup
    cleanup:
        enabled: true
        retention_days: 30
    
    # Optional: Admin interface settings
    admin:
        enabled: true
        menu_section: "Device Management"
```

## Quick Start

### 1. Register a Device

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

### 2. Track Login Events

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

## Usage

### Device Service

The `DeviceService` provides methods for device management:

```php
<?php

use DeviceBundle\Service\DeviceService;

$deviceService = $container->get(DeviceService::class);

// Register new device (or get existing)
$device = $deviceService->register('iPhone 15', 'UNIQUE_CODE');

// Find device by code
$device = $deviceService->findByCode('UNIQUE_CODE');

// Get all devices for a user
$devices = $deviceService->getDevicesForUser($user);
```

### Login Log Service

Track user login activities:

```php
<?php

use DeviceBundle\Service\LoginLogService;
use DeviceBundle\Enum\Platform;

$logService = $container->get(LoginLogService::class);

// Log a login event
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

// Get recent logins for user
$recentLogins = $logService->getRecentLoginsForUser($user, limit: 10);
```

### Device Status Management

Monitor and update device online status:

```php
<?php

use DeviceBundle\Service\DeviceStatusManager;

$statusManager = $container->get(DeviceStatusManager::class);

// Update device online status
$statusManager->updateOnlineStatus($device, isOnline: true);

// Update last connection info
$statusManager->updateLastConnection($device, ip: '192.168.1.100');

// Get online devices
$onlineDevices = $statusManager->getOnlineDevices();

// Check for timeout devices (offline for > 5 minutes)
$statusManager->checkAndUpdateTimeoutDevices(timeoutSeconds: 300);
```

## Advanced Usage

### Custom Device Types

You can extend the device type system:

```php
<?php

use DeviceBundle\Enum\DeviceType;

// The bundle supports various device types
$device->setDeviceType(DeviceType::PHONE);
$device->setDeviceType(DeviceType::TABLET);
$device->setDeviceType(DeviceType::DESKTOP);
```

### Hardware Information Tracking

Update device hardware details:

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

### Custom Data Fixtures

Create your own device fixtures:

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
        // Use predefined devices
        $device1 = $this->getReference(DeviceFixtures::DEVICE_REFERENCE_1);
        
        // Create custom associations
        // ... your custom logic
    }
    
    public function getDependencies(): array
    {
        return [DeviceFixtures::class];
    }
}
```

### Admin Interface Customization

The bundle provides admin controllers that can be customized:

```php
<?php

// Admin controllers are automatically registered
// - DeviceBundle\Controller\Admin\DeviceCrudController
// - DeviceBundle\Controller\Admin\LoginLogCrudController

// Customize in your dashboard:
use DeviceBundle\Controller\Admin\DeviceCrudController;

public function configureMenuItems(): iterable
{
    // ... other menu items
    
    yield MenuItem::linkToCrud('Devices', 'fas fa-mobile-alt', Device::class);
    yield MenuItem::linkToCrud('Login Logs', 'fas fa-history', LoginLog::class);
}
```

### Data Fixtures

Load test data for development:

```bash
# Load all fixtures
php bin/console doctrine:fixtures:load

# Load only device fixtures
php bin/console doctrine:fixtures:load --group=device

# Append without truncating
php bin/console doctrine:fixtures:load --append
```

## Database Schema

### Device Table (`ims_device`)

- `id` - Snowflake ID
- `code` - Unique device identifier
- `model` - Device model name
- `name` - Device display name
- `valid` - Device validity status
- `reg_ip` - Registration IP address
- `create_time` / `update_time` - Timestamps

### Login Log Table (`device_login_log`)

- `id` - Auto-increment ID
- `user_id` - Associated user
- `login_ip` - Login IP address
- `login_platform` - Platform enum
- `login_imei` - Device IMEI
- `login_channel` - Login channel
- `system_version` - OS version
- `version` - App version
- `ip_city` - IP location city
- `ip_location` - Full IP location
- `device_model` - Device model
- `net_type` - Network type
- `create_time` - Log timestamp

## Security

### Data Privacy

- **IP Address Logging**: Login IPs are stored for security tracking. Consider GDPR
  compliance for EU users.
- **Device Fingerprinting**: Hardware fingerprints are stored as TEXT fields with
  length validation.
- **Automatic Cleanup**: Old login logs are automatically cleaned up according to
  configured retention periods.

### Input Validation

- All entity properties include proper validation constraints
- Device codes must be unique and are validated for length
- IP addresses are validated using Symfony's IP constraint
- Device models use regex validation to prevent malicious input

### Access Control

- Admin controllers should be protected by proper authentication
- Consider implementing role-based access for device management
- Use CSRF protection on admin forms

## Contributing

Please see [CONTRIBUTING.md](../../CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.