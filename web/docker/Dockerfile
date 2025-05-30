# Stage 1: build PHP dependencies
FROM composer:2 AS builder
WORKDIR /app
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype=/usr/include/freetype2 --with-jpeg \
    && docker-php-ext-install gd
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --prefer-dist --no-scripts

# Stage 2: runtime image
FROM dunglas/frankenphp

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql \
    gd

# Copy application code and built vendor
WORKDIR /app
COPY --from=builder /app/vendor ./vendor
COPY . /app

# Copy custom Caddyfile to standard location
COPY ./docker/Caddyfile /etc/frankenphp/Caddyfile

# Set development PHP configuration
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Run the application using PHP Octane with FrankenPHP
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80", "--caddyfile=/etc/frankenphp/Caddyfile"]

EXPOSE 80