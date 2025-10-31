<?php

namespace DeviceBundle\Tests\Enum;

use DeviceBundle\Enum\DeviceStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DeviceStatus::class)]
final class DeviceStatusTest extends AbstractEnumTestCase
{
    public function testFromWithValidValue(): void
    {
        $this->assertSame(DeviceStatus::ONLINE, DeviceStatus::from('online'));
        $this->assertSame(DeviceStatus::OFFLINE, DeviceStatus::from('offline'));
        $this->assertSame(DeviceStatus::DISABLED, DeviceStatus::from('disabled'));
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertSame(DeviceStatus::ONLINE, DeviceStatus::tryFrom('online'));
        $this->assertSame(DeviceStatus::OFFLINE, DeviceStatus::tryFrom('offline'));
        $this->assertSame(DeviceStatus::DISABLED, DeviceStatus::tryFrom('disabled'));
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (DeviceStatus $status) => $status->value, DeviceStatus::cases());
        $this->assertSame($values, array_unique($values), 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (DeviceStatus $status) => $status->getLabel(), DeviceStatus::cases());
        $this->assertSame($labels, array_unique($labels), 'All enum labels must be unique');
    }

    public function testGetColor(): void
    {
        $this->assertSame('success', DeviceStatus::ONLINE->getColor());
        $this->assertSame('warning', DeviceStatus::OFFLINE->getColor());
        $this->assertSame('danger', DeviceStatus::DISABLED->getColor());
    }

    public function testGetBadgeClass(): void
    {
        $this->assertSame('badge-success', DeviceStatus::ONLINE->getBadgeClass());
        $this->assertSame('badge-warning', DeviceStatus::OFFLINE->getBadgeClass());
        $this->assertSame('badge-danger', DeviceStatus::DISABLED->getBadgeClass());
    }

    public function testIsActive(): void
    {
        $this->assertTrue(DeviceStatus::ONLINE->isActive());
        $this->assertFalse(DeviceStatus::OFFLINE->isActive());
        $this->assertFalse(DeviceStatus::DISABLED->isActive());
    }

    public function testIsEnabled(): void
    {
        $this->assertTrue(DeviceStatus::ONLINE->isEnabled());
        $this->assertTrue(DeviceStatus::OFFLINE->isEnabled());
        $this->assertFalse(DeviceStatus::DISABLED->isEnabled());
    }

    public function testCases(): void
    {
        $cases = DeviceStatus::cases();
        $this->assertCount(3, $cases);
        $this->assertContains(DeviceStatus::ONLINE, $cases);
        $this->assertContains(DeviceStatus::OFFLINE, $cases);
        $this->assertContains(DeviceStatus::DISABLED, $cases);
    }

    public function testToArray(): void
    {
        $onlineArray = DeviceStatus::ONLINE->toArray();
        $this->assertSame(['value' => 'online', 'label' => '在线'], $onlineArray);

        $offlineArray = DeviceStatus::OFFLINE->toArray();
        $this->assertSame(['value' => 'offline', 'label' => '离线'], $offlineArray);

        $disabledArray = DeviceStatus::DISABLED->toArray();
        $this->assertSame(['value' => 'disabled', 'label' => '已禁用'], $disabledArray);
    }
}
