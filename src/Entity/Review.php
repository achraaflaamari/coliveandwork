<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité Review (Avis) Représente un avis laissé par un client après une réservation terminée
 * Lecture publique : tout le monde peut lire les avis (affichage sur le front).
 * Création : seuls les clients connectés peuvent laisser un avis.
 * Suppression : uniquement les employés ou administrateurs (modération).
 */
#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource(
    operations: [
        // Lecture publique : tous les avis visibles sur le front (pas besoin de connexion)
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),

        // Lecture d’un avis précis
        new Get(security: "is_granted('PUBLIC_ACCESS')"),

        // Création d’un avis : réservée aux clients connectés
        new Post(
            security: "is_granted('ROLE_USER')",
            securityMessage: "Seuls les clients connectés peuvent laisser un avis."
        ),

        // Suppression d’un avis : réservée aux employés ou administrateurs
        new Delete(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés ou administrateurs peuvent supprimer un avis."
        )
    ],
    normalizationContext: ['groups' => ['review:read']],
    denormalizationContext: ['groups' => ['review:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    // Permet de filtrer les avis sur le front :
    // - par coliving (afficher les avis d’un espace)
    // - par note (par ex : afficher seulement les 5 étoiles)
    'reservation.privateSpace.colivingSpace.id' => 'exact',
    'rating' => 'exact'
])]
class Review
{
    // Identifiant unique de l’avis
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Note donnée par le client (de 1 à 5)
    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 1)]
    private ?string $rating = null;

    // Commentaire laissé par le client (facultatif)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    // Date automatique de création de l’avis
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Chaque avis est lié à une réservation (1 seul avis par réservation)
    #[ORM\OneToOne(targetEntity: Reservation::class, inversedBy: 'review')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    // Constructeur : génère automatiquement la date de création
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ---  Getters / Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(string $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;
        return $this;
    }
}
