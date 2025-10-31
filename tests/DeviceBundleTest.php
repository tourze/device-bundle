<?php

declare(strict_types=1);

namespace DeviceBundle\Tests;

use DeviceBundle\DeviceBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DeviceBundle::class)]
#[RunTestsInSeparateProcesses]
final class DeviceBundleTest extends AbstractBundleTestCase
{
}
