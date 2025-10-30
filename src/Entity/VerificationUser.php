<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VerificationUserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant une vérification de document liée à un proprietaire
#[ORM\Entity(repositoryClass: VerificationUserRepository::class)]
#[ApiResource]
class VerificationUser
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Type de document vérifié (ex: "CNI", "Passeport")
    #[ORM\Column(length: 50)]
    private ?string $documentType = null;

    // URL du document stocké (ex: lien vers un fichier PDF ou image)
    #[ORM\Column(length: 255)]
    private ?string $documentUrl = null;

    // Date de création de la vérification
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Date de validation du document (optionnelle)
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    // Statut de la vérification (ex: "validé", "refusé", "en attente")
    #[ORM\Column(length: 50)]
    private ?string $status = null;

    // Notes internes ou commentaires (optionnels)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    // Utilisateur qui a effectué la vérification (ex: admin ou modérateur)
    #[ORM\ManyToOne(inversedBy: 'ownedVerifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    // Utilisateur concerné par la vérification
    #[ORM\ManyToOne(inversedBy: 'userVerifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Getters / Setters générés automatiquement
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
