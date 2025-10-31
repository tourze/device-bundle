<?php

namespace DeviceBundle\Entity;

use DeviceBundle\Enum\DeviceStatus;
use DeviceBundle\Enum\DeviceType;
use DeviceBundle\Repository\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
#[ORM\Table(name: 'ims_device', options: ['comment' => '登录设备'])]
class Device implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[TrackColumn]
    #[ORM\Column(length: 120, unique: true, options: ['comment' => '唯一编码'])]
    private string $code;

    #[Assert\Length(max: 200)]
    #[TrackColumn]
    #[ORM\Column(length: 200, nullable: true, options: ['comment' => '设备型号'])]
    private ?string $model = null;

    #[Assert\Length(max: 100)]
    #[TrackColumn]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '设备名称'])]
    private ?string $name = null;

    /** @var Collection<int, UserInterface> */
    #[ORM\ManyToMany(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    private Collection $users;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn(name: 'device_idx_valid')]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效'])]
    private ?bool $valid = false;

    #[Assert\Length(max: 45)]
    #[Assert\Ip]
    #[ORM\Column(length: 45, nullable: true, options: ['comment' => '注册IP'])]
    private ?string $regIp = null;

    #[Assert\Choice(callback: [DeviceType::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, enumType: DeviceType::class, options: ['comment' => '设备类型'])]
    private ?DeviceType $deviceType = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '操作系统版本'])]
    private ?string $osVersion = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '设备品牌'])]
    private ?string $brand = null;

    #[Assert\Choice(callback: [DeviceStatus::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, enumType: DeviceStatus::class, options: ['comment' => '设备状态', 'default' => 'offline'])]
    private DeviceStatus $status = DeviceStatus::OFFLINE;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后在线时间'])]
    private ?\DateTimeImmutable $lastOnlineTime = null;

    #[Assert\Length(max: 45)]
    #[Assert\Ip]
    #[ORM\Column(type: Types::STRING, length: 45, nullable: true, options: ['comment' => '最后连接IP'])]
    private ?string $lastIp = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '设备指纹信息'])]
    private ?string $fingerprint = null;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'CPU核心数', 'default' => 0])]
    private int $cpuCores = 0;

    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: '/^\d+$/')]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '内存大小(MB)', 'default' => 0])]
    private string $memorySize = '0';

    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: '/^\d+$/')]
    #[ORM\Column(type: Types::BIGINT, options: ['comment' => '存储空间大小(MB)', 'default' => 0])]
    private string $storageSize = '0';

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注信息'])]
    private ?string $remark = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getCode()} | {$this->getName()}";
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $deviceModel): void
    {
        $this->model = $deviceModel;
    }

    /**
     * @return Collection<int, UserInterface>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserInterface $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    public function removeUser(UserInterface $user): void
    {
        $this->users->removeElement($user);
    }

    public function getRegIp(): ?string
    {
        return $this->regIp;
    }

    public function setRegIp(?string $regIp): void
    {
        $this->regIp = $regIp;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getUserCount(): int
    {
        return $this->getUsers()->count();
    }

    public function getDeviceType(): ?DeviceType
    {
        return $this->deviceType;
    }

    public function setDeviceType(?DeviceType $deviceType): void
    {
        $this->deviceType = $deviceType;
    }

    public function getOsVersion(): ?string
    {
        return $this->osVersion;
    }

    public function setOsVersion(?string $osVersion): void
    {
        $this->osVersion = $osVersion;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getStatus(): DeviceStatus
    {
        return $this->status;
    }

    public function setStatus(DeviceStatus $status): void
    {
        $this->status = $status;
    }

    public function getLastOnlineTime(): ?\DateTimeImmutable
    {
        return $this->lastOnlineTime;
    }

    public function setLastOnlineTime(?\DateTimeImmutable $lastOnlineTime): void
    {
        $this->lastOnlineTime = $lastOnlineTime;
    }

    public function getLastIp(): ?string
    {
        return $this->lastIp;
    }

    public function setLastIp(?string $lastIp): void
    {
        $this->lastIp = $lastIp;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    public function getCpuCores(): int
    {
        return $this->cpuCores;
    }

    public function setCpuCores(int $cpuCores): void
    {
        $this->cpuCores = $cpuCores;
    }

    public function getMemorySize(): string
    {
        return $this->memorySize;
    }

    public function setMemorySize(string $memorySize): void
    {
        $this->memorySize = $memorySize;
    }

    public function getStorageSize(): string
    {
        return $this->storageSize;
    }

    public function setStorageSize(string $storageSize): void
    {
        $this->storageSize = $storageSize;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * 检查设备是否在线
     */
    public function isOnline(): bool
    {
        return $this->status->isActive();
    }

    /**
     * 检查设备是否启用
     */
    public function isEnabled(): bool
    {
        return $this->status->isEnabled() && true === $this->valid;
    }
}
