<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Controller\PhotoCollectionController;
use App\Repository\PhotoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*Entité représentant une photo d’un espace coliving ou privé.Lecture publique (affichage sur le front)*/

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[ApiResource(
    operations: [
        //Lecture publique (affichage des photos sur le site)
        new GetCollection(
            controller: PhotoCollectionController::class,
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Get(security: "is_granted('PUBLIC_ACCESS')")
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'colivingSpace.id' => 'exact',
    'privateSpace.id' => 'exact',
    'isMain' => 'exact'
])]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $photoUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $isMain = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ColivingSpace $colivingSpace = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    private ?PrivateSpace $privateSpace = null;

    public function __construct()
    {
        $this->uploadedAt = new \DateTimeImmutable();
    }

    // --- Getters / Setters ---
    public function getId(): ?int { return $this->id; }
    public function getPhotoUrl(): ?string { return $this->photoUrl; }
    public function setPhotoUrl(string $photoUrl): static { $this->photoUrl = $photoUrl; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getIsMain(): ?bool { return $this->isMain; }
    public function setIsMain(bool $isMain): static { $this->isMain = $isMain; return $this; }
    public function getUploadedAt(): ?\DateTimeImmutable { return $this->uploadedAt; }
    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static { $this->uploadedAt = $uploadedAt; return $this; }
    public function getColivingSpace(): ?ColivingSpace { return $this->colivingSpace; }
    public function setColivingSpace(?ColivingSpace $colivingSpace): static { $this->colivingSpace = $colivingSpace; return $this; }
    public function getPrivateSpace(): ?PrivateSpace { return $this->privateSpace; }
    public function setPrivateSpace(?PrivateSpace $privateSpace): static { $this->privateSpace = $privateSpace; return $this; }
}
