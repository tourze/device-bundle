<?php

namespace DeviceBundle\Service;

use DeviceBundle\Entity\Device;
use DeviceBundle\Entity\LoginLog;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('设备管理')) {
            $item->addChild('设备管理');
        }

        $deviceMenu = $item->getChild('设备管理');
        if (null === $deviceMenu) {
            return;
        }

        $deviceMenu
            ->addChild('设备管理')
            ->setUri($this->linkGenerator->getCurdListPage(Device::class))
            ->setAttribute('icon', 'fas fa-mobile-alt')
        ;

        $deviceMenu
            ->addChild('登录日志')
            ->setUri($this->linkGenerator->getCurdListPage(LoginLog::class))
            ->setAttribute('icon', 'fas fa-history')
        ;
    }
}
