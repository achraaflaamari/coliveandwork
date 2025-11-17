<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*Entité représentant une réservation d’un espace privé (PrivateSpace)
 * - Le client connecté crée une réservation (statut initial = "en attente").
 * - Le propriétaire de l’espace peut confirmer ou refuser la réservation.
 * - L’employé ou l’administrateur peuvent exceptionnellement modifier le statut
 *   (litige, erreur ou absence du propriétaire).
 * - Le client ne peut ni annuler ni modifier la réservation.
 * - Les employés et administrateurs peuvent supprimer une réservation uniquement dans des cas exceptionnels.
 * - Le front bloque uniquement les réservations ayant le statut "confirmée".
 */
#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        // Lister toutes les réservations (employee et admin)
        new GetCollection(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent voir toutes les réservations."
        ),

        // Consulter une réservation : accessible à l'employee et l'admin, au client concerné, ou au propriétaire de l’espace réservé
        new Get(
            security: "
                is_granted('ROLE_ADMIN')
                or is_granted('ROLE_EMPLOYEE')
                or object.getClient() == user
                or object.getPrivateSpace().getColivingSpace().getOwner() == user
            ",
            securityMessage: "Accès restreint à la réservation (staff, client concerné ou propriétaire)."
        ),

        // Création : client connecté uniquement
        new Post(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Seuls les clients connectés peuvent créer une réservation."
        ),

        // Modification du statut :
        // Autorisée au propriétaire (cas normal) ou au staff (litige, erreur, absence du propriétaire)
        new Patch(
            security: "
                is_granted('ROLE_EMPLOYEE')
                or is_granted('ROLE_ADMIN')
                or (is_granted('ROLE_OWNER') and object.getPrivateSpace().getColivingSpace().getOwner() == user)
            ",
            securityMessage: "Seuls les propriétaires de l’espace ou le staff peuvent modifier le statut d’une réservation."
        ),

        // Suppression : autorisée à l’employé ou l’administrateur (cas exceptionnels)
        new Delete(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent supprimer une réservation (cas exceptionnels)."
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',             // filtrer par statut (confirmée, refusée, en attente, etc.)
    'client.email' => 'iexact',      // filtrer par client
    'privateSpace.id' => 'exact'     // filtrer par espace privé
])]
#[ApiFilter(DateFilter::class, properties: [
    'startDate', 'endDate'           // filtrer par période
])]
class Reservation
{
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

    // Réservation pour deux personnes ?
    #[ORM\Column]
    private ?bool $isForTwo = null;

    // Taxe de séjour
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $lodgingTax = null;

    // Prix total
    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $totalPrice = null;

    // Statut de la réservation (en attente, confirmée, refusée, annulée, terminée)
    #[ORM\Column(length: 50)]
    private ?string $status = 'en attente';

    // Date de création
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Avis associé à la réservation
    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Review $review = null;

    // Espace privé réservé
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PrivateSpace $privateSpace = null;

    // Client qui a effectué la réservation
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 'en attente';
    }

    // === Getters / Setters ===
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
