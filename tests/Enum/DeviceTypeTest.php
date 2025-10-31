<?php

namespace DeviceBundle\Tests\Enum;

use DeviceBundle\Enum\DeviceType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DeviceType::class)]
final class DeviceTypeTest extends AbstractEnumTestCase
{
    #[TestWith([DeviceType::PHONE, 'phone', '手机'])]
    #[TestWith([DeviceType::TABLET, 'tablet', '平板'])]
    #[TestWith([DeviceType::EMULATOR, 'emulator', '模拟器'])]
    #[TestWith([DeviceType::DESKTOP, 'desktop', '桌面设备'])]
    #[TestWith([DeviceType::OTHER, 'other', '其他'])]
    public function testValueAndLabel(DeviceType $type, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $type->value);
        $this->assertSame($expectedLabel, $type->getLabel());
    }

    public function testValueUniqueness(): void
    {
        $values = array_map(fn (DeviceType $type) => $type->value, DeviceType::cases());
        $this->assertSame($values, array_unique($values), 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = array_map(fn (DeviceType $type) => $type->getLabel(), DeviceType::cases());
        $this->assertSame($labels, array_unique($labels), 'All enum labels must be unique');
    }

    public function testGetIcon(): void
    {
        $this->assertSame('smartphone', DeviceType::PHONE->getIcon());
        $this->assertSame('tablet', DeviceType::TABLET->getIcon());
        $this->assertSame('phone_android', DeviceType::EMULATOR->getIcon());
        $this->assertSame('computer', DeviceType::DESKTOP->getIcon());
        $this->assertSame('devices_other', DeviceType::OTHER->getIcon());
    }

    public function testCases(): void
    {
        $cases = DeviceType::cases();
        $this->assertCount(5, $cases);
        $this->assertContains(DeviceType::PHONE, $cases);
        $this->assertContains(DeviceType::TABLET, $cases);
        $this->assertContains(DeviceType::EMULATOR, $cases);
        $this->assertContains(DeviceType::DESKTOP, $cases);
        $this->assertContains(DeviceType::OTHER, $cases);
    }

    public function testToArray(): void
    {
        $phoneArray = DeviceType::PHONE->toArray();
        $this->assertSame(['value' => 'phone', 'label' => '手机'], $phoneArray);

        $tabletArray = DeviceType::TABLET->toArray();
        $this->assertSame(['value' => 'tablet', 'label' => '平板'], $tabletArray);

        $emulatorArray = DeviceType::EMULATOR->toArray();
        $this->assertSame(['value' => 'emulator', 'label' => '模拟器'], $emulatorArray);

        $desktopArray = DeviceType::DESKTOP->toArray();
        $this->assertSame(['value' => 'desktop', 'label' => '桌面设备'], $desktopArray);

        $otherArray = DeviceType::OTHER->toArray();
        $this->assertSame(['value' => 'other', 'label' => '其他'], $otherArray);
    }
}
