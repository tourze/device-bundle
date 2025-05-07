<?php

namespace DeviceBundle\Service;

use DeviceBundle\Entity\Device;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('设备管理')) {
            $item->addChild('设备管理');
        }

        $item->getChild('设备管理')
            ->addChild('设备管理')
            ->setUri($this->linkGenerator->getCurdListPage(Device::class))
            ->setAttribute('icon', 'fas fa-mobile-alt');
    }
}
