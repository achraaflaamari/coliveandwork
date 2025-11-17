<?php

namespace App\Controller;

use App\Repository\PrivateSpaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur pour récupérer tous les espaces privés actifs
 * avec support des filtres et pagination
 */
class PrivateSpaceCollectionController extends AbstractController
{
    public function __construct(
        private readonly PrivateSpaceRepository $privateSpaceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->select('ps', 'cs', 'cc', 'a')
            ->from('App\Entity\PrivateSpace', 'ps')
            ->leftJoin('ps.colivingSpace', 'cs')
            ->leftJoin('cs.colivingCity', 'cc')
            ->leftJoin('cs.address', 'a')
            ->where('ps.isActive = :active')
            ->setParameter('active', true);

        // Filtres optionnels basés sur les paramètres de requête
        if ($request->query->get('isActive') !== null) {
            $isActive = filter_var($request->query->get('isActive'), FILTER_VALIDATE_BOOLEAN);
            $qb->andWhere('ps.isActive = :isActiveFilter')
               ->setParameter('isActiveFilter', $isActive);
        }

        if ($request->query->get('capacity')) {
            $qb->andWhere('ps.capacity = :capacity')
               ->setParameter('capacity', $request->query->get('capacity'));
        }

        if ($request->query->get('pricePerMonth')) {
            $qb->andWhere('ps.pricePerMonth <= :maxPrice')
               ->setParameter('maxPrice', $request->query->get('pricePerMonth'));
        }

        if ($request->query->get('colivingSpace.id')) {
            $qb->andWhere('cs.id = :colivingSpaceId')
               ->setParameter('colivingSpaceId', $request->query->get('colivingSpace.id'));
        }

        // Tri par défaut
        $qb->orderBy('ps.createdAt', 'DESC');

        // Pagination
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(1, (int) $request->query->get('itemsPerPage', 10)));
        $offset = ($page - 1) * $limit;

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $spaces = $qb->getQuery()->getResult();

        // Compter le total pour la pagination
        $countQb = $this->entityManager->createQueryBuilder();
        $countQb->select('COUNT(ps.id)')
                ->from('App\Entity\PrivateSpace', 'ps')
                ->leftJoin('ps.colivingSpace', 'cs')
                ->where('ps.isActive = :active')
                ->setParameter('active', true);

        // Appliquer les mêmes filtres pour le count
        if ($request->query->get('isActive') !== null) {
            $isActive = filter_var($request->query->get('isActive'), FILTER_VALIDATE_BOOLEAN);
            $countQb->andWhere('ps.isActive = :isActiveFilter')
                    ->setParameter('isActiveFilter', $isActive);
        }

        if ($request->query->get('capacity')) {
            $countQb->andWhere('ps.capacity = :capacity')
                    ->setParameter('capacity', $request->query->get('capacity'));
        }

        if ($request->query->get('pricePerMonth')) {
            $countQb->andWhere('ps.pricePerMonth <= :maxPrice')
                    ->setParameter('maxPrice', $request->query->get('pricePerMonth'));
        }

        if ($request->query->get('colivingSpace.id')) {
            $countQb->andWhere('cs.id = :colivingSpaceId')
                    ->setParameter('colivingSpaceId', $request->query->get('colivingSpace.id'));
        }

        $totalItems = $countQb->getQuery()->getSingleScalarResult();

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
                'createdAt' => $space->getCreatedAt()->format('Y-m-d H:i:s'),
                'colivingSpace' => [
                    'id' => $space->getColivingSpace()->getId(),
                    'titleColivingSpace' => $space->getColivingSpace()->getTitleColivingSpace(),
                    'descriptionColivingSpace' => $space->getColivingSpace()->getDescriptionColivingSpace(),
                    'colivingCity' => [
                        'id' => $space->getColivingSpace()->getColivingCity()->getId(),
                        'name' => $space->getColivingSpace()->getColivingCity()->getName(),
                    ]
                ]
            ];
        }

        return $this->json([
            'member' => $formattedSpaces,
            'totalItems' => $totalItems,
            'page' => $page,
            'itemsPerPage' => $limit,
            'totalPages' => ceil($totalItems / $limit)
        ]);
    }
}
