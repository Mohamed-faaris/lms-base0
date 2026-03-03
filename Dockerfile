FROM php:8.4-fpm

ARG WWWUSER=1000
ARG WWWGROUP=1000

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache \
        zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN npm install -g npm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY package.json package-lock.json* ./

RUN npm install

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan key:generate --force

RUN chmod -R 755 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
