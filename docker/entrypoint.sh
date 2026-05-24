#!/bin/sh
set -e

echo "==> CamwaterApp — démarrage du conteneur"

# Générer la clé si absente
if [ -z "$APP_KEY" ]; then
    echo "==> Génération de APP_KEY..."
    php artisan key:generate --force
fi

# Optimisations Laravel (production uniquement)
if [ "$APP_ENV" = "production" ]; then
    echo "==> Optimisation du cache Laravel..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Migrations
echo "==> Exécution des migrations..."
php artisan migrate --force --no-interaction

# Permissions storage
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Démarrage de Supervisor (nginx + php-fpm + queue)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
