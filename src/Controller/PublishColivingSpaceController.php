<?php

namespace App\Controller;

use App\Entity\ColivingSpace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

//Contrôleur permettant de publier un espace coliving. Action réservée aux employés ou administrateurs.
class PublishColivingSpaceController extends AbstractController
{
    /*Méthode d'exécution appelée automatiquement par API Platform.
    Le paramètre $colivingSpace est injecté grâce à l'ID passé dans l'URL.*/
    public function __invoke(ColivingSpace $colivingSpace, EntityManagerInterface $em): JsonResponse
    {
        // Vérifie si l’espace est déjà publié
        if ($colivingSpace->getIsActive()) {
            // Si oui, on renvoie un message d’erreur HTTP 400 (Bad Request)
            return $this->json([
                'message' => 'Cet espace est déjà publié.'
            ], 400);
        }

        // Mise à jour du statut de l’espace On active l’espace et on met à jour la date de modification
        $colivingSpace->setIsActive(true);
        $colivingSpace->setUpdatedAt(new \DateTimeImmutable());

        // Enregistrement en base de données
        $em->flush();

        //  Réponse JSON claire pour le front Le front saura que la publication a bien réussi
        return $this->json([
            'message' => 'Espace coliving publié avec succès.',
            'id' => $colivingSpace->getId(),
            'isActive' => $colivingSpace->getIsActive(),
        ]);
    }
}
