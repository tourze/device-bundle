<?php

namespace DeviceBundle\Entity;

use DeviceBundle\Repository\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
#[ORM\Table(name: 'ims_device', options: ['comment' => '登录设备'])]
class Device implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[TrackColumn]
    #[ORM\Column(length: 120, unique: true, options: ['comment' => '唯一编码'])]
    private string $code;

    #[TrackColumn]
    #[ORM\Column(length: 200, options: ['comment' => '设备型号'])]
    private ?string $model = null;

    #[TrackColumn]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '设备名称'])]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: UserInterface::class, fetch: 'EXTRA_LAZY')]
    private Collection $users;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效'])]
    private ?bool $valid = false;

    #[ORM\Column(length: 45, nullable: true, options: ['comment' => '注册IP'])]
    private ?string $regIp = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->getId() === null) {
            return '';
        }

        return "{$this->getCode()} | {$this->getName()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $deviceModel): static
    {
        $this->model = $deviceModel;

        return $this;
    }

    /**
     * @return Collection<int, UserInterface>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserInterface $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(UserInterface $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getRegIp(): ?string
    {
        return $this->regIp;
    }

    public function setRegIp(?string $regIp): static
    {
        $this->regIp = $regIp;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }#[ListColumn(title: '用户数')]
    public function getUserCount(): int
    {
        return $this->getUsers()->count();
    }
}
