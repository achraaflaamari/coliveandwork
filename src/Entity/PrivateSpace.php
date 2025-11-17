<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\PrivateSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Import des contrôleurs personnalisés
use App\Controller\PublishPrivateSpaceController;
use App\Controller\SuspendPrivateSpaceController;
use App\Controller\PrivateSpaceTopController;
use App\Controller\PrivateSpaceCollectionController;

/*Espace privé d’un espace coliving */
#[ORM\Entity(repositoryClass: PrivateSpaceRepository::class)]
#[ORM\Table(name: 'private_space')]
#[ApiResource(
    operations: [
        // Lecture publique - Collection personnalisée avec filtres
        new GetCollection(
            controller: PrivateSpaceCollectionController::class,
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        
        // Top 3 espaces privés les mieux notés
        new GetCollection(
            uriTemplate: '/private_spaces/top',
            controller: PrivateSpaceTopController::class,
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        
        new Get(security: "is_granted('PUBLIC_ACCESS')"),

        // Création : propriétaire uniquement
        new Post(
            security: "is_granted('ROLE_OWNER')",
            securityMessage: "Seuls les propriétaires peuvent créer un espace privé."
        ),

        // Modification : propriétaire seulement si non publié
        new Put(
            security: "is_granted('ROLE_OWNER') and object.getColivingSpace().getOwner() == user and object.getIsActive() == false",
            securityMessage: "Vous pouvez modifier uniquement vos espaces privés non publiés de vos propres colivings."
        ),
        new Patch(
            security: "is_granted('ROLE_OWNER') and object.getColivingSpace().getOwner() == user and object.getIsActive() == false",
            securityMessage: "Vous pouvez modifier uniquement vos espaces privés non publiés de vos propres colivings."
        ),

        // Publication (employé/admin)
        new Patch(
            uriTemplate: '/private_spaces/{id}/publish',
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent publier un espace privé.",
            input: false,
            output: false,
            controller: PublishPrivateSpaceController::class
        ),

        // Suspension (employé/admin)
        new Patch(
            uriTemplate: '/private_spaces/{id}/suspend',
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent suspendre un espace privé.",
            input: false,
            output: false,
            controller: SuspendPrivateSpaceController::class
        ),

        // Suppression : admin uniquement
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les administrateurs peuvent supprimer un espace privé."
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'colivingSpace.id' => 'exact',
    'capacity' => 'exact',
    'pricePerMonth' => 'exact',
    'isActive' => 'exact'
])]
class PrivateSpace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $titlePrivateSpace = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionPrivateSpace = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $areaM2 = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $pricePerMonth = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isActive = false;

    #[ORM\ManyToOne(inversedBy: 'privateSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'privateSpace')]
    private Collection $reservations;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'privateSpace')]
    private Collection $photos;

    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'privateSpace')]
    private Collection $verificationSpaces;

    #[ORM\ManyToMany(targetEntity: Amenity::class, inversedBy: 'privateSpaces')]
    private Collection $amenities;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->amenities = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // === Getters & Setters ===

    public function getId(): ?int { return $this->id; }

    public function getTitlePrivateSpace(): ?string { return $this->titlePrivateSpace; }
    public function setTitlePrivateSpace(string $title): static { $this->titlePrivateSpace = $title; return $this; }

    public function getDescriptionPrivateSpace(): ?string { return $this->descriptionPrivateSpace; }
    public function setDescriptionPrivateSpace(string $description): static { $this->descriptionPrivateSpace = $description; return $this; }

    public function getCapacity(): ?int { return $this->capacity; }
    public function setCapacity(int $capacity): static { $this->capacity = $capacity; return $this; }

    public function getAreaM2(): ?string { return $this->areaM2; }
    public function setAreaM2(string $areaM2): static { $this->areaM2 = $areaM2; return $this; }

    public function getPricePerMonth(): ?string { return $this->pricePerMonth; }
    public function setPricePerMonth(string $pricePerMonth): static { $this->pricePerMonth = $pricePerMonth; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getIsActive(): ?bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getColivingSpace(): ?ColivingSpace { return $this->colivingSpace; }
    public function setColivingSpace(?ColivingSpace $colivingSpace): static { $this->colivingSpace = $colivingSpace; return $this; }

    /** @return Collection<int, Reservation> */
    public function getReservations(): Collection { return $this->reservations; }

    /** @return Collection<int, Photo> */
    public function getPhotos(): Collection { return $this->photos; }

    /** @return Collection<int, VerificationSpace> */
    public function getVerificationSpaces(): Collection { return $this->verificationSpaces; }

    /** @return Collection<int, Amenity> */
    public function getAmenities(): Collection { return $this->amenities; }

    public function addAmenity(Amenity $amenity): static
    {
        if (!$this->amenities->contains($amenity)) {
            $this->amenities->add($amenity);
            $amenity->addPrivateSpace($this);
        }
        return $this;
    }

    public function removeAmenity(Amenity $amenity): static
    {
        if ($this->amenities->removeElement($amenity)) {
            $amenity->removePrivateSpace($this);
        }
        return $this;
    }
}
