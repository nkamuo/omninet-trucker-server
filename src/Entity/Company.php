<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Table(name: 'companies')]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_USER')"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_TRUCK_OWNER') and object.getUsers().contains(user))"),
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['company:read']],
    denormalizationContext: ['groups' => ['company:write']]
)]
class Company
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    #[Groups(['company:read', 'user:read', 'truck:read'])]
    private ?Ulid $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['company:read', 'company:write', 'user:read', 'truck:read'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 20, unique: true, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $dotNumber = null;

    #[ORM\Column(type: 'string', length: 20, unique: true, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $mcNumber = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $taxId = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $website = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $logo = null;

    #[ORM\Column(type: 'string', length: 20, enumType: CompanyStatus::class)]
    #[Groups(['company:read', 'company:write'])]
    private CompanyStatus $status = CompanyStatus::ACTIVE;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['company:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['company:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    // Address fields
    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $state = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $zipCode = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups(['company:read', 'company:write'])]
    private ?string $country = null;

    // Relationships
    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Truck::class)]
    private Collection $trucks;

    public function __construct()
    {
        $this->id = new Ulid();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
        $this->trucks = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDotNumber(): ?string
    {
        return $this->dotNumber;
    }

    public function setDotNumber(?string $dotNumber): static
    {
        $this->dotNumber = $dotNumber;
        return $this;
    }

    public function getMcNumber(): ?string
    {
        return $this->mcNumber;
    }

    public function setMcNumber(?string $mcNumber): static
    {
        $this->mcNumber = $mcNumber;
        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): static
    {
        $this->taxId = $taxId;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;
        return $this;
    }

    public function getStatus(): CompanyStatus
    {
        return $this->status;
    }

    public function setStatus(CompanyStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection<int, Truck>
     */
    public function getTrucks(): Collection
    {
        return $this->trucks;
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zipCode,
            $this->country
        ]);

        return implode(', ', $parts);
    }
}

enum CompanyStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}
