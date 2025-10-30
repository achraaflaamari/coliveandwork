<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant une réservation d’un espace privé
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource]
class Reservation
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Date de début de la réservation
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $startDate = null;

    // Date de fin de la réservation
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $endDate = null;

    // Indique si la réservation est pour deux personnes
    #[ORM\Column]
    private ?bool $isForTwo = null;

    // Taxe de séjour appliquée
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $lodgingTax = null;

    // Prix total de la réservation
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $totalPrice = null;

    // Statut de la réservation (ex: "confirmée", "annulée")
    #[ORM\Column(length: 50)]
    private ?string $status = null;

    // Date de création de la réservation
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Avis associé à cette réservation (relation 1:1)
    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Review $review = null;

    // Espace privé réservé
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PrivateSpace $privateSpace = null;

    // Utilisateur ayant effectué la réservation
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    // Getters / Setters générés automatiquement
    public function getId(): ?int { return $this->id; }

    public function getStartDate(): ?\DateTime { return $this->startDate; }
    public function setStartDate(\DateTime $startDate): static {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTime { return $this->endDate; }
    public function setEndDate(\DateTime $endDate): static {
        $this->endDate = $endDate;
        return $this;
    }

    public function isForTwo(): ?bool { return $this->isForTwo; }
    public function setIsForTwo(bool $isForTwo): static {
        $this->isForTwo = $isForTwo;
        return $this;
    }

    public function getLodgingTax(): ?string { return $this->lodgingTax; }
    public function setLodgingTax(string $lodgingTax): static {
        $this->lodgingTax = $lodgingTax;
        return $this;
    }

    public function getTotalPrice(): ?string { return $this->totalPrice; }
    public function setTotalPrice(string $totalPrice): static {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReview(): ?Review { return $this->review; }
    public function setReview(Review $review): static {
        // Synchronisation bidirectionnelle
        if ($review->getReservation() !== $this) {
            $review->setReservation($this);
        }
        $this->review = $review;
        return $this;
    }

    public function getPrivateSpace(): ?PrivateSpace { return $this->privateSpace; }
    public function setPrivateSpace(?PrivateSpace $privateSpace): static {
        $this->privateSpace = $privateSpace;
        return $this;
    }

    public function getClient(): ?User { return $this->client; }
    public function setClient(?User $client): static {
        $this->client = $client;
        return $this;
    }
}
