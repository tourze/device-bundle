<?php

namespace DeviceBundle\Controller\Admin;

use DeviceBundle\Entity\Device;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Device>
 */
#[AdminCrud(routePath: '/device/device', routeName: 'device_device')]
final class DeviceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Device::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('登录设备')
            ->setEntityLabelInPlural('登录设备')
            ->setPageTitle('index', '登录设备列表')
            ->setPageTitle('detail', fn (Device $device) => sprintf('设备详情: %s', $device->getCode()))
            ->setPageTitle('edit', fn (Device $device) => sprintf('编辑设备: %s', $device->getCode()))
            ->setPageTitle('new', '添加设备')
            ->setHelp('index', '管理系统中的所有登录设备信息')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['code', 'model', 'name', 'regIp'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('code', '唯一编码')
            ->setHelp('设备的唯一标识符')
        ;

        yield TextField::new('model', '设备型号')
            ->setHelp('设备的型号信息')
        ;

        yield TextField::new('name', '设备名称')
            ->setRequired(false)
        ;

        yield TextField::new('regIp', '注册IP')
            ->setHelp('设备首次注册时的IP地址')
            ->hideOnIndex()
        ;

        yield BooleanField::new('valid', '有效');

        // 用户关联字段仅在详情页显示
        if (Crud::PAGE_DETAIL === $pageName) {
            yield AssociationField::new('users', '关联用户')
                ->hideOnForm()
            ;
        }

        // 用户数量字段仅在列表和详情页显示
        if (Crud::PAGE_INDEX === $pageName || Crud::PAGE_DETAIL === $pageName) {
            yield IntegerField::new('userCount', '用户数')
                ->formatValue(function ($value, Device $entity) {
                    return $entity->getUserCount();
                })
                ->hideOnForm()
            ;
        }

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('code', '唯一编码'))
            ->add(TextFilter::new('model', '设备型号'))
            ->add(TextFilter::new('name', '设备名称'))
            ->add(TextFilter::new('regIp', '注册IP'))
            ->add(BooleanFilter::new('valid', '有效'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // 添加详情操作到列表页
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
