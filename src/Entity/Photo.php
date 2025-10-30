<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant une photo associée à un espace coliving ou privé
#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ApiResource]
class Photo
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // URL de la photo (ex: lien vers un fichier image)
    #[ORM\Column(length: 255)]
    private ?string $photoUrl = null;

    // Description optionnelle de la photo
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    // Indique si cette photo est la principale (true = photo principale)
    #[ORM\Column]
    private ?bool $isMain = null;

    // Date d’envoi ou d’ajout de la photo
    #[ORM\Column]
    private ?\DateTimeImmutable $uploadedAt = null;

    // Espace coliving auquel cette photo est rattachée
    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    // Espace privé auquel cette photo est rattachée (optionnel)
    #[ORM\ManyToOne(inversedBy: 'photos')]
    private ?PrivateSpace $privateSpace = null;

    // Getters / Setters générés automatiquement
    public function getId(): ?int { return $this->id; }

    public function getPhotoUrl(): ?string { return $this->photoUrl; }
    public function setPhotoUrl(string $photoUrl): static {
        $this->photoUrl = $photoUrl;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static {
        $this->description = $description;
        return $this;
    }

    public function isMain(): ?bool { return $this->isMain; }
    public function setIsMain(bool $isMain): static {
        $this->isMain = $isMain;
        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable { return $this->uploadedAt; }
    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static {
        $this->uploadedAt = $uploadedAt;
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
}
