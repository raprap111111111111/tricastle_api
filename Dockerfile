FROM php:8.4-cli

# Install system dependencies & extensions
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip gd bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependencies for production
RUN composer install --no-dev --optimize-autoloader

# Run database migrations on boot, then start Laravel on port 10000
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000
