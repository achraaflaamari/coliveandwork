<?php

namespace App\Controller;

use App\Repository\PrivateSpaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Contrôleur pour récupérer les 3 meilleurs espaces privés actifs
 * triés par note moyenne (basée sur les avis des réservations)
 */
class PrivateSpaceTopController extends AbstractController
{
    public function __construct(
        private readonly PrivateSpaceRepository $privateSpaceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(): JsonResponse
    {
        // Pour le moment, récupérons simplement les 3 espaces privés actifs les plus récents
        // TODO: Implémenter le tri par note moyenne quand il y aura des données
        $qb = $this->entityManager->createQueryBuilder();
        
        $spaces = $qb
            ->select('ps', 'cs')
            ->from('App\Entity\PrivateSpace', 'ps')
            ->leftJoin('ps.colivingSpace', 'cs')
            ->where('ps.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('ps.createdAt', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        // Formatage des données pour le frontend
        $formattedSpaces = [];
        foreach ($spaces as $space) {
            $formattedSpaces[] = [
                'id' => $space->getId(),
                'titlePrivateSpace' => $space->getTitlePrivateSpace(),
                'descriptionPrivateSpace' => $space->getDescriptionPrivateSpace(),
                'capacity' => $space->getCapacity(),
                'areaM2' => $space->getAreaM2(),
                'pricePerMonth' => $space->getPricePerMonth(),
                'isActive' => $space->getIsActive(),
                'avgRating' => 4.5, // Valeur par défaut pour les tests
                'colivingSpace' => [
                    'id' => $space->getColivingSpace()->getId(),
                    'titleColivingSpace' => $space->getColivingSpace()->getTitleColivingSpace(),
                    'descriptionColivingSpace' => $space->getColivingSpace()->getDescriptionColivingSpace(),
                ]
            ];
        }

        return $this->json([
            'member' => $formattedSpaces,
            'totalItems' => count($formattedSpaces)
        ]);
    }
}
