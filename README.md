# 🏠 Colive&Work - Backend API



## 🎯 Guide de Création des Contrôleurs

### 1. Structure des Contrôleurs

Les contrôleurs personnalisés sont organisés dans `src/Controller/` et suivent cette structure :

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MonControllerController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        // Logique métier
        return $this->json(['data' => 'result']);
    }
}
```

### 2. Types de Contrôleurs Implémentés

#### A. Contrôleur de Collection Simple
**Exemple : `ColivingCityCollectionController`**

```php
final class ColivingCityCollectionController
{
    public function __construct(private readonly ColivingCityRepository $repository) {}

    public function __invoke(): iterable
    {
        return $this->repository
            ->createQueryBuilder('city')
            ->orderBy('city.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
```

**Intégration dans l'entité :**
```php
#[ApiResource(
    operations: [
        new GetCollection(
            controller: ColivingCityCollectionController::class,
            security: "is_granted('PUBLIC_ACCESS')"
        ),
    ]
)]
```

#### B. Contrôleur de Collection Avancé avec Filtres
**Exemple : `PrivateSpaceCollectionController`**

```php
class PrivateSpaceCollectionController extends AbstractController
{
    public function __invoke(Request $request): JsonResponse
    {
        $qb = $this->entityManager->createQueryBuilder();
        
        $qb->select('ps', 'cs', 'cc', 'a')
            ->from('App\Entity\PrivateSpace', 'ps')
            ->leftJoin('ps.colivingSpace', 'cs')
            ->where('ps.isActive = :active')
            ->setParameter('active', true);

        // Filtres dynamiques
        if ($request->query->get('capacity')) {
            $qb->andWhere('ps.capacity = :capacity')
               ->setParameter('capacity', $request->query->get('capacity'));
        }

        // Pagination
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(1, (int) $request->query->get('itemsPerPage', 10)));
        
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $this->json([
            'member' => $formattedData,
            'totalItems' => $totalItems,
            'page' => $page,
            'itemsPerPage' => $limit
        ]);
    }
}
```

#### C. Contrôleur avec Route Personnalisée
**Exemple : `PrivateSpaceTopController`**

```php
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/private_spaces/top',
            controller: PrivateSpaceTopController::class,
            security: "is_granted('PUBLIC_ACCESS')"
        ),
    ]
)]
```

#### D. Contrôleur d'Actions Métier
**Exemple : `AuthController`**

```php
#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/register/client', name: 'register_client', methods: ['POST'])]
    public function registerClient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Validation
        if (!$data || empty($data['email'])) {
            return $this->json(['error' => 'Email requis'], 400);
        }
        
        // Logique métier
        $user = new User();
        $user->setEmail($data['email'])
             ->setRoles(['ROLE_USER']);
             
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $this->json(['message' => 'Utilisateur créé'], 201);
    }
}
```

### 3. Bonnes Pratiques

#### A. Injection de Dépendances
```php
public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly MyRepository $repository,
    private readonly ValidatorInterface $validator
) {}
```

#### B. Gestion des Erreurs
```php
try {
    // Logique métier
} catch (\Exception $e) {
    return $this->json(['error' => 'Message d\'erreur'], 500);
}
```

#### C. Validation des Données
```php
$errors = $this->validator->validate($entity);
if (count($errors) > 0) {
    $errorMessages = [];
    foreach ($errors as $error) {
        $errorMessages[] = $error->getMessage();
    }
    return $this->json(['errors' => $errorMessages], 400);
}
```

#### D. Formatage des Réponses
```php
$formattedData = [];
foreach ($entities as $entity) {
    $formattedData[] = [
        'id' => $entity->getId(),
        'name' => $entity->getName(),
        'createdAt' => $entity->getCreatedAt()->format('Y-m-d H:i:s')
    ];
}

return $this->json([
    'member' => $formattedData,
    'totalItems' => count($formattedData)
]);
```

### 4. Configuration de Sécurité

#### A. Accès Public
```yaml
# config/packages/security.yaml
access_control:
    - { path: ^/api/coliving_cities, roles: PUBLIC_ACCESS, methods: [GET] }
    - { path: ^/api/private_spaces, roles: PUBLIC_ACCESS, methods: [GET] }
```

#### B. Sécurité dans les Entités
```php
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('PUBLIC_ACCESS')"),
        new Post(security: "is_granted('ROLE_USER')"),
        new Put(security: "is_granted('ROLE_ADMIN') or object.getOwner() == user"),
    ]
)]
```

## 📚 APIs Disponibles

### Endpoints Publics (Lecture)
- `GET /api/coliving_cities` - Liste des villes
- `GET /api/private_spaces` - Espaces privés avec filtres
- `GET /api/private_spaces/top` - Top 3 des espaces les mieux notés
- `GET /api/photos` - Photos des espaces

### Endpoints d'Authentification
- `POST /api/auth/register/client` - Inscription client
- `POST /api/auth/register/owner` - Inscription propriétaire
- `GET /api/auth/profile` - Profil utilisateur
- `POST /api/login_check` - Connexion JWT

### Endpoints Protégés
- `POST /api/reservations` - Créer une réservation
- `GET /api/reservations/my-reservations` - Mes réservations
- `PATCH /api/reservations/{id}/status` - Modifier statut réservation

## 🔧 Outils de Développement

### Tests des APIs
```bash
# Tester une API
curl -X GET "http://127.0.0.1:8000/api/coliving_cities" -H "Accept: application/json"

# Avec authentification JWT
curl -X GET "http://127.0.0.1:8000/api/reservations/my-reservations" \
     -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Documentation API
- Interface Swagger : `http://127.0.0.1:8000/api/docs`
- Format JSON-LD : `http://127.0.0.1:8000/api/docs.json`

### Commandes Utiles
```bash
# Vider le cache
php bin/console cache:clear

# Créer une migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Recharger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

## 🏗️ Architecture

```
src/
├── Controller/          # Contrôleurs personnalisés
│   ├── AuthController.php
│   ├── PrivateSpaceTopController.php
│   └── PrivateSpaceCollectionController.php
├── Entity/             # Entités Doctrine + API Platform
│   ├── User.php
│   ├── PrivateSpace.php
│   └── ColivingCity.php
├── Repository/         # Repositories Doctrine
└── DataFixtures/       # Données de test
```

