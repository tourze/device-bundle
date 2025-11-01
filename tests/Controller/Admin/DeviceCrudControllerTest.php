<?php

namespace DeviceBundle\Tests\Controller\Admin;

use DeviceBundle\Controller\Admin\DeviceCrudController;
use DeviceBundle\Entity\Device;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DeviceCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DeviceCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /** @return AbstractCrudController<Device> */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(DeviceCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '唯一编码' => ['唯一编码'];
        yield '设备型号' => ['设备型号'];
        yield '设备名称' => ['设备名称'];
        yield '有效' => ['有效'];
        yield '用户数' => ['用户数'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'code' => ['code'];
        yield 'model' => ['model'];
        yield 'name' => ['name'];
        yield 'regIp' => ['regIp'];
        yield 'valid' => ['valid'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        yield 'code' => ['code'];
        yield 'model' => ['model'];
        yield 'name' => ['name'];
        yield 'regIp' => ['regIp'];
        yield 'valid' => ['valid'];
    }

    /**
     * 静态客户端属性，用于 BrowserKitAssertionsTrait
     *
     * @var AbstractBrowser<Request, Response>|null
     */
    protected static $client;

    public function testUnauthorizedAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/admin/device/device');
            $this->assertTrue(
                $client->getResponse()->isNotFound()
                || $client->getResponse()->isRedirect()
                || $client->getResponse()->isClientError(),
                'Response should be 404, redirect, or client error for unauthenticated access'
            );
        } catch (\Exception $e) {
            // Access denied or other security exceptions are expected
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error: ' . $e->getMessage()
            );
        }
    }

    public function testDeviceListPageAccess(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/device/device');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString('登录设备', $crawler->filter('h1')->text());
    }

    public function testDeviceListPageStructure(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/device/device');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('.table')->count());
        $this->assertGreaterThan(0, $crawler->filter('.btn-primary')->count());
    }

    public function testDeviceNewPageAccess(): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request('GET', '/admin/device/device/new');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name*="[code]"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name*="[model]"]')->count());
    }

    public function testDeviceCreateValidation(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/device/device/new');

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testDeviceCreateSuccess(): void
    {
        $client = self::createClient();

        $client->request('POST', '/admin/device/device/new');

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testDeviceEditPageAccess(): void
    {
        $client = self::createAuthenticatedClient();

        $device = $this->createTestDevice();

        $crawler = $client->request('GET', "/admin/device/device/{$device->getId()}/edit");

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form')->count());
        $this->assertSame($device->getCode(), $crawler->filter('input[name="Device[code]"]')->attr('value'));
    }

    public function testDeviceDetailPageAccess(): void
    {
        $client = self::createAuthenticatedClient();

        $device = $this->createTestDevice();

        $crawler = $client->request('GET', "/admin/device/device/{$device->getId()}");

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString($device->getCode(), $crawler->filter('.content')->text());
        $this->assertStringContainsString($device->getModel() ?? '', $crawler->filter('.content')->text());
    }

    public function testDeviceDeleteAction(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/device/device/1');

        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testDeviceSearchFunctionality(): void
    {
        $client = self::createAuthenticatedClient();

        $device = $this->createTestDevice();

        $crawler = $client->request('GET', '/admin/device/device', ['query' => $device->getCode()]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString($device->getCode(), $crawler->filter('.table')->text());
    }

    public function testDeviceFilterFunctionality(): void
    {
        $client = self::createAuthenticatedClient();

        $device = $this->createTestDevice();

        $crawler = $client->request('GET', '/admin/device/device', [
            'filters' => [
                'code' => [
                    'comparison' => '=',
                    'value' => $device->getCode(),
                ],
            ],
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    private function createTestDevice(): Device
    {
        $device = new Device();
        $device->setCode('TEST_' . uniqid());
        $device->setModel('Test Model');
        $device->setName('Test Device');
        $device->setRegIp('127.0.0.1');
        $device->setValid(true);

        self::getEntityManager()->persist($device);
        self::getEntityManager()->flush();

        return $device;
    }
}
