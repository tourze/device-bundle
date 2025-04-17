<?php

namespace DeviceBundle\Controller\Admin;

use DeviceBundle\Entity\Device;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DeviceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Device::class;
    }
}
