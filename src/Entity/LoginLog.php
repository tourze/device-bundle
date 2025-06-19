<?php

namespace DeviceBundle\Entity;

use DeviceBundle\Repository\LoginLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\OperationSystemEnum\Platform;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;

/**
 * 这个本质是设备登录记录
 */
#[AsPermission('登录日志')]
#[AsScheduleClean(expression: '0 4 * * *', defaultKeepDay: 30)]
#[ORM\Entity(repositoryClass: LoginLogRepository::class)]
#[ORM\Table(name: 'device_login_log', options:["comment" => "登录日志"])]
class LoginLog implements \Stringable
{
    use CreateTimeAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[ORM\Column(length: 39, nullable: true, options: ['comment' => '登录IP'])]
    private ?string $loginIp = null;

    #[ORM\Column(name: 'login_platform', length: 128, nullable: true, enumType: Platform::class, options: ['comment' => '登录平台'])]
    private ?Platform $platform = null;

    #[ORM\Column(name: 'login_imei', length: 128, nullable: true, options: ['comment' => '登录IMEI'])]
    private ?string $imei = null;

    #[ORM\Column(name: 'login_channel', length: 128, nullable: true, options: ['comment' => '登录渠道'])]
    private ?string $channel = null;

    #[ORM\Column(name: 'systemVersion', length: 512, nullable: true, options: ['comment' => '系统版本'])]
    private ?string $systemVersion = null;

    #[ORM\Column(length: 32, nullable: true, options: ['comment' => 'APP版本'])]
    private ?string $version = null;

    #[ORM\Column(name: 'ip_City', length: 128, nullable: true, options: ['comment' => '地区'])]
    private ?string $ipCity = null;

    #[ORM\Column(length: 128, nullable: true, options: ['comment' => 'IP位置'])]
    private ?string $ipLocation = null;

    #[ORM\Column(length: 256, nullable: true, options: ['comment' => '设备型号'])]
    private ?string $phoneModel = null;

    #[ORM\Column(name: 'netType', length: 128, nullable: true, options: ['comment' => '网络类型'])]
    private ?string $netType = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return "LoginLog #{$this->id}";
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getLoginIp(): ?string
    {
        return $this->loginIp;
    }

    public function setLoginIp(?string $loginIp): static
    {
        $this->loginIp = $loginIp;

        return $this;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function setImei(?string $imei): static
    {
        $this->imei = $imei;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getSystemVersion(): ?string
    {
        return $this->systemVersion;
    }

    public function setSystemVersion(?string $systemVersion): static
    {
        $this->systemVersion = $systemVersion;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getIpCity(): ?string
    {
        return $this->ipCity;
    }

    public function setIpCity(?string $ipCity): static
    {
        $this->ipCity = $ipCity;

        return $this;
    }

    public function getIpLocation(): ?string
    {
        return $this->ipLocation;
    }

    public function setIpLocation(?string $ipLocation): static
    {
        $this->ipLocation = $ipLocation;

        return $this;
    }

    public function getPhoneModel(): ?string
    {
        return $this->phoneModel;
    }

    public function setPhoneModel(?string $phoneModel): static
    {
        $this->phoneModel = $phoneModel;

        return $this;
    }

    public function getNetType(): ?string
    {
        return $this->netType;
    }

    public function setNetType(?string $netType): static
    {
        $this->netType = $netType;

        return $this;
    }

}
