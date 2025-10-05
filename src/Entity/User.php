<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER') and object == user"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        new Post(
            security: "is_granted('PUBLIC_ACCESS')",
            validationContext: ['groups' => ['Default', 'user:create']],
            processor: \App\State\UserPasswordHasher::class
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object == user",
            processor: \App\State\UserPasswordHasher::class
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[Groups(['user:read'])]
    private ?Ulid $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(['user:write'])]
    #[Assert\NotBlank(groups: ['user:create'])]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['user:read', 'user:write', 'truck:read', 'booking:read'])]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['user:read', 'user:write', 'truck:read', 'booking:read'])]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\Length(max: 20)]
    private ?string $phone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $profileImage = null;

    #[ORM\Column(type: 'string', length: 20, enumType: UserRole::class)]
    #[Groups(['user:read', 'user:write'])]
    private UserRole $userRole = UserRole::RENTER;

    #[ORM\Column(type: 'string', length: 20, enumType: UserStatus::class)]
    #[Groups(['user:read', 'user:write'])]
    private UserStatus $status = UserStatus::ACTIVE;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $lastLoginAt = null;

    // Relationships
    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Truck::class)]
    private Collection $ownedTrucks;

    #[ORM\OneToMany(mappedBy: 'renter', targetEntity: Booking::class)]
    private Collection $bookings;

    public function __construct()
    {
        $this->id = new Ulid();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->ownedTrucks = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        // Add role based on userRole enum
        if ($this->userRole === UserRole::TRUCK_OWNER) {
            $roles[] = 'ROLE_TRUCK_OWNER';
        } elseif ($this->userRole === UserRole::RENTER) {
            $roles[] = 'ROLE_RENTER';
        } elseif ($this->userRole === UserRole::ADMIN) {
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data if needed
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;
        return $this;
    }

    public function getUserRole(): UserRole
    {
        return $this->userRole;
    }

    public function setUserRole(UserRole $userRole): static
    {
        $this->userRole = $userRole;
        return $this;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): static
    {
        $this->status = $status;
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

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;
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

    /**
     * @return Collection<int, Truck>
     */
    public function getOwnedTrucks(): Collection
    {
        return $this->ownedTrucks;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }
}

enum UserRole: string
{
    case ADMIN = 'admin';
    case TRUCK_OWNER = 'truck_owner';
    case RENTER = 'renter';
}

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}
