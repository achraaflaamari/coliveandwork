# Correction de l'authentification JWT - API login_check

## Problème initial
L'API `/api/login_check` retournait une erreur **404 Not Found**.

## Causes identifiées

### 1. Configuration de sécurité incomplète
- L'entité `User` n'implémentait pas les interfaces requises par Symfony Security
- Aucun firewall configuré pour gérer l'authentification JSON
- Pas de bundle JWT installé

### 2. Route sans contrôleur
- La route `/api/login_check` était définie mais sans contrôleur associé
- Le système `json_login` nécessite une route avec un contrôleur

### 3. Mot réservé PostgreSQL
- Le nom de table `user` est un mot réservé en PostgreSQL
- Causait des erreurs SQL lors des requêtes

## Solutions appliquées

### 1. Mise à jour de l'entité User
**Fichier:** `src/Entity/User.php`

```php
// Ajout des interfaces
class User implements UserInterface, PasswordAuthenticatedUserInterface

// Échappement du nom de table PostgreSQL
#[ORM\Table(name: '`user`')]

// Ajout des méthodes requises
public function getUserIdentifier(): string
{
    return (string) $this->email;
}

public function eraseCredentials(): void
{
    // Nettoyage des données sensibles temporaires
}
```

**Pourquoi ?**
- `UserInterface` : Requis par Symfony Security pour identifier un utilisateur
- `PasswordAuthenticatedUserInterface` : Requis pour la gestion des mots de passe
- Backticks : Échappent le mot réservé PostgreSQL

### 2. Installation et configuration JWT
**Commandes:**
```bash
composer require lexik/jwt-authentication-bundle
php bin/console lexik:jwt:generate-keypair
```

**Pourquoi ?**
- Génère et gère les tokens JWT pour l'authentification stateless
- Crée automatiquement les clés privée/publique pour signer les tokens

### 3. Configuration de la sécurité
**Fichier:** `config/packages/security.yaml`

```yaml
firewalls:
    login:
        pattern: ^/api/login
        stateless: true
        json_login:
            check_path: /api/login_check
            username_path: email
            password_path: password
            success_handler: lexik_jwt_authentication.handler.authentication_success
            failure_handler: lexik_jwt_authentication.handler.authentication_failure
    main:
        lazy: true
        provider: app_user_provider
        stateless: true
        jwt: ~
```

**Pourquoi ?**
- **Firewall `login`** : Gère uniquement l'authentification (génération du token)
- **Firewall `main`** : Protège les autres routes avec validation JWT
- **stateless: true** : Pas de session, tout repose sur le token
- **Handlers JWT** : Retournent le token en cas de succès

### 4. Création du contrôleur
**Fichier:** `src/Controller/SecurityController.php`

```php
#[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
public function login(): JsonResponse
{
    // L'authentificateur json_login intercepte la requête
    return new JsonResponse(['message' => 'Login endpoint']);
}
```

**Pourquoi ?**
- Symfony nécessite une route avec un contrôleur
- Le contrôleur n'est jamais exécuté car `json_login` intercepte la requête avant
- Sert de point d'entrée pour le système de routing

### 5. Création d'utilisateur de test
**Fichier:** `src/DataFixtures/AppFixtures.php`

```php
$user = new User();
$user->setEmail('test@example.com');
$user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
// ... autres propriétés
$manager->persist($user);
```

**Commande:**
```bash
php bin/console doctrine:fixtures:load --append
```

**Pourquoi ?**
- Permet de tester l'authentification immédiatement
- Le mot de passe est correctement hashé avec l'algorithme Symfony


## Fichiers modifiés

1. `src/Entity/User.php` - Interfaces + échappement table
2. `config/packages/security.yaml` - Configuration firewalls
3. `src/Controller/SecurityController.php` - Route login
4. `src/DataFixtures/AppFixtures.php` - Utilisateur de test
5. `.env` - Variables JWT (auto-générées)

