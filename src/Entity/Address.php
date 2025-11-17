<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité : Address (Adresse)
 * Utilisée par : User + ColivingSpace
 * Lecture publique uniquement
 * Sert à afficher les localisations et filtrer les espaces
 */
#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\Table(name: 'address')]
#[ApiResource(
    operations: [
        // Lecture publique — tout le monde peut consulter les adresses
        new GetCollection(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Get(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'streetName' => 'ipartial',   // recherche partielle sur le nom de rue
    'postalCode' => 'iexact',     // correspondance exacte du code postal
    'otherCityName' => 'ipartial', // nom de la ville (autre)
    'regionName' => 'ipartial',   // recherche partielle sur la région
    'countryName' => 'ipartial'   // recherche partielle sur le pays
])]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $streetName = null;

    #[ORM\Column(length: 20)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $otherCityName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $regionName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $countryName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, nullable: true)]
    private ?string $latitude = null;

    // Espaces coliving associés à cette adresse
    #[ORM\OneToMany(targetEntity: ColivingSpace::class, mappedBy: 'address')]
    private Collection $colivingSpaces;

    // Utilisateurs associés à cette adresse
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'address')]
    private Collection $users;

    public function __construct()
    {
        $this->colivingSpaces = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    // === Getters / Setters ===
    public function getId(): ?int { return $this->id; }

    public function getStreetNumber(): ?string { return $this->streetNumber; }
    public function setStreetNumber(?string $streetNumber): static {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    public function getStreetName(): ?string { return $this->streetName; }
    public function setStreetName(string $streetName): static {
        $this->streetName = $streetName;
        return $this;
    }

    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(string $postalCode): static {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getOtherCityName(): ?string { return $this->otherCityName; }
    public function setOtherCityName(?string $otherCityName): static {
        $this->otherCityName = $otherCityName;
        return $this;
    }

    public function getRegionName(): ?string { return $this->regionName; }
    public function setRegionName(?string $regionName): static {
        $this->regionName = $regionName;
        return $this;
    }

    public function getCountryName(): ?string { return $this->countryName; }
    public function setCountryName(?string $countryName): static {
        $this->countryName = $countryName;
        return $this;
    }

    public function getLongitude(): ?string { return $this->longitude; }
    public function setLongitude(?string $longitude): static {
        $this->longitude = $longitude;
        return $this;
    }

    public function getLatitude(): ?string { return $this->latitude; }
    public function setLatitude(?string $latitude): static {
        $this->latitude = $latitude;
        return $this;
    }

    /** @return Collection<int, ColivingSpace> */
    public function getColivingSpaces(): Collection { return $this->colivingSpaces; }

    public function addColivingSpace(ColivingSpace $colivingSpace): static {
        if (!$this->colivingSpaces->contains($colivingSpace)) {
            $this->colivingSpaces->add($colivingSpace);
            $colivingSpace->setAddress($this);
        }
        return $this;
    }

    public function removeColivingSpace(ColivingSpace $colivingSpace): static {
        if ($this->colivingSpaces->removeElement($colivingSpace)) {
            if ($colivingSpace->getAddress() === $this) {
                $colivingSpace->setAddress(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, User> */
    public function getUsers(): Collection { return $this->users; }

    public function addUser(User $user): static {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setAddress($this);
        }
        return $this;
    }

    public function removeUser(User $user): static {
        if ($this->users->removeElement($user)) {
            if ($user->getAddress() === $this) {
                $user->setAddress(null);
            }
        }
        return $this;
    }
}
