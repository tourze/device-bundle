<?php

namespace DeviceBundle\Controller\Admin;

use DeviceBundle\Entity\Device;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DeviceCrudController extends AbstractCrudController
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
            ->setPageTitle('detail', fn(Device $device) => sprintf('设备详情: %s', $device->getCode()))
            ->setPageTitle('edit', fn(Device $device) => sprintf('编辑设备: %s', $device->getCode()))
            ->setPageTitle('new', '添加设备')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['code', 'model', 'name', 'regIp']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm();

        yield TextField::new('code', '唯一编码');
        yield TextField::new('model', '设备型号');
        yield TextField::new('name', '设备名称');
        yield TextField::new('regIp', '注册IP')
            ->hideOnIndex();

        yield BooleanField::new('valid', '有效');

        if ($pageName === Crud::PAGE_INDEX || $pageName === Crud::PAGE_DETAIL) {
            yield IntegerField::new('userCount', '用户数')
                ->formatValue(function ($value, $entity) {
                    return $entity->getUserCount();
                });
        }

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm();
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
            ->add(DateTimeFilter::new('updateTime', '更新时间'));
    }

    public function configureActions(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions $actions): \EasyCorp\Bundle\EasyAdminBundle\Config\Actions
    {
        // 全新配置 Actions
        $newActions = Actions::new()
            // 批量操作
            ->addBatchAction(Action::BATCH_DELETE)

            // 列表页面操作
            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            // 详情页面操作
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::DELETE)

            // 编辑页面操作
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)

            // 新建页面操作
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)

            // 自定义操作标签
            ->update(Crud::PAGE_DETAIL, Action::INDEX, function (Action $action) {
                return $action->setLabel('返回列表');
            });

        return $newActions;
    }
}
