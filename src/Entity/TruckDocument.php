<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TruckDocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TruckDocumentRepository::class)]
#[ORM\Table(name: 'truck_documents')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_TRUCK_OWNER')"),
        new Put(security: "is_granted('ROLE_TRUCK_OWNER') and object.getTruck().getOwner() == user"),
        new Delete(security: "is_granted('ROLE_TRUCK_OWNER') and object.getTruck().getOwner() == user")
    ],
    normalizationContext: ['groups' => ['truck_document:read']],
    denormalizationContext: ['groups' => ['truck_document:write']]
)]
class TruckDocument
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[Groups(['truck_document:read', 'truck:read'])]
    private ?Ulid $id = null;

    #[ORM\Column(type: 'string', length: 50, enumType: DocumentType::class)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    #[Assert\NotBlank]
    private DocumentType $documentType;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    #[Assert\NotBlank]
    private ?string $fileName = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    #[Assert\NotBlank]
    private ?string $url = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    private ?int $fileSize = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    private ?\DateTimeInterface $expiryDate = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    private ?string $documentNumber = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['truck_document:read', 'truck_document:write', 'truck:read'])]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['truck_document:read', 'truck:read'])]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne(targetEntity: Truck::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['truck_document:read', 'truck_document:write'])]
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

    public function getDocumentType(): DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(DocumentType $documentType): static
    {
        $this->documentType = $documentType;
        return $this;
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

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): static
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }

    public function getDocumentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber(?string $documentNumber): static
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
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

    public function isExpired(): bool
    {
        if ($this->expiryDate === null) {
            return false;
        }

        return $this->expiryDate < new \DateTime();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if ($this->expiryDate === null) {
            return false;
        }

        $threshold = new \DateTime("+{$days} days");
        return $this->expiryDate <= $threshold && !$this->isExpired();
    }
}

enum DocumentType: string
{
    case INSURANCE = 'insurance';
    case REGISTRATION = 'registration';
    case INSPECTION = 'inspection';
    case PERMIT = 'permit';
    case LEASE_AGREEMENT = 'lease_agreement';
    case PURCHASE_AGREEMENT = 'purchase_agreement';
    case MAINTENANCE_RECORD = 'maintenance_record';
    case DOT_CERTIFICATE = 'dot_certificate';
    case EMISSION_TEST = 'emission_test';
    case OTHER = 'other';
}
