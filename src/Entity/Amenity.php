<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AmenityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant une commodité (équipement ou service)
#[ORM\Entity(repositoryClass: AmenityRepository::class)]
#[ApiResource]
#[ORM\Table(name: 'amenity')]
class Amenity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom de la commodité (ex: Wi-Fi, Parking)
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Type de commodité (ex: service, équipement)
    #[ORM\Column(length: 50)]
    private ?string $amenityType = null;

    // Description optionnelle
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // URL de l’icône (optionnelle)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iconUrl = null;

    // Commodités liées aux espaces privés
    #[ORM\ManyToMany(targetEntity: PrivateSpace::class, mappedBy: 'amenities')]
    private Collection $privateSpaces;

    // Commodités liées aux espaces coliving
    #[ORM\ManyToMany(targetEntity: ColivingSpace::class, inversedBy: 'amenities')]
    #[ORM\JoinTable(name: 'coliving_space_amenity')]
    private Collection $colivingSpaces;

    public function __construct()
    {
        $this->privateSpaces = new ArrayCollection();
        $this->colivingSpaces = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static {
        $this->name = $name;
        return $this;
    }

    public function getAmenityType(): ?string { return $this->amenityType; }
    public function setAmenityType(string $amenityType): static {
        $this->amenityType = $amenityType;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static {
        $this->description = $description;
        return $this;
    }

    public function getIconUrl(): ?string { return $this->iconUrl; }
    public function setIconUrl(?string $iconUrl): static {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    /** @return Collection<int, PrivateSpace> */
    public function getPrivateSpaces(): Collection {
        return $this->privateSpaces;
    }

    public function addPrivateSpace(PrivateSpace $privateSpace): static {
        if (!$this->privateSpaces->contains($privateSpace)) {
            $this->privateSpaces->add($privateSpace);
            $privateSpace->addAmenity($this); // synchronisation bidirectionnelle
        }
        return $this;
    }

    public function removePrivateSpace(PrivateSpace $privateSpace): static {
        if ($this->privateSpaces->removeElement($privateSpace)) {
            $privateSpace->removeAmenity($this); // synchronisation bidirectionnelle
        }
        return $this;
    }

    /** @return Collection<int, ColivingSpace> */
    public function getColivingSpaces(): Collection {
        return $this->colivingSpaces;
    }

    public function addColivingSpace(ColivingSpace $colivingSpace): static {
        if (!$this->colivingSpaces->contains($colivingSpace)) {
            $this->colivingSpaces->add($colivingSpace);
            $colivingSpace->addAmenity($this); // synchronisation bidirectionnelle
        }
        return $this;
    }

    public function removeColivingSpace(ColivingSpace $colivingSpace): static {
        if ($this->colivingSpaces->removeElement($colivingSpace)) {
            $colivingSpace->removeAmenity($this); // synchronisation bidirectionnelle
        }
        return $this;
    }
}
