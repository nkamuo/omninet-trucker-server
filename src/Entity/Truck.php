<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TruckRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TruckRepository::class)]
#[ORM\Table(name: 'trucks')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Post(security: "is_granted('ROLE_TRUCK_OWNER')"),
        new Put(security: "is_granted('ROLE_TRUCK_OWNER') and object.getOwner() == user"),
        new Delete(security: "is_granted('ROLE_TRUCK_OWNER') and object.getOwner() == user")
    ],
    normalizationContext: ['groups' => ['truck:read']],
    denormalizationContext: ['groups' => ['truck:write']]
)]
class Truck
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[Groups(['truck:read', 'booking:read'])]
    private ?Ulid $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Groups(['truck:read', 'truck:write', 'booking:read'])]
    #[Assert\NotBlank]
    private ?string $truckNumber = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Groups(['truck:read', 'truck:write'])]
    #[Assert\NotBlank]
    private ?string $licensePlate = null;

    #[ORM\Column(type: 'string', length: 17, unique: true)]
    #[Groups(['truck:read', 'truck:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(exactly: 17)]
    private ?string $vin = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['truck:read', 'truck:write', 'booking:read'])]
    #[Assert\NotBlank]
    private ?string $make = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['truck:read', 'truck:write', 'booking:read'])]
    #[Assert\NotBlank]
    private ?string $model = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['truck:read', 'truck:write', 'booking:read'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1900, max: 2100)]
    private ?int $year = null;

    #[ORM\Column(type: 'string', length: 30)]
    #[Groups(['truck:read', 'truck:write'])]
    #[Assert\NotBlank]
    private ?string $color = null;

    #[ORM\Column(type: 'string', length: 20, enumType: TruckType::class)]
    #[Groups(['truck:read', 'truck:write', 'booking:read'])]
    private TruckType $truckType = TruckType::SEMI_TRUCK;

    #[ORM\Column(type: 'string', length: 20, enumType: FuelType::class)]
    #[Groups(['truck:read', 'truck:write'])]
    private FuelType $fuelType = FuelType::DIESEL;

    #[ORM\Column(type: 'string', length: 20, enumType: TransmissionType::class)]
    #[Groups(['truck:read', 'truck:write'])]
    private TransmissionType $transmissionType = TransmissionType::MANUAL;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['truck:read', 'truck:write'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $dailyRate = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['truck:read', 'truck:write'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?int $odometer = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?string $fuelCapacity = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['truck:read', 'truck:write'])]
    #[Assert\Positive]
    private ?int $maxPayload = null;

    #[ORM\Column(type: 'string', length: 20, enumType: TruckStatus::class)]
    #[Groups(['truck:read', 'truck:write'])]
    private TruckStatus $status = TruckStatus::AVAILABLE;

    #[ORM\Column(type: 'string', length: 20, enumType: TruckCondition::class)]
    #[Groups(['truck:read', 'truck:write'])]
    private TruckCondition $condition = TruckCondition::GOOD;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?\DateTimeInterface $lastInspectionDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?\DateTimeInterface $nextInspectionDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?\DateTimeInterface $insuranceExpiryDate = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?\DateTimeInterface $registrationExpiryDate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?string $location = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?array $specifications = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['truck:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['truck:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    // Relationships
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'trucks')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['truck:read', 'truck:write'])]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedTrucks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['truck:read'])]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'truck', targetEntity: TruckImage::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['truck:read'])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'truck', targetEntity: TruckDocument::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['truck:read'])]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'truck', targetEntity: Booking::class)]
    private Collection $bookings;

    public function __construct()
    {
        $this->id = new Ulid();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getTruckNumber(): ?string
    {
        return $this->truckNumber;
    }

    public function setTruckNumber(string $truckNumber): static
    {
        $this->truckNumber = $truckNumber;
        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): static
    {
        $this->licensePlate = $licensePlate;
        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(string $vin): static
    {
        $this->vin = $vin;
        return $this;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function setMake(string $make): static
    {
        $this->make = $make;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getTruckType(): TruckType
    {
        return $this->truckType;
    }

    public function setTruckType(TruckType $truckType): static
    {
        $this->truckType = $truckType;
        return $this;
    }

    public function getFuelType(): FuelType
    {
        return $this->fuelType;
    }

    public function setFuelType(FuelType $fuelType): static
    {
        $this->fuelType = $fuelType;
        return $this;
    }

    public function getTransmissionType(): TransmissionType
    {
        return $this->transmissionType;
    }

    public function setTransmissionType(TransmissionType $transmissionType): static
    {
        $this->transmissionType = $transmissionType;
        return $this;
    }

    public function getDailyRate(): ?string
    {
        return $this->dailyRate;
    }

    public function setDailyRate(string $dailyRate): static
    {
        $this->dailyRate = $dailyRate;
        return $this;
    }

    public function getOdometer(): ?int
    {
        return $this->odometer;
    }

    public function setOdometer(int $odometer): static
    {
        $this->odometer = $odometer;
        return $this;
    }

    public function getFuelCapacity(): ?string
    {
        return $this->fuelCapacity;
    }

    public function setFuelCapacity(string $fuelCapacity): static
    {
        $this->fuelCapacity = $fuelCapacity;
        return $this;
    }

    public function getMaxPayload(): ?int
    {
        return $this->maxPayload;
    }

    public function setMaxPayload(int $maxPayload): static
    {
        $this->maxPayload = $maxPayload;
        return $this;
    }

    public function getStatus(): TruckStatus
    {
        return $this->status;
    }

    public function setStatus(TruckStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCondition(): TruckCondition
    {
        return $this->condition;
    }

    public function setCondition(TruckCondition $condition): static
    {
        $this->condition = $condition;
        return $this;
    }

    public function getLastInspectionDate(): ?\DateTimeInterface
    {
        return $this->lastInspectionDate;
    }

    public function setLastInspectionDate(?\DateTimeInterface $lastInspectionDate): static
    {
        $this->lastInspectionDate = $lastInspectionDate;
        return $this;
    }

    public function getNextInspectionDate(): ?\DateTimeInterface
    {
        return $this->nextInspectionDate;
    }

    public function setNextInspectionDate(?\DateTimeInterface $nextInspectionDate): static
    {
        $this->nextInspectionDate = $nextInspectionDate;
        return $this;
    }

    public function getInsuranceExpiryDate(): ?\DateTimeInterface
    {
        return $this->insuranceExpiryDate;
    }

    public function setInsuranceExpiryDate(?\DateTimeInterface $insuranceExpiryDate): static
    {
        $this->insuranceExpiryDate = $insuranceExpiryDate;
        return $this;
    }

    public function getRegistrationExpiryDate(): ?\DateTimeInterface
    {
        return $this->registrationExpiryDate;
    }

    public function setRegistrationExpiryDate(?\DateTimeInterface $registrationExpiryDate): static
    {
        $this->registrationExpiryDate = $registrationExpiryDate;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getSpecifications(): ?array
    {
        return $this->specifications;
    }

    public function setSpecifications(?array $specifications): static
    {
        $this->specifications = $specifications;
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, TruckImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(TruckImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setTruck($this);
        }

        return $this;
    }

    public function removeImage(TruckImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getTruck() === $this) {
                $image->setTruck(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TruckDocument>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(TruckDocument $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setTruck($this);
        }

        return $this;
    }

    public function removeDocument(TruckDocument $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getTruck() === $this) {
                $document->setTruck(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function getDisplayName(): string
    {
        return sprintf('%s - %s %s %d', $this->truckNumber, $this->make, $this->model, $this->year);
    }

    public function isAvailableForDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        if ($this->status !== TruckStatus::AVAILABLE) {
            return false;
        }

        foreach ($this->bookings as $booking) {
            if ($booking->getStatus() === BookingStatus::CONFIRMED ||
                $booking->getStatus() === BookingStatus::PENDING) {
                // Check for date overlap
                if ($startDate <= $booking->getEndDate() && $endDate >= $booking->getStartDate()) {
                    return false;
                }
            }
        }

        return true;
    }
}

enum TruckType: string
{
    case SEMI_TRUCK = 'semi_truck';
    case BOX_TRUCK = 'box_truck';
    case FLATBED = 'flatbed';
    case TANKER = 'tanker';
    case REFRIGERATED = 'refrigerated';
    case DUMP_TRUCK = 'dump_truck';
    case TOW_TRUCK = 'tow_truck';
    case DELIVERY_VAN = 'delivery_van';
}

enum FuelType: string
{
    case DIESEL = 'diesel';
    case GASOLINE = 'gasoline';
    case ELECTRIC = 'electric';
    case HYBRID = 'hybrid';
    case CNG = 'cng';
    case LNG = 'lng';
}

enum TransmissionType: string
{
    case MANUAL = 'manual';
    case AUTOMATIC = 'automatic';
    case AMT = 'amt';
}

enum TruckStatus: string
{
    case AVAILABLE = 'available';
    case RENTED = 'rented';
    case IN_MAINTENANCE = 'in_maintenance';
    case OUT_OF_SERVICE = 'out_of_service';
    case RETIRED = 'retired';
}

enum TruckCondition: string
{
    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';
    case NEEDS_REPAIR = 'needs_repair';
}
