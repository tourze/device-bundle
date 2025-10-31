<?php

namespace DeviceBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 设备状态枚举
 */
enum DeviceStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case DISABLED = 'disabled';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => '在线',
            self::OFFLINE => '离线',
            self::DISABLED => '已禁用',
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
     * 检查设备是否为激活状态
     */
    public function isActive(): bool
    {
        return self::ONLINE === $this;
    }

    /**
     * 检查设备是否可用
     */
    public function isEnabled(): bool
    {
        return self::DISABLED !== $this;
    }

    /**
     * 获取状态对应的颜色
     */
    public function getColor(): string
    {
        return match ($this) {
            self::ONLINE => 'success',
            self::OFFLINE => 'warning',
            self::DISABLED => 'danger',
        };
    }

    /**
     * 获取状态对应的徽章样式类
     */
    public function getBadgeClass(): string
    {
        return match ($this) {
            self::ONLINE => 'badge-success',
            self::OFFLINE => 'badge-warning',
            self::DISABLED => 'badge-danger',
        };
    }
}
