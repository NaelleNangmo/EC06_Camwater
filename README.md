# Kaba-Delivery API

API de gestion des livraisons pour le projet Kaba-Delivery.

## Stack
- PHP 8.2 / Laravel 12
- MySQL 8
- Docker

## Démarrage rapide
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Endpoints principaux

| Méthode | Route | Description |
|---|---|---|
| POST | /api/auth/login | Connexion livreur |
| GET | /api/livraisons | Liste des livraisons |
| POST | /api/livraisons | Créer une livraison |
| GET | /api/livraisons/{id}/suivi | Suivi en temps réel |

## Tests
```bash
php artisan test
```
