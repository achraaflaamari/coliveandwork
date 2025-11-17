<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur pour récupérer les photos des espaces
 */
class PhotoCollectionController extends AbstractController
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->select('p', 'cs', 'ps')
            ->from('App\Entity\Photo', 'p')
            ->leftJoin('p.colivingSpace', 'cs')
            ->leftJoin('p.privateSpace', 'ps');

        // Filtres optionnels
        if ($request->query->get('colivingSpace.id')) {
            $qb->andWhere('cs.id = :colivingSpaceId')
               ->setParameter('colivingSpaceId', $request->query->get('colivingSpace.id'));
        }

        if ($request->query->get('privateSpace.id')) {
            $qb->andWhere('ps.id = :privateSpaceId')
               ->setParameter('privateSpaceId', $request->query->get('privateSpace.id'));
        }

        if ($request->query->get('isMain') !== null) {
            $isMain = filter_var($request->query->get('isMain'), FILTER_VALIDATE_BOOLEAN);
            $qb->andWhere('p.isMain = :isMain')
               ->setParameter('isMain', $isMain);
        }

        // Tri par défaut : photos principales en premier, puis par date
        $qb->orderBy('p.isMain', 'DESC')
           ->addOrderBy('p.uploadedAt', 'DESC');

        // Pagination
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(1, (int) $request->query->get('itemsPerPage', 10)));
        $offset = ($page - 1) * $limit;

        $qb->setFirstResult($offset)
           ->setMaxResults($limit);

        $photos = $qb->getQuery()->getResult();

        // Compter le total
        $countQb = $this->entityManager->createQueryBuilder();
        $countQb->select('COUNT(p.id)')
                ->from('App\Entity\Photo', 'p')
                ->leftJoin('p.colivingSpace', 'cs')
                ->leftJoin('p.privateSpace', 'ps');

        // Appliquer les mêmes filtres pour le count
        if ($request->query->get('colivingSpace.id')) {
            $countQb->andWhere('cs.id = :colivingSpaceId')
                    ->setParameter('colivingSpaceId', $request->query->get('colivingSpace.id'));
        }

        if ($request->query->get('privateSpace.id')) {
            $countQb->andWhere('ps.id = :privateSpaceId')
                    ->setParameter('privateSpaceId', $request->query->get('privateSpace.id'));
        }

        if ($request->query->get('isMain') !== null) {
            $isMain = filter_var($request->query->get('isMain'), FILTER_VALIDATE_BOOLEAN);
            $countQb->andWhere('p.isMain = :isMain')
                    ->setParameter('isMain', $isMain);
        }

        $totalItems = $countQb->getQuery()->getSingleScalarResult();

        // Formatage des données
        $formattedPhotos = [];
        foreach ($photos as $photo) {
            $formattedPhotos[] = [
                'id' => $photo->getId(),
                'photoUrl' => $photo->getPhotoUrl(),
                'description' => $photo->getDescription(),
                'isMain' => $photo->getIsMain(),
                'uploadedAt' => $photo->getUploadedAt()->format('Y-m-d H:i:s'),
                'colivingSpace' => $photo->getColivingSpace() ? [
                    'id' => $photo->getColivingSpace()->getId(),
                    'titleColivingSpace' => $photo->getColivingSpace()->getTitleColivingSpace()
                ] : null,
                'privateSpace' => $photo->getPrivateSpace() ? [
                    'id' => $photo->getPrivateSpace()->getId(),
                    'titlePrivateSpace' => $photo->getPrivateSpace()->getTitlePrivateSpace()
                ] : null
            ];
        }

        return $this->json([
            'member' => $formattedPhotos,
            'totalItems' => $totalItems,
            'page' => $page,
            'itemsPerPage' => $limit,
            'totalPages' => ceil($totalItems / $limit)
        ]);
    }
}
