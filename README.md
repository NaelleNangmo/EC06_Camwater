
##  Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Technologies](#-technologies)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Tests](#-tests)
- [Documentation](#-documentation)
- [Sécurité](#-sécurité)

##  Fonctionnalités

### Gestion des abonnés
- ✅ Création, modification, suppression d'abonnés
- ✅ Consultation de la liste des abonnés (paginée)
- ✅ Détails d'un abonné avec ses factures
- ✅ Validation des données (villes autorisées : Yaoundé, Douala, Bafoussam, Garoua, Bamenda)

### Gestion des factures
- ✅ Génération automatique de factures avec calcul par tranches
- ✅ Consultation des factures (paginée)
- ✅ Mise à jour du statut (Emise, Payee, Annulee)
- ✅ Suppression de factures

### Sécurité
- ✅ Authentification par token (Laravel Sanctum)
- ✅ Protection des routes sensibles
- ✅ Validation des données
- ✅ Gestion des erreurs

### Performance
- ✅ Système de cache (30 minutes)
- ✅ Invalidation automatique du cache
- ✅ Pagination (15 éléments par page)

### Logs
- ✅ Logs MongoDB pour traçabilité
- ✅ Logs Laravel pour débogage

##  Technologies

- **Framework** : Laravel 11.x
- **Base de données** : MySQL
- **Authentification** : Laravel Sanctum
- **Cache** : Redis / File
- **Logs** : MongoDB (optionnel) + Laravel Log
- **Tests** : PHPUnit

##  Installation

### Prérequis

- PHP 8.2+
- Composer
- MySQL 8.0+
- Redis (optionnel, pour le cache)
- MongoDB (optionnel, pour les logs)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd backend
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**

Éditer le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=camwater_pro
DB_USERNAME=root
DB_PASSWORD=
```

5. **Exécuter les migrations**
```bash
php artisan migrate
```

6. **Créer les utilisateurs de test**
```bash
php artisan db:seed --class=UserSeeder
```

7. **Lancer le serveur**
```bash
php artisan serve
```

L'API est maintenant accessible sur `http://localhost:8000`

##  Configuration

### Cache

Par défaut, le cache utilise le système de fichiers. Pour utiliser Redis :

```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### MongoDB (optionnel)

Pour activer les logs MongoDB :

```env
MONGODB_DSN=mongodb://localhost:27017
MONGODB_DATABASE=camwater_logs
```

Voir [MONGODB_INSTALLATION.md](MONGODB_INSTALLATION.md) pour plus de détails.

##  Utilisation

### Authentification

**1. Se connecter**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@camwater.cm",
    "password": "password123"
  }'
```

Réponse :
```json
{
  "success": true,
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Admin CAMWATER",
    "email": "admin@camwater.cm"
  }
}
```

**2. Utiliser le token**

Pour les routes protégées, ajouter le header :
```
Authorization: Bearer 1|abc123...
```

### Exemples de requêtes

**Créer un abonné** (protégé)
```bash
curl -X POST http://localhost:8000/api/abonnes \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Kamga",
    "prenom": "Jean",
    "ville": "Yaoundé",
    "quartier": "Bastos",
    "numero_compteur": "CPT-YAO-001",
    "type_abonnement": "Domestique"
  }'
```

**Générer une facture** (protégé)
```bash
curl -X POST http://localhost:8000/api/factures/generer \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "abonne_id": 1,
    "consommation": 15.5
  }'
```

**Consulter les abonnés** (public)
```bash
curl http://localhost:8000/api/abonnes
```

Voir [TEST_API_SECURISEE.md](TEST_API_SECURISEE.md) pour tous les exemples.

##  Tests

### Lancer tous les tests
```bash
php artisan test
```

### Lancer des tests spécifiques
```bash
# Tests d'authentification
php artisan test --filter=AuthenticationTest

# Tests des routes sécurisées
php artisan test --filter=SecuredRoutesTest

# Tests de pagination
php artisan test --filter=PaginationTest

# Tests de cache
php artisan test --filter=CacheTest
```

### Résultats attendus
```
Tests:    61 passed (234 assertions)
Duration: ~4s
```

##  Documentation

### Documentation principale

| Document | Description |
|----------|-------------|
| [SECURITE_API.md](SECURITE_API.md) | Guide complet de sécurité et d'utilisation |
| [TEST_API_SECURISEE.md](TEST_API_SECURISEE.md) | Exemples de requêtes cURL |
| [RESUME_SECURISATION.md](RESUME_SECURISATION.md) | Résumé des travaux effectués |
| [INDEX_SECURITE.md](INDEX_SECURITE.md) | Index de toute la documentation |

### Documentation MongoDB

| Document | Description |
|----------|-------------|
| [MONGODB_INTEGRATION.md](MONGODB_INTEGRATION.md) | Guide d'intégration MongoDB |
| [MONGODB_INSTALLATION.md](MONGODB_INSTALLATION.md) | Installation de MongoDB |
| [MONGODB_COMMANDES.md](MONGODB_COMMANDES.md) | Commandes MongoDB utiles |

##  Sécurité

### Routes publiques (sans authentification)
- `GET /api/abonnes` - Liste des abonnés
- `GET /api/abonnes/{id}` - Détails d'un abonné
- `GET /api/factures` - Liste des factures
- `GET /api/factures/{id}` - Détails d'une facture
- `POST /api/login` - Connexion

### Routes protégées (token requis)
- `POST /api/abonnes` - Créer un abonné
- `PUT /api/abonnes/{id}` - Modifier un abonné
- `DELETE /api/abonnes/{id}` - Supprimer un abonné
- `POST /api/factures/generer` - Générer une facture
- `PUT /api/factures/{id}` - Modifier une facture
- `DELETE /api/factures/{id}` - Supprimer une facture
- `POST /api/logout` - Déconnexion
- `GET /api/me` - Informations utilisateur

### Utilisateurs de test

| admin@camwater.cm | password123 | Admin |
| operateur@camwater.cm | password123 | Opérateur |



##  Changelog

### Version 1.0.0 (Mars 2026)
- ✅ API REST complète (CRUD abonnés, factures, réclamations)
- ✅ Authentification Laravel Sanctum
- ✅ Système de cache avec invalidation automatique
- ✅ Pagination des résultats
- ✅ Logs MongoDB
- ✅ 61 tests automatisés (100% de réussite)
- ✅ Documentation complète

##  Contributeurs

- **Noutong Naelle**


**Version :** 1.0.0  
**Date :** Mars 2026  
**Statut :** ✅ Production Ready
