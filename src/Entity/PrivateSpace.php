<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PrivateSpaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant un espace privé dans un coliving
#[ORM\Entity(repositoryClass: PrivateSpaceRepository::class)]
#[ApiResource]
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

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'privateSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'privateSpace')]
    private Collection $reservations;

    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'privateSpace')]
    private Collection $photos;

    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'privateSpace')]
    private Collection $verificationSpaces;

    // Commodités associées à cet espace
    #[ORM\ManyToMany(targetEntity: Amenity::class, inversedBy: 'privateSpaces')]
    private Collection $amenities;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->amenities = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitlePrivateSpace(): ?string { return $this->titlePrivateSpace; }
    public function setTitlePrivateSpace(string $titlePrivateSpace): static {
        $this->titlePrivateSpace = $titlePrivateSpace;
        return $this;
    }

    public function getDescriptionPrivateSpace(): ?string { return $this->descriptionPrivateSpace; }
    public function setDescriptionPrivateSpace(string $descriptionPrivateSpace): static {
        $this->descriptionPrivateSpace = $descriptionPrivateSpace;
        return $this;
    }

    public function getCapacity(): ?int { return $this->capacity; }
    public function setCapacity(int $capacity): static {
        $this->capacity = $capacity;
        return $this;
    }

    public function getAreaM2(): ?string { return $this->areaM2; }
    public function setAreaM2(string $areaM2): static {
        $this->areaM2 = $areaM2;
        return $this;
    }

    public function getPricePerMonth(): ?string { return $this->pricePerMonth; }
    public function setPricePerMonth(string $pricePerMonth): static {
        $this->pricePerMonth = $pricePerMonth;
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

    public function getColivingSpace(): ?ColivingSpace { return $this->colivingSpace; }
    public function setColivingSpace(?ColivingSpace $colivingSpace): static {
        $this->colivingSpace = $colivingSpace;
        return $this;
    }

    public function getReservations(): Collection { return $this->reservations; }
    public function getPhotos(): Collection { return $this->photos; }
    public function getVerificationSpaces(): Collection { return $this->verificationSpaces; }

    public function getAmenities(): Collection { return $this->amenities; }
    public function addAmenity(Amenity $amenity): static {
        if (!$this->amenities->contains($amenity)) {
            $this->amenities->add($amenity);
            $amenity->addPrivateSpace($this);
        }
        return $this;
    }
    public function removeAmenity(Amenity $amenity): static {
        if ($this->amenities->removeElement($amenity)) {
            $amenity->removePrivateSpace($this);
        }
        return $this;
    }
}
