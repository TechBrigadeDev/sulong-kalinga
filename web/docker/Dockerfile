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
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    cron \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql \
    gd

# Set up the cron job for Laravel Scheduler
RUN (crontab -l ; echo "* * * * * cd /app && php artisan schedule:run >> /dev/null 2>&1") | crontab -

WORKDIR /app
COPY --from=builder /app/vendor ./vendor
COPY . /app

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8000

CMD ["/start.sh"]
