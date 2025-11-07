<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ColivingSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Ajout des contrôleurs personnalisés
use App\Controller\PublishColivingSpaceController;
use App\Controller\SuspendColivingSpaceController;

/*Entité principale représentant un espace de coliving*/
#[ORM\Entity(repositoryClass: ColivingSpaceRepository::class)]
#[ORM\Table(name: 'coliving_space')]
#[ApiResource(
    operations: [
        // Lecture publique
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Get(security: "is_granted('PUBLIC_ACCESS')"),

        // Création : propriétaire uniquement
        new Post(
            security: "is_granted('ROLE_OWNER')",
            securityMessage: "Seuls les propriétaires peuvent créer un espace de coliving."
        ),

        // Modification : uniquement propriétaire et seulement si non publié
        new Put(
            security: "is_granted('ROLE_OWNER') and object.getOwner() == user and object.getIsActive() == false",
            securityMessage: "Vous pouvez modifier uniquement vos propres espaces non publiés."
        ),
        new Patch(
            security: "is_granted('ROLE_OWNER') and object.getOwner() == user and object.getIsActive() == false",
            securityMessage: "Vous pouvez modifier uniquement vos propres espaces non publiés."
        ),

        // Publication (employé/admin)
        new Patch(
            uriTemplate: '/coliving_spaces/{id}/publish',
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent publier un espace.",
            input: false,
            output: false,
            controller: PublishColivingSpaceController::class
        ),

        // Suspension (employé/admin)
        new Patch(
            uriTemplate: '/coliving_spaces/{id}/suspend',
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent suspendre un espace.",
            input: false,
            output: false,
            controller: SuspendColivingSpaceController::class
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'titleColivingSpace' => 'ipartial',
    'colivingCity.name' => 'ipartial',
    'housingType' => 'ipartial',
    'owner.email' => 'iexact',
    'isActive' => 'exact'
])]
class ColivingSpace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $titleColivingSpace = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionColivingSpace = null;

    #[ORM\Column(length: 50)]
    private ?string $housingType = null;

    #[ORM\Column]
    private ?int $roomCount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $totalAreaM2 = null;

    #[ORM\Column]
    private ?int $capacityMax = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isActive = true;

    #[ORM\ManyToOne(inversedBy: 'colivingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'colivingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\ManyToOne(targetEntity: ColivingCity::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingCity $colivingCity = null;

    #[ORM\OneToMany(targetEntity: PrivateSpace::class, mappedBy: 'colivingSpace')]
    private Collection $privateSpaces;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'colivingSpace', orphanRemoval: true)]
    private Collection $photos;

    // ✅ Correction ici : on doit utiliser "inversedBy" (et non mappedBy)
    #[ORM\ManyToMany(targetEntity: Amenity::class, inversedBy: 'colivingSpaces')]
    private Collection $amenities;

    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'colivingSpace', orphanRemoval: true)]
    private Collection $verificationSpaces;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->privateSpaces = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->amenities = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
    }

    // === Getters & Setters ===

    public function getId(): ?int { return $this->id; }

    public function getTitleColivingSpace(): ?string { return $this->titleColivingSpace; }
    public function setTitleColivingSpace(string $title): static { $this->titleColivingSpace = $title; return $this; }

    public function getDescriptionColivingSpace(): ?string { return $this->descriptionColivingSpace; }
    public function setDescriptionColivingSpace(string $description): static { $this->descriptionColivingSpace = $description; return $this; }

    public function getHousingType(): ?string { return $this->housingType; }
    public function setHousingType(string $type): static { $this->housingType = $type; return $this; }

    public function getRoomCount(): ?int { return $this->roomCount; }
    public function setRoomCount(int $count): static { $this->roomCount = $count; return $this; }

    public function getTotalAreaM2(): ?string { return $this->totalAreaM2; }
    public function setTotalAreaM2(string $area): static { $this->totalAreaM2 = $area; return $this; }

    public function getCapacityMax(): ?int { return $this->capacityMax; }
    public function setCapacityMax(int $capacity): static { $this->capacityMax = $capacity; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getIsActive(): ?bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): static { $this->owner = $owner; return $this; }

    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $address): static { $this->address = $address; return $this; }

    public function getColivingCity(): ?ColivingCity { return $this->colivingCity; }
    public function setColivingCity(?ColivingCity $colivingCity): static { $this->colivingCity = $colivingCity; return $this; }

    /** @return Collection<int, Amenity> */
    public function getAmenities(): Collection { return $this->amenities; }

    public function addAmenity(Amenity $amenity): static
    {
        if (!$this->amenities->contains($amenity)) {
            $this->amenities->add($amenity);
            $amenity->addColivingSpace($this);
        }
        return $this;
    }

    public function removeAmenity(Amenity $amenity): static
    {
        if ($this->amenities->removeElement($amenity)) {
            $amenity->removeColivingSpace($this);
        }
        return $this;
    }
}
