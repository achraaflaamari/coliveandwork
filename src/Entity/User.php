<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

// Entité principale représentant un utilisateur du système
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', columns: ['email'])
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[ORM\Column(nullable: true)]
    private ?bool $gender = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isEmailVerified = false;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Réservations effectuées par l'utilisateur
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'client')]
    private Collection $reservations;

    // Espaces coliving créés par l'utilisateur
    #[ORM\OneToMany(targetEntity: ColivingSpace::class, mappedBy: 'owner')]
    private Collection $colivingSpaces;

    // Adresse associée à l'utilisateur
    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Address $address = null;

    // Photo de profil
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Photo $photo = null;

    // Messages reçus par l'utilisateur
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $messages;

    // Vérifications de documents liées à l'utilisateur
    #[ORM\OneToMany(targetEntity: VerificationUser::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $verificationUsers;

    // Vérifications des espaces coliving créés par l'utilisateur
    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'owner')]
    private Collection $verificationSpaces;

    // Vérifications des espaces où l'utilisateur est impliqué
    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userVerificationSpaces;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->colivingSpaces = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->verificationUsers = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->userVerificationSpaces = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array { return $this->roles; }
    public function setRoles(array $roles): static {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static {
        $this->password = $password;
        return $this;
    }

    public function getFirstname(): ?string { return $this->firstname; }
    public function setFirstname(string $firstname): static {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string { return $this->lastname; }
    public function setLastname(string $lastname): static {
        $this->lastname = $lastname;
        return $this;
    }

    public function getGender(): ?bool { return $this->gender; }
    public function setGender(?bool $gender): static {
        $this->gender = $gender;
        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface { return $this->birthDate; }
    public function setBirthDate(?\DateTimeInterface $birthDate): static {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(?string $phoneNumber): static {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getIsEmailVerified(): ?bool { return $this->isEmailVerified; }
    public function setIsEmailVerified(bool $isEmailVerified): static {
        $this->isEmailVerified = $isEmailVerified;
        return $this;
    }

    public function getIsActive(): ?bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $address): static {
        $this->address = $address;
        return $this;
    }

    public function getPhoto(): ?Photo { return $this->photo; }
    public function setPhoto(?Photo $photo): static {
        $this->photo = $photo;
        return $this;
    }

    /** @return Collection<int, Reservation> */
    public function getReservations(): Collection { return $this->reservations; }

    /** @return Collection<int, ColivingSpace> */
    public function getColivingSpaces(): Collection { return $this->colivingSpaces; }

    /** @return Collection<int, Message> */
    public function getMessages(): Collection { return $this->messages; }

    /** @return Collection<int, VerificationUser> */
    public function getVerificationUsers(): Collection { return $this->verificationUsers; }

    /** @return Collection<int, VerificationSpace> */
    public function getVerificationSpaces(): Collection { return $this->verificationSpaces; }

    /** @return Collection<int, VerificationSpace> */
    public function getUserVerificationSpaces(): Collection { return $this->userVerificationSpaces; }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
