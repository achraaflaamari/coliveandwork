<?php

namespace App\Controller;

use App\Entity\PrivateSpace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/*Contrôleur : Suspendre un espace privé : Action réservée aux employés et administrateurs.
Permet de rendre un espace temporairement inactif (non visible sur le front).*/
class SuspendPrivateSpaceController extends AbstractController
{
    /* Méthode exécutée automatiquement par API Platform.Appelée lorsque la route `/private_spaces/{id}/suspend` est utilisée.*/
    public function __invoke(PrivateSpace $privateSpace, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Récupérer la raison optionnelle envoyée par le front
        $data = json_decode($request->getContent(), true);
        $reason = $data['reason'] ?? 'Non spécifié';

        // Vérifier si l’espace est déjà suspendu
        if (!$privateSpace->getIsActive()) {
            return $this->json([
                'message' => 'Cet espace privé est déjà suspendu.'
            ], 400);
        }

        // Suspendre l’espace
        $privateSpace->setIsActive(false);
        $privateSpace->setUpdatedAt(new \DateTimeImmutable());

        // Sauvegarder la modification
        $em->flush();

        // Retourner une réponse claire pour le front
        return $this->json([
            'message' => 'Espace privé suspendu avec succès.',
            'reason' => $reason,
            'id' => $privateSpace->getId(),
            'isActive' => $privateSpace->getIsActive(),
        ]);
    }
}
