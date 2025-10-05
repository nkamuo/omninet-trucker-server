<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TruckImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TruckImageRepository::class)]
#[ORM\Table(name: 'truck_images')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Post(security: "is_granted('ROLE_TRUCK_OWNER')"),
        new Put(security: "is_granted('ROLE_TRUCK_OWNER') and object.getTruck().getOwner() == user"),
        new Delete(security: "is_granted('ROLE_TRUCK_OWNER') and object.getTruck().getOwner() == user")
    ],
    normalizationContext: ['groups' => ['truck_image:read']],
    denormalizationContext: ['groups' => ['truck_image:write']]
)]
class TruckImage
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[Groups(['truck_image:read', 'truck:read'])]
    private ?Ulid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    #[Assert\NotBlank]
    private ?string $fileName = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    #[Assert\NotBlank]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    private ?int $fileSize = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    #[Assert\NotBlank]
    private int $displayOrder = 0;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    private bool $isPrimary = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['truck_image:read', 'truck_image:write', 'truck:read'])]
    private ?string $caption = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['truck_image:read', 'truck:read'])]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne(targetEntity: Truck::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['truck_image:read', 'truck_image:write'])]
    private ?Truck $truck = null;

    public function __construct()
    {
        $this->id = new Ulid();
        $this->uploadedAt = new \DateTimeImmutable();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): static
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getDisplayOrder(): int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;
        return $this;
    }

    public function getIsPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): static
    {
        $this->caption = $caption;
        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function getTruck(): ?Truck
    {
        return $this->truck;
    }

    public function setTruck(?Truck $truck): static
    {
        $this->truck = $truck;
        return $this;
    }
}
