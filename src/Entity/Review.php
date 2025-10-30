<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant un avis laissé suite à une réservation
#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource]
class Review
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Note attribuée (ex: 4.5 sur 5)
    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]
    private ?string $rating = null;

    // Commentaire libre (optionnel)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    // Date de création de l’avis
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Réservation liée à cet avis (relation 1:1)
    #[ORM\OneToOne(inversedBy: 'review', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    // Getters / Setters générés automatiquement
    public function getId(): ?int { return $this->id; }

    public function getRating(): ?string { return $this->rating; }
    public function setRating(string $rating): static {
        $this->rating = $rating;
        return $this;
    }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): static {
        $this->comment = $comment;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(Reservation $reservation): static {
        $this->reservation = $reservation;
        return $this;
    }
}
