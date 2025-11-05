<?php

namespace DeviceBundle\Entity;

use DeviceBundle\Repository\LoginLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\OperationSystemEnum\Platform;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;

/**
 * 这个本质是设备登录记录
 */
#[AsScheduleClean(expression: '0 4 * * *', defaultKeepDay: 30)]
#[ORM\Entity(repositoryClass: LoginLogRepository::class)]
#[ORM\Table(name: 'device_login_log', options: ['comment' => '登录日志'])]
class LoginLog implements \Stringable
{
    use CreateTimeAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[Assert\Length(max: 39)]
    #[Assert\Ip]
    #[ORM\Column(length: 39, nullable: true, options: ['comment' => '登录IP'])]
    private ?string $loginIp = null;

    #[Assert\Choice(callback: [Platform::class, 'cases'])]
    #[ORM\Column(name: 'login_platform', length: 128, nullable: true, enumType: Platform::class, options: ['comment' => '登录平台'])]
    private ?Platform $platform = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'login_imei', length: 128, nullable: true, options: ['comment' => '登录IMEI'])]
    private ?string $imei = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'login_channel', length: 128, nullable: true, options: ['comment' => '登录渠道'])]
    private ?string $channel = null;

    #[Assert\Length(max: 512)]
    #[ORM\Column(name: 'systemVersion', length: 512, nullable: true, options: ['comment' => '系统版本'])]
    private ?string $systemVersion = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, nullable: true, options: ['comment' => 'APP版本'])]
    private ?string $version = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'ip_City', length: 128, nullable: true, options: ['comment' => '地区'])]
    private ?string $ipCity = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => 'IP位置'])]
    private ?string $ipLocation = null;

    #[Assert\Length(max: 256)]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9\s\-_\.\/\(\)\+]+$/', message: '设备型号格式不正确')]
    #[ORM\Column(length: 256, nullable: true, options: ['comment' => '设备型号'])]
    private ?string $deviceModel = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'netType', length: 128, nullable: true, options: ['comment' => '网络类型'])]
    private ?string $netType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return 'LoginLog #' . ($this->id ?? 'new');
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getLoginIp(): ?string
    {
        return $this->loginIp;
    }

    public function setLoginIp(?string $loginIp): void
    {
        $this->loginIp = $loginIp;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): void
    {
        $this->platform = $platform;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function setImei(?string $imei): void
    {
        $this->imei = $imei;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): void
    {
        $this->channel = $channel;
    }

    public function getSystemVersion(): ?string
    {
        return $this->systemVersion;
    }

    public function setSystemVersion(?string $systemVersion): void
    {
        $this->systemVersion = $systemVersion;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    public function getIpCity(): ?string
    {
        return $this->ipCity;
    }

    public function setIpCity(?string $ipCity): void
    {
        $this->ipCity = $ipCity;
    }

    public function getIpLocation(): ?string
    {
        return $this->ipLocation;
    }

    public function setIpLocation(?string $ipLocation): void
    {
        $this->ipLocation = $ipLocation;
    }

    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    public function setDeviceModel(?string $deviceModel): void
    {
        $this->deviceModel = $deviceModel;
    }

    public function getNetType(): ?string
    {
        return $this->netType;
    }

    public function setNetType(?string $netType): void
    {
        $this->netType = $netType;
    }
}
