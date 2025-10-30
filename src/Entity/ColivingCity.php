<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ColivingCityRepository;
use Doctrine\ORM\Mapping as ORM;

// Entité représentant une ville dans laquelle se trouvent des espaces de coliving
#[ORM\Entity(repositoryClass: ColivingCityRepository::class)]
#[ApiResource]
class ColivingCity
{
    // Identifiant unique auto-généré
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom de la ville (ex: "Lyon", "Marseille")
    #[ORM\Column(length: 50)]
    private ?string $name = null;

    // Getter pour l'identifiant
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le nom de la ville
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter pour le nom de la ville
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
}
