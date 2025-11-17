<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ColivingCityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColivingCityRepository::class)]
#[ApiResource(
    operations: [
        // 🔹 Liste et consultation accessibles à tout le monde
        new GetCollection(
            security: "is_granted('PUBLIC_ACCESS')"
        ),
        new Get(
            security: "is_granted('PUBLIC_ACCESS')"
        ),

        // 🔹 Création, modification, suppression réservées à EMPLOYÉ ou ADMIN
        new Post(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent ajouter une ville."
        ),
        new Put(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent modifier une ville."
        ),
        new Patch(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent modifier une ville."
        ),
        new Delete(
            security: "is_granted('ROLE_EMPLOYEE') or is_granted('ROLE_ADMIN')",
            securityMessage: "Seuls les employés et administrateurs peuvent supprimer une ville."
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'ipartial'
])]
class ColivingCity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
}
