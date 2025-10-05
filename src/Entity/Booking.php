<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'bookings')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER') and (object.getRenter() == user or object.getTruck().getOwner() == user)"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_RENTER')"),
        new Put(security: "is_granted('ROLE_USER') and (object.getRenter() == user or object.getTruck().getOwner() == user)"),
        new Delete(security: "is_granted('ROLE_USER') and (object.getRenter() == user or object.getTruck().getOwner() == user)")
    ],
    normalizationContext: ['groups' => ['booking:read']],
    denormalizationContext: ['groups' => ['booking:write']]
)]
class Booking
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[Groups(['booking:read'])]
    private ?Ulid $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Groups(['booking:read'])]
    private ?string $bookingNumber = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['booking:read', 'booking:write'])]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['booking:read', 'booking:write'])]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(propertyPath: 'startDate')]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['booking:read', 'booking:write'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $totalAmount = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    #[Assert\PositiveOrZero]
    private ?string $depositAmount = null;

    #[ORM\Column(type: 'string', length: 20, enumType: BookingStatus::class)]
    #[Groups(['booking:read', 'booking:write'])]
    private BookingStatus $status = BookingStatus::PENDING;

    #[ORM\Column(type: 'string', length: 20, enumType: PaymentStatus::class)]
    #[Groups(['booking:read', 'booking:write'])]
    private PaymentStatus $paymentStatus = PaymentStatus::PENDING;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $notes = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $pickupLocation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $dropoffLocation = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $purpose = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['booking:read'])]
    private ?\DateTimeImmutable $confirmedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['booking:read'])]
    private ?\DateTimeImmutable $cancelledAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $cancellationReason = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['booking:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['booking:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    // Relationships
    #[ORM\ManyToOne(targetEntity: Truck::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['booking:read', 'booking:write'])]
    #[Assert\NotBlank]
    private ?Truck $truck = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['booking:read'])]
    private ?User $renter = null;

    public function __construct()
    {
        $this->id = new Ulid();
        $this->bookingNumber = 'BK-' . strtoupper(substr((string) $this->id, -10));
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getBookingNumber(): ?string
    {
        return $this->bookingNumber;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getDepositAmount(): ?string
    {
        return $this->depositAmount;
    }

    public function setDepositAmount(?string $depositAmount): static
    {
        $this->depositAmount = $depositAmount;
        return $this;
    }

    public function getStatus(): BookingStatus
    {
        return $this->status;
    }

    public function setStatus(BookingStatus $status): static
    {
        $this->status = $status;

        if ($status === BookingStatus::CONFIRMED && $this->confirmedAt === null) {
            $this->confirmedAt = new \DateTimeImmutable();
        }

        if ($status === BookingStatus::CANCELLED && $this->cancelledAt === null) {
            $this->cancelledAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getPaymentStatus(): PaymentStatus
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(PaymentStatus $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;
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

    public function getPickupLocation(): ?string
    {
        return $this->pickupLocation;
    }

    public function setPickupLocation(?string $pickupLocation): static
    {
        $this->pickupLocation = $pickupLocation;
        return $this;
    }

    public function getDropoffLocation(): ?string
    {
        return $this->dropoffLocation;
    }

    public function setDropoffLocation(?string $dropoffLocation): static
    {
        $this->dropoffLocation = $dropoffLocation;
        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): static
    {
        $this->purpose = $purpose;
        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function getCancelledAt(): ?\DateTimeImmutable
    {
        return $this->cancelledAt;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
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

    public function getRenter(): ?User
    {
        return $this->renter;
    }

    public function setRenter(?User $renter): static
    {
        $this->renter = $renter;
        return $this;
    }

    public function getDurationInDays(): int
    {
        if ($this->startDate === null || $this->endDate === null) {
            return 0;
        }

        return $this->startDate->diff($this->endDate)->days + 1;
    }

    public function isActive(): bool
    {
        return $this->status === BookingStatus::CONFIRMED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [BookingStatus::PENDING, BookingStatus::CONFIRMED], true);
    }
}

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case DEPOSIT_PAID = 'deposit_paid';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';
}
