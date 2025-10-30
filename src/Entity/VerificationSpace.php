<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VerificationSpaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant une vérification effectuée sur un espace coliving ou privé
#[ORM\Entity(repositoryClass: VerificationSpaceRepository::class)]
#[ApiResource]
class VerificationSpace
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Date à laquelle la vérification a été effectuée (optionnelle)
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $verifiedAt = null;

    // Statut de la vérification (ex: "validé", "refusé", "en attente")
    #[ORM\Column(length: 50)]
    private ?string $status = null;

    // Notes ou commentaires liés à la vérification (optionnels)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    // Espace coliving concerné par la vérification
    #[ORM\ManyToOne(inversedBy: 'verificationSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    // Espace privé concerné par la vérification (optionnel)
    #[ORM\ManyToOne(inversedBy: 'verificationSpaces')]
    private ?PrivateSpace $privateSpace = null;

    // Utilisateur qui a effectué la vérification
    #[ORM\ManyToOne(inversedBy: 'userVerificationSpaces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Getters / Setters générés automatiquement
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
