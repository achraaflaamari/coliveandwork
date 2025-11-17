<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\PrivateSpaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des réservations
 */
#[Route('/api/reservations', name: 'api_reservations_')]
class ReservationController extends AbstractController
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
        private readonly PrivateSpaceRepository $privateSpaceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function createReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Données JSON invalides'], 400);
        }

        // Validation des champs requis
        $requiredFields = ['privateSpaceId', 'startDate', 'endDate', 'isForTwo', 'totalPrice'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Le champ '$field' est requis"], 400);
            }
        }

        // Vérifier que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Récupérer l'espace privé
        $privateSpace = $this->privateSpaceRepository->find($data['privateSpaceId']);
        if (!$privateSpace) {
            return $this->json(['error' => 'Espace privé non trouvé'], 404);
        }

        if (!$privateSpace->getIsActive()) {
            return $this->json(['error' => 'Cet espace n\'est pas disponible'], 400);
        }

        // Créer la réservation
        $reservation = new Reservation();
        
        try {
            $startDate = new \DateTime($data['startDate']);
            $endDate = new \DateTime($data['endDate']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Format de date invalide'], 400);
        }

        if ($startDate >= $endDate) {
            return $this->json(['error' => 'La date de fin doit être postérieure à la date de début'], 400);
        }

        if ($startDate < new \DateTime('today')) {
            return $this->json(['error' => 'La date de début ne peut pas être dans le passé'], 400);
        }

        // Vérifier les conflits de réservation
        $conflictingReservations = $this->reservationRepository->createQueryBuilder('r')
            ->where('r.privateSpace = :privateSpace')
            ->andWhere('r.status = :status')
            ->andWhere('(r.startDate <= :endDate AND r.endDate >= :startDate)')
            ->setParameter('privateSpace', $privateSpace)
            ->setParameter('status', 'confirmée')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();

        if (!empty($conflictingReservations)) {
            return $this->json(['error' => 'Ces dates ne sont pas disponibles'], 409);
        }

        $reservation->setStartDate($startDate)
                   ->setEndDate($endDate)
                   ->setIsForTwo($data['isForTwo'])
                   ->setTotalPrice($data['totalPrice'])
                   ->setLodgingTax($data['lodgingTax'] ?? '0.00')
                   ->setPrivateSpace($privateSpace)
                   ->setClient($user)
                   ->setStatus('en attente');

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Réservation créée avec succès',
            'reservation' => [
                'id' => $reservation->getId(),
                'startDate' => $reservation->getStartDate()->format('Y-m-d'),
                'endDate' => $reservation->getEndDate()->format('Y-m-d'),
                'isForTwo' => $reservation->isForTwo(),
                'totalPrice' => $reservation->getTotalPrice(),
                'status' => $reservation->getStatus(),
                'privateSpace' => [
                    'id' => $privateSpace->getId(),
                    'titlePrivateSpace' => $privateSpace->getTitlePrivateSpace()
                ]
            ]
        ], 201);
    }

    #[Route('/my-reservations', name: 'my_reservations', methods: ['GET'])]
    public function getMyReservations(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $reservations = $this->reservationRepository->findBy(
            ['client' => $user],
            ['createdAt' => 'DESC']
        );

        $formattedReservations = [];
        foreach ($reservations as $reservation) {
            $formattedReservations[] = [
                'id' => $reservation->getId(),
                'startDate' => $reservation->getStartDate()->format('Y-m-d'),
                'endDate' => $reservation->getEndDate()->format('Y-m-d'),
                'isForTwo' => $reservation->isForTwo(),
                'totalPrice' => $reservation->getTotalPrice(),
                'lodgingTax' => $reservation->getLodgingTax(),
                'status' => $reservation->getStatus(),
                'createdAt' => $reservation->getCreatedAt()->format('Y-m-d H:i:s'),
                'privateSpace' => [
                    'id' => $reservation->getPrivateSpace()->getId(),
                    'titlePrivateSpace' => $reservation->getPrivateSpace()->getTitlePrivateSpace(),
                    'pricePerMonth' => $reservation->getPrivateSpace()->getPricePerMonth(),
                    'colivingSpace' => [
                        'id' => $reservation->getPrivateSpace()->getColivingSpace()->getId(),
                        'titleColivingSpace' => $reservation->getPrivateSpace()->getColivingSpace()->getTitleColivingSpace()
                    ]
                ]
            ];
        }

        return $this->json([
            'reservations' => $formattedReservations,
            'totalItems' => count($formattedReservations)
        ]);
    }

    #[Route('/{id}/status', name: 'update_status', methods: ['PATCH'])]
    public function updateReservationStatus(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['status'])) {
            return $this->json(['error' => 'Le statut est requis'], 400);
        }

        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Réservation non trouvée'], 404);
        }

        $user = $this->getUser();
        $allowedStatuses = ['confirmée', 'refusée'];
        
        // Vérifier les permissions
        $isOwner = $user === $reservation->getPrivateSpace()->getColivingSpace()->getOwner();
        $isStaff = in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles());
        
        if (!$isOwner && !$isStaff) {
            return $this->json(['error' => 'Accès non autorisé'], 403);
        }

        if (!in_array($data['status'], $allowedStatuses)) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $reservation->setStatus($data['status']);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Statut de la réservation mis à jour',
            'reservation' => [
                'id' => $reservation->getId(),
                'status' => $reservation->getStatus()
            ]
        ]);
    }
}
