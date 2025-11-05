<?php

namespace DeviceBundle\Controller\Admin;

use DeviceBundle\Entity\LoginLog;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Tourze\OperationSystemEnum\Platform;

/**
 * @extends AbstractCrudController<LoginLog>
 */
#[AdminCrud(routePath: '/device/login-log', routeName: 'device_login_log')]
final class LoginLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LoginLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('登录日志')
            ->setEntityLabelInPlural('登录日志')
            ->setPageTitle('index', '登录日志列表')
            ->setPageTitle('detail', fn (LoginLog $log) => sprintf('登录日志详情: #%d', $log->getId()))
            ->setPageTitle('edit', fn (LoginLog $log) => sprintf('编辑登录日志: #%d', $log->getId()))
            ->setPageTitle('new', '添加登录日志')
            ->setHelp('index', '查看用户设备登录记录和统计信息')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['loginIp', 'imei', 'deviceModel', 'version', 'ipCity'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('user', '用户')
            ->setRequired(true)
        ;

        yield TextField::new('loginIp', '登录IP')
            ->setHelp('用户登录时的IP地址')
        ;

        yield ChoiceField::new('platform', '登录平台')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => Platform::class])
            ->formatValue(function ($value) {
                return $value instanceof Platform ? $value->getLabel() : '';
            })
        ;

        yield TextField::new('imei', '设备IMEI')
            ->hideOnIndex()
        ;

        yield TextField::new('channel', '登录渠道')
            ->hideOnIndex()
        ;

        yield TextField::new('systemVersion', '系统版本')
            ->hideOnIndex()
        ;

        yield TextField::new('version', 'APP版本');

        yield TextField::new('ipCity', '地区');

        yield TextField::new('ipLocation', 'IP位置')
            ->hideOnIndex()
        ;

        yield TextField::new('deviceModel', '设备型号');

        yield TextField::new('netType', '网络类型')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        // 构建平台选项
        $platformChoices = [];
        foreach (Platform::cases() as $case) {
            $platformChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('user', '用户'))
            ->add(TextFilter::new('loginIp', '登录IP'))
            ->add(ChoiceFilter::new('platform', '登录平台')->setChoices($platformChoices))
            ->add(TextFilter::new('imei', '设备IMEI'))
            ->add(TextFilter::new('version', 'APP版本'))
            ->add(TextFilter::new('ipCity', '地区'))
            ->add(TextFilter::new('deviceModel', '设备型号'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // 禁用新建和编辑操作，日志通常只读
            ->disable(Action::NEW, Action::EDIT)
            // 添加详情操作到列表页
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
