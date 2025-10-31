<?php

namespace DeviceBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 设备类型枚举
 */
enum DeviceType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PHONE = 'phone';
    case TABLET = 'tablet';
    case EMULATOR = 'emulator';
    case DESKTOP = 'desktop';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::PHONE => '手机',
            self::TABLET => '平板',
            self::EMULATOR => '模拟器',
            self::DESKTOP => '桌面设备',
            self::OTHER => '其他',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }

    /**
     * 获取设备类型图标
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::PHONE => 'smartphone',
            self::TABLET => 'tablet',
            self::EMULATOR => 'phone_android',
            self::DESKTOP => 'computer',
            self::OTHER => 'devices_other',
        };
    }
}
