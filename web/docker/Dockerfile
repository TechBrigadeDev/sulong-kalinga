# Stage 1: build PHP dependencies
FROM composer:2 AS builder
WORKDIR /app
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype=/usr/include/freetype2 --with-jpeg \
    && docker-php-ext-install gd
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-scripts

# Stage 2: runtime image
FROM dunglas/frankenphp

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql

# Copy application code and built vendor
WORKDIR /app
COPY --from=builder /app/vendor ./vendor
COPY . /app

# Run the application using PHP Octane with FrankenPHP
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]

EXPOSE 8000