<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\VerificationUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*Entité : Vérification d’identité d’un propriétaire (ROLE_OWNER)
 * - Créée automatiquement à l’inscription d’un propriétaire.
 * - Seuls les employés et administrateurs peuvent consulter et valider/refuser.
 * - Le statut passe de "en attente" → "validé" ou "refusé".
 */
#[ORM\Entity(repositoryClass: VerificationUserRepository::class)]
#[ApiResource(
    operations: [
        // Liste complète — employee et admin uniquement
        new GetCollection(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent voir les vérifications."
        ),

        // Détail d'une vérification — employee et admin uniquement
        new Get(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent consulter les détails d'une vérification."
        ),

        // Validation ou refus — employee et admin uniquement
        new Patch(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent modifier le statut d'une vérification."
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',             // filtrer par statut (en attente / validé / refusé)
    'user.email' => 'iexact',        // filtrer par propriétaire
    'documentType' => 'ipartial'     // recherche par type de document
])]
class VerificationUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Type de document (CNI, passeport, justificatif, etc.)
    #[ORM\Column(length: 50)]
    private ?string $documentType = null;

    // Lien du fichier stocké (PDF ou image)
    #[ORM\Column(length: 255)]
    private ?string $documentUrl = null;

    // Date de création
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Date de validation (optionnelle)
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    // Statut : "en attente", "validé", "refusé"
    #[ORM\Column(length: 50)]
    private ?string $status = 'en attente';

    // Notes internes du employee et admin
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    // Employé/Admin ayant effectué la vérification
    #[ORM\ManyToOne(inversedBy: 'ownedVerifications')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $owner = null;

    // Propriétaire concerné
    #[ORM\ManyToOne(inversedBy: 'verificationUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 'en attente';
    }

    // === Getters / Setters ===
    public function getId(): ?int { return $this->id; }

    public function getDocumentType(): ?string { return $this->documentType; }
    public function setDocumentType(string $documentType): static {
        $this->documentType = $documentType;
        return $this;
    }

    public function getDocumentUrl(): ?string { return $this->documentUrl; }
    public function setDocumentUrl(string $documentUrl): static {
        $this->documentUrl = $documentUrl;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

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

    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): static {
        $this->owner = $owner;
        return $this;
    }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static {
        $this->user = $user;
        return $this;
    }
}
