FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    ca-certificates \
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

# UPDATED: 
# 1. Clear & cache config first
# 2. Run migrations
# 3. Create Passport keys & clients non-interactively (--force / --no-interaction)
# 4. Start Laravel server
CMD ["sh", "-c", "php artisan config:clear && php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan passport:keys --force && php artisan passport:client --personal --name='Personal Access Client' --no-interaction && php artisan passport:client --password --name='Password Grant Client' --provider='users' --no-interaction && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]