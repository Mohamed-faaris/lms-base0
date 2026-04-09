FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
COPY --from=vendor /app/vendor ./vendor

RUN npm ci

COPY resources resources
COPY public public
COPY vite.config.js ./

RUN npm run build

FROM php:8.4-cli-bookworm AS runtime

ARG WWWUSER=1000
ARG WWWGROUP=1000

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN groupadd --gid "${WWWGROUP}" laravel \
    && useradd --uid "${WWWUSER}" --gid laravel --create-home --shell /bin/bash laravel

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p \
        bootstrap/cache \
        storage/app \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && touch database/database.sqlite \
    && chown -R laravel:laravel /var/www/html \
    && chmod -R ug+rwx storage bootstrap/cache

USER laravel

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
