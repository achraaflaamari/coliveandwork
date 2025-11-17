<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/*Entité représentant un utilisateur du système.
 * - Peut être client, propriétaire, employé ou administrateur.
 * - Contient les informations de profil, rôles et relations (réservations, messages, etc.).
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', columns: ['email'])
])]

/* Exposition API sécurisée via API Platform :
 * - Les administrateurs et employés peuvent consulter tous les utilisateurs.
 * - Chaque utilisateur ne peut voir ou modifier que son propre profil.
 */
#[ApiResource(
    operations: [
        //  Liste complète des utilisateurs → réservée au staff
        new GetCollection(security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')"),

        // Consultation d’un profil utilisateur
        new Get(security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN') or object == user"),

        // Inscription publique
        new Post(security: "is_granted('PUBLIC_ACCESS')"),

        // Modification de profil (admin ou utilisateur lui-même)
        new Put(security: "is_granted('ROLE_ADMIN') or object == user"),
        new Patch(security: "is_granted('ROLE_ADMIN') or object == user"),

        // Suppression d’un compte (admin uniquement)
        new Delete(security: "is_granted('ROLE_ADMIN')")
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]

/**
 * 🔍 Filtres de recherche dans l’API
 */
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'iexact',
    'firstname' => 'ipartial',
    'lastname' => 'ipartial',
    'roles' => 'exact',
    'isActive' => 'exact'
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // --- Identifiant unique ---
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    // --- Email unique (identifiant de connexion) ---
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Email]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    // --- Rôles (ROLE_USER, ROLE_OWNER, ROLE_EMPLOYEE, ROLE_ADMIN) ---
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    // --- Mot de passe sécurisé (haché) ---
    #[ORM\Column]
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 8)]
    #[Groups(['user:write'])]
    private ?string $password = null;

    // --- Prénom et nom ---
    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastname = null;

    // --- Sexe, date de naissance, téléphone ---
    #[ORM\Column(nullable: true)]
    private ?bool $gender = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    // --- État du compte ---
    #[ORM\Column(options: ['default' => false])]
    private ?bool $isEmailVerified = false;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isActive = true;

    // --- Date de création du compte ---
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // RELATIONS AVEC LES AUTRES ENTITÉS

    // Réservations du client
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'client')]
    private Collection $reservations;

    // Espaces de coliving créés par le propriétaire
    #[ORM\OneToMany(targetEntity: ColivingSpace::class, mappedBy: 'owner')]
    private Collection $colivingSpaces;

    // Adresse principale (Many users -> One address)
    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Address $address = null;

    // Photo de profil
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Photo $photo = null;

    // Messages envoyés
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messagesSent;

    // Messages reçus
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    private Collection $messagesReceived;

    // Vérifications de documents utilisateur (CNI, passeport…)
    #[ORM\OneToMany(targetEntity: VerificationUser::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $verificationUsers;

    // Vérifications des espaces (effectuées par employé/admin)
    #[ORM\OneToMany(targetEntity: VerificationSpace::class, mappedBy: 'user')]
    private Collection $userVerificationSpaces;

     // Vérifications d'utilisateurs effectuées (en tant qu'employé/admin)
     #[ORM\OneToMany(targetEntity: VerificationUser::class, mappedBy: 'owner')]
     private Collection $ownedVerifications;

    //  CONSTRUCTEUR
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->colivingSpaces = new ArrayCollection();
        $this->messagesSent = new ArrayCollection();
        $this->messagesReceived = new ArrayCollection();
        $this->verificationUsers = new ArrayCollection();
        $this->verificationSpaces = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->userVerificationsPerformed = new ArrayCollection();
    }

    // MÉTHODES DE BASE

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // chaque utilisateur a ce rôle par défaut
        return array_unique($roles);
    }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getFirstname(): ?string { return $this->firstname; }
    public function setFirstname(string $firstname): static { $this->firstname = $firstname; return $this; }

    public function getLastname(): ?string { return $this->lastname; }
    public function setLastname(string $lastname): static { $this->lastname = $lastname; return $this; }

    public function getGender(): ?bool { return $this->gender; }
    public function setGender(?bool $gender): static { $this->gender = $gender; return $this; }

    public function getBirthDate(): ?\DateTimeInterface { return $this->birthDate; }
    public function setBirthDate(?\DateTimeInterface $birthDate): static { $this->birthDate = $birthDate; return $this; }

    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(?string $phoneNumber): static { $this->phoneNumber = $phoneNumber; return $this; }

    public function getIsEmailVerified(): ?bool { return $this->isEmailVerified; }
    public function setIsEmailVerified(bool $isEmailVerified): static { $this->isEmailVerified = $isEmailVerified; return $this; }

    public function getIsActive(): ?bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getAddress(): ?Address { return $this->address; }
    public function setAddress(?Address $address): static { $this->address = $address; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function eraseCredentials(): void {}
}
