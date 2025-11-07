<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\VerificationSpaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*Entité : Vérification d’un espace coliving ou d'un espace privé
 * - Créée par un employé ou un administrateur après la création d’un espace.
 * - Le propriétaire n’y a pas accès (il voit juste l’état de son espace).
 * - L’employé ou l’admin peuvent valider, refuser, ou ajouter des notes.
 */
#[ORM\Entity(repositoryClass: VerificationSpaceRepository::class)]
#[ApiResource(
    operations: [
        // Liste complète des vérifications — staff uniquement
        new GetCollection(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent consulter les vérifications."
        ),

        // Détail d'une vérification — staff uniquement
        new Get(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Accès réservé aux employés et administrateurs."
        ),

        // Création — staff uniquement
        new Post(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent créer une vérification d’espace."
        ),

        // Modification (valider / refuser / noter) — staff uniquement
        new Patch(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent modifier une vérification."
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',                 // filtrer par statut
    'colivingSpace.id' => 'exact',       // filtrer par coliving
    'privateSpace.id' => 'exact',        // filtrer par espace privé
    'user.email' => 'iexact'             // filtrer par vérificateur
])]
class VerificationSpace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Date de vérification
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    // Statut : "en attente", "validé", "refusé"
    #[ORM\Column(length: 50)]
    private ?string $status = 'en attente';

    // Notes internes ou commentaires
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    // Espace coliving concerné
    #[ORM\ManyToOne(inversedBy: 'verificationSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    // Espace privé concerné (facultatif)
    #[ORM\ManyToOne(inversedBy: 'verificationSpaces')]
    private ?PrivateSpace $privateSpace = null;

    // Employé/Admin ayant réalisé la vérification
    #[ORM\ManyToOne(inversedBy: 'userVerificationSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->status = 'en attente';
        $this->verifiedAt = new \DateTimeImmutable();
    }

    // === Getters / Setters ===
    public function getId(): ?int { return $this->id; }

    public function getVerifiedAt(): ?\DateTimeImmutable { return $this->verifiedAt; }
    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): static {
        $this->verifiedAt = $verifiedAt;
        return $this;
    }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static {
        $this->status = $status;
        return $this;
    }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static {
        $this->notes = $notes;
        return $this;
    }

    public function getColivingSpace(): ?ColivingSpace { return $this->colivingSpace; }
    public function setColivingSpace(?ColivingSpace $colivingSpace): static {
        $this->colivingSpace = $colivingSpace;
        return $this;
    }

    public function getPrivateSpace(): ?PrivateSpace { return $this->privateSpace; }
    public function setPrivateSpace(?PrivateSpace $privateSpace): static {
        $this->privateSpace = $privateSpace;
        return $this;
    }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static {
        $this->user = $user;
        return $this;
    }
}
