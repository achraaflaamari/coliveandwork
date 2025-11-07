<?php

namespace App\Controller;

use App\Entity\PrivateSpace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/* Contrôleur : Publier un espace privé : Action réservée aux employés et administrateurs*/
class PublishPrivateSpaceController extends AbstractController
{
    /*Méthode exécutée automatiquement par API Platform. Appelée lorsque la route `/private_spaces/{id}/publish` est utilisée.*/
    public function __invoke(PrivateSpace $privateSpace, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier si l’espace est déjà publié
        if ($privateSpace->getIsActive()) {
            return $this->json([
                'message' => 'Cet espace privé est déjà publié.'
            ], 400);
        }

        // Activer l’espace
        $privateSpace->setIsActive(true);
        $privateSpace->setUpdatedAt(new \DateTimeImmutable());

        // Enregistrer la modification
        $em->flush();

        // Répondre au front
        return $this->json([
            'message' => 'Espace privé publié avec succès.',
            'id' => $privateSpace->getId(),
            'isActive' => $privateSpace->getIsActive(),
        ]);
    }
}
