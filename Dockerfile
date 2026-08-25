FROM php:8.4-cli

# Install system dependencies & PHP extensions
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

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy Laravel application
COPY . .

# Increase Composer timeout for slow network downloads
ENV COMPOSER_PROCESS_TIMEOUT=600

# Install production dependencies
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Run migrations, passport setup, then start web server safely
CMD ["sh", "-c", "php artisan migrate --force && php artisan passport:keys --force && php artisan passport:install --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]