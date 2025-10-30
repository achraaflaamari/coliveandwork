<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité pour gérer les adresses (utilisée par les utilisateurs et les espaces coliving)
#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource]
#[ORM\Table(name: 'address')]
class Address
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Numéro de rue (ex: "12")
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $streetNumber = null;

    // Nom de la rue (ex: "Rue des Lilas")
    #[ORM\Column(length: 100)]
    private ?string $streetName = null;

    // Code postal (ex: "69001")
    #[ORM\Column(length: 20)]
    private ?string $postalCode = null;

    // Autre nom de ville (si différent de la ville principale)
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $otherCityName = null;

    // Nom de la région (ex: "Auvergne-Rhône-Alpes")
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $regionName = null;

    // Nom du pays (ex: "France")
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $countryName = null;

    // Longitude GPS (ex: "4.835659")
    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, nullable: true)]
    private ?string $longitude = null;

    // Latitude GPS (ex: "45.764043")
    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, nullable: true)]
    private ?string $latitude = null;

    // Ville de coliving à laquelle cette adresse est rattachée (relation récursive)
    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(name: 'coliving_city_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Address $colivingCity = null;

    // Liste des adresses rattachées à cette ville de coliving
    #[ORM\OneToMany(targetEntity: Address::class, mappedBy: 'colivingCity')]
    private Collection $addresses;

    // Espaces coliving associés à cette adresse
    #[ORM\OneToMany(targetEntity: ColivingSpace::class, mappedBy: 'address')]
    private Collection $colivingSpaces;

    // Utilisateurs associés à cette adresse
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'address')]
    private Collection $users;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->colivingSpaces = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

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

    public function getColivingCity(): ?Address { return $this->colivingCity; }
    public function setColivingCity(?Address $colivingCity): static {
        $this->colivingCity = $colivingCity;
        return $this;
    }

    /** @return Collection<int, Address> */
    public function getAddresses(): Collection {
        return $this->addresses;
    }

    public function addAddress(Address $address): static {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setColivingCity($this);
        }
        return $this;
    }

    public function removeAddress(Address $address): static {
        if ($this->addresses->removeElement($address)) {
            if ($address->getColivingCity() === $this) {
                $address->setColivingCity(null);
            }
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
    public function getUsers(): Collection {
        return $this->users;
    }

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
