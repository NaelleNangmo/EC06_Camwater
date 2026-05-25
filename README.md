# Kaba-Delivery API

API REST de gestion des livraisons  projet EC06 Camwater.

## Prerequis
- PHP 8.2+
- Composer
- MySQL 8 ou SQLite (tests)
- Docker (optionnel)

## Installation

```bash
git clone https://github.com/NaelleNangmo/EC06_Camwater.git
cd EC06_Camwater
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Endpoints

| Methode | Route | Auth | Description |
|---|---|---|---|
| POST | /api/auth/login | Non | Connexion livreur |
| POST | /api/auth/logout | Oui | Deconnexion |
| GET | /api/livraisons | Oui | Liste des livraisons |
| POST | /api/livraisons | Oui | Creer une livraison |
| GET | /api/livraisons/{id} | Oui | Detail livraison |
| GET | /api/livraisons/{id}/suivi | Oui | Suivi en temps reel |

## Tests

```bash
php artisan test
```

## Docker

```bash
cp .env.example .env
docker compose up -d
docker compose exec app php artisan migrate --seed
```

## CI/CD

Le pipeline GitHub Actions execute automatiquement :
- Les tests PHPUnit
- Le lint (Laravel Pint)
- L analyse qualite SonarCloud
- Le build Docker

## Contribution

Voir [CONTRIBUTING.md](CONTRIBUTING.md) pour les conventions de branches et commits.
