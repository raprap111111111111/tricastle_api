FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        gd \
        bcmath \
        pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

ENV COMPOSER_PROCESS_TIMEOUT=600

RUN composer install \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

CMD ["sh", "-c", "php artisan migrate --force && php artisan passport:keys --force && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]