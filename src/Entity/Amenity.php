<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AmenityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Entité représentant un équipement ou un service (lié aux espaces de coliving)
 */
#[ORM\Entity(repositoryClass: AmenityRepository::class)]
#[ORM\Table(name: 'amenity')]
#[ApiResource]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'ipartial',
    'amenityType' => 'exact'
])]
class Amenity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $amenityType = null;

    // === Relations ===

    #[ORM\ManyToMany(targetEntity: ColivingSpace::class, mappedBy: 'amenities')]
    private Collection $colivingSpaces;

    #[ORM\ManyToMany(targetEntity: PrivateSpace::class, mappedBy: 'amenities')]
    private Collection $privateSpaces;

    public function __construct()
    {
        $this->colivingSpaces = new ArrayCollection();
        $this->privateSpaces = new ArrayCollection();
    }

    // === Getters & Setters ===

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getAmenityType(): ?string { return $this->amenityType; }
    public function setAmenityType(string $amenityType): static { $this->amenityType = $amenityType; return $this; }

    /** @return Collection<int, ColivingSpace> */
    public function getColivingSpaces(): Collection { return $this->colivingSpaces; }

    public function addColivingSpace(ColivingSpace $colivingSpace): static
    {
        if (!$this->colivingSpaces->contains($colivingSpace)) {
            $this->colivingSpaces->add($colivingSpace);
            $colivingSpace->addAmenity($this);
        }
        return $this;
    }

    public function removeColivingSpace(ColivingSpace $colivingSpace): static
    {
        if ($this->colivingSpaces->removeElement($colivingSpace)) {
            $colivingSpace->removeAmenity($this);
        }
        return $this;
    }

    /** @return Collection<int, PrivateSpace> */
    public function getPrivateSpaces(): Collection { return $this->privateSpaces; }

    public function addPrivateSpace(PrivateSpace $privateSpace): static
    {
        if (!$this->privateSpaces->contains($privateSpace)) {
            $this->privateSpaces->add($privateSpace);
            $privateSpace->addAmenity($this);
        }
        return $this;
    }

    public function removePrivateSpace(PrivateSpace $privateSpace): static
    {
        if ($this->privateSpaces->removeElement($privateSpace)) {
            $privateSpace->removeAmenity($this);
        }
        return $this;
    }
}
