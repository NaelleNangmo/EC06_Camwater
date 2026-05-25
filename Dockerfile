# ─────────────────────────────────────────────────────────────────────────────
# Stage 1 — dépendances Composer (builder)
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS builder

# Extensions système requises
RUN apk add --no-cache \
        git \
        curl \
        unzip \
        libpng-dev \
        libxml2-dev \
        oniguruma-dev \
        sqlite-dev \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        pcntl

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier uniquement les fichiers de dépendances d'abord (cache layer)
COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --ignore-platform-req=ext-mongodb \
        --prefer-dist

# Copier le reste du code
COPY . .

RUN composer dump-autoload --optimize --ignore-platform-req=ext-mongodb

# ─────────────────────────────────────────────────────────────────────────────
# Stage 2 — image de production
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="CamwaterApp Team"
LABEL org.opencontainers.image.source="https://github.com/NaelleNangmo/EC06_Camwater"

# Extensions système
RUN apk add --no-cache \
        libpng-dev \
        libxml2-dev \
        oniguruma-dev \
        sqlite-dev \
        nginx \
        supervisor \
        curl \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        pcntl \
        opcache

# Configuration PHP optimisée pour la production
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configuration Supervisor (gère nginx + php-fpm)
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copier l'application depuis le builder
COPY --from=builder --chown=www-data:www-data /app .

# Créer les répertoires de stockage et fixer les permissions
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views,testing} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Script d'entrée
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ─────────────────────────────────────────────────────────────────────────────
# Stage 3 — image de test (CI)
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.2-cli-alpine AS testing

RUN apk add --no-cache \
        git \
        curl \
        unzip \
        libpng-dev \
        libxml2-dev \
        oniguruma-dev \
        sqlite-dev \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        pcntl

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
        --no-scripts \
        --ignore-platform-req=ext-mongodb \
        --prefer-dist

COPY . .
RUN composer dump-autoload --ignore-platform-req=ext-mongodb

CMD ["php", "artisan", "test"]
