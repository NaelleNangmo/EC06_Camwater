# Stage 1  builder
FROM php:8.2-fpm-alpine AS builder
RUN apk add --no-cache git curl unzip sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring bcmath pcntl
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

# Stage 2  production
FROM php:8.2-fpm-alpine AS production
LABEL maintainer="Kaba-Delivery Team"
RUN apk add --no-cache nginx supervisor sqlite-dev curl \
    && docker-php-ext-install pdo pdo_sqlite mbstring bcmath pcntl opcache
WORKDIR /var/www/html
COPY --from=builder --chown=www-data:www-data /app .
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=10s CMD curl -f http://localhost/up || exit 1
CMD ["php-fpm"]
