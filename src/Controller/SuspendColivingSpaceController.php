<?php

namespace App\Controller;

use App\Entity\ColivingSpace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/*Contrôleur qui permet de SUSPENDRE un espace coliving. Cette action est réservée aux employés et administrateurs.*/
class SuspendColivingSpaceController extends AbstractController
{
    /* Méthode d’exécution de l’action "Suspendre un espace". Elle est appelée automatiquement par API Platform quand la route /suspend est utilisée.*/
    public function __invoke(ColivingSpace $colivingSpace, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Récupérer le corps JSON envoyé dans la requête (le "body")
        $data = json_decode($request->getContent(), true);

        // Extraire le motif de suspension
        $reason = $data['reason'] ?? 'Non spécifié';

        // Vérifie si l’espace est déjà suspendu (isActive = false)
        if (!$colivingSpace->getIsActive()) {
            // Si oui, on renvoie un message d’erreur HTTP 400 (Bad Request) Cela évite de suspendre deux fois le même espace
            return $this->json([
                'message' => 'Cet espace est déjà suspendu.'
            ], 400);
        }

        //Suspendre l’espace On met isActive à false pour le rendre invisible aux clients
        $colivingSpace->setIsActive(false);

        // On met à jour la date de dernière modification
        $colivingSpace->setUpdatedAt(new \DateTimeImmutable());

        // Sauvegarder la modification en base de données
        $em->flush();

        //Retourner une réponse JSON claire pour le front On renvoie un message + les infos utiles (id, statut, motif)
        return $this->json([
            'message' => 'Espace suspendu avec succès.',
            'reason' => $reason,
            'id' => $colivingSpace->getId(),
            'isActive' => $colivingSpace->getIsActive(),
        ]);
    }
}
