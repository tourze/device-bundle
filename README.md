# DeviceBundle

## 简介

DeviceBundle 提供设备管理功能。

## 功能

- 设备注册与管理
- 设备与用户关联

## 数据填充 (Fixtures)

DeviceBundle 提供了一组数据填充工具，用于生成测试/开发环境所需的初始数据。

### 可用的 Fixtures

**DeviceFixtures**: 创建测试设备数据

### 使用方法

在项目根目录执行以下命令加载所有 fixtures:

```bash
php bin/console doctrine:fixtures:load
```

仅加载设备相关 fixture:

```bash
php bin/console doctrine:fixtures:load --group=device
```

添加数据而不清空数据库:

```bash
php bin/console doctrine:fixtures:load --append
```

### 引用关系

在开发自定义 fixtures 时，可以使用以下常量引用已创建的设备实体:

- `DeviceFixtures::DEVICE_REFERENCE_1` (iPhone 设备)
- `DeviceFixtures::DEVICE_REFERENCE_2` (Samsung 设备)
- `DeviceFixtures::DEVICE_REFERENCE_3` (Xiaomi 设备，无效状态)
