<?php

namespace App\Entity;

use App\Repository\ColivingSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité Doctrine liée à la table 'coliving_space'
#[ORM\Entity(repositoryClass: ColivingSpaceRepository::class)]
#[ORM\Table(name: 'coliving_space')]
class ColivingSpace
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Titre du coliving (ex: "Maison partagée Lyon")
    #[ORM\Column(length: 100)]
    private ?string $titleColivingSpace = null;

    // Description complète du coliving
    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionColivingSpace = null;

    // Type de logement (ex: appartement, maison)
    #[ORM\Column(length: 50)]
    private ?string $housingType = null;

    // Nombre total de pièces
    #[ORM\Column]
    private ?int $roomCount = null;

    // Surface totale en m²
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $totalAreaM2 = null;

    // Capacité maximale en nombre de personnes
    #[ORM\Column]
    private ?int $capacityMax = null;

    // Date de création de l'enregistrement
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Date de mise à jour (optionnelle)
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Statut actif ou non (true par défaut)
    #[ORM\Column(options: ['default' => true])]
    private ?bool $isActive = true;

    // Liste des espaces privés liés à ce coliving (relation OneToMany)
    #[ORM\OneToMany(targetEntity: PrivateSpace::class, mappedBy: 'colivingSpace')]
    private Collection $privateSpaces;

    // Adresse associée au coliving (relation ManyToOne)
    #[ORM\ManyToOne(inversedBy: 'colivingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    // Propriétaire du coliving (relation ManyToOne)
    #[ORM\ManyToOne(inversedBy: 'colivingSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    // Photos liées au coliving (relation OneToMany)
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'colivingSpace', orphanRemoval: true)]
    private Collection $photos;

    // Vérifications liées au coliving (relation OneToMany)
    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'colivingSpace', orphanRemoval: true)]
    private Collection $verificationSpaces;

    // Commodités associées à ce coliving (relation ManyToMany inversée)
    #[ORM\ManyToMany(targetEntity: Amenity::class, mappedBy: 'colivingSpaces')]
    private Collection $amenities;

    // Constructeur : initialise toutes les collections
    public function __construct()
    {
        $this->privateSpaces = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->amenities = new ArrayCollection();
    }

    // Getters et setters pour chaque propriété

    public function getId(): ?int { return $this->id; }

    public function getTitleColivingSpace(): ?string { return $this->titleColivingSpace; }
    public function setTitleColivingSpace(string $title): static {
        $this->titleColivingSpace = $title;
        return $this;
    }

    public function getDescriptionColivingSpace(): ?string { return $this->descriptionColivingSpace; }
    public function setDescriptionColivingSpace(string $description): static {
        $this->descriptionColivingSpace = $description;
        return $this;
    }

    public function getHousingType(): ?string { return $this->housingType; }
    public function setHousingType(string $type): static {
        $this->housingType = $type;
        return $this;
    }

    public function getRoomCount(): ?int { return $this->roomCount; }
    public function setRoomCount(int $count): static {
        $this->roomCount = $count;
        return $this;
    }

    public function getTotalAreaM2(): ?string { return $this->totalAreaM2; }
    public function setTotalAreaM2(string $area): static {
        $this->totalAreaM2 = $area;
        return $this;
    }

    public function getCapacityMax(): ?int { return $this->capacityMax; }
    public function setCapacityMax(int $capacity): static {
        $this->capacityMax = $capacity;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getIsActive(): ?bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static {
        $this->isActive = $isActive;
        return $this;
    }

    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $address): static {
        $this->address = $address;
        return $this;
    }

    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): static {
        $this->owner = $owner;
        return $this;
    }

    /** Retourne les espaces privés liés à ce coliving */
    public function getPrivateSpaces(): Collection { return $this->privateSpaces; }

    /** Retourne les photos liées à ce coliving */
    public function getPhotos(): Collection { return $this->photos; }

    /** Retourne les vérifications liées à ce coliving */
    public function getVerificationSpaces(): Collection { return $this->verificationSpaces; }

    /** Retourne les commodités liées à ce coliving */
    public function getAmenities(): Collection { return $this->amenities; }

    /** Ajoute une commodité à ce coliving et synchronise l’autre côté de la relation */
    public function addAmenity(Amenity $amenity): static {
        if (!$this->amenities->contains($amenity)) {
            $this->amenities->add($amenity);
            $amenity->addColivingSpace($this); // synchronisation bidirectionnelle
        }
        return $this;
    }

    /** Retire une commodité de ce coliving et synchronise l’autre côté de la relation */
    public function removeAmenity(Amenity $amenity): static {
        if ($this->amenities->removeElement($amenity)) {
            $amenity->removeColivingSpace($this); // synchronisation bidirectionnelle
        }
        return $this;
    }
}
