# Use the official FrankenPHP image as the base
FROM dunglas/frankenphp

# Install necessary system dependencies for PostgreSQL and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql

# Copy your application files to the container
COPY . /app

# Set the working directory
WORKDIR /app

# Install PHP dependencies for Laravel
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Run the application using PHP Octane with FrankenPHP
ENTRYPOINT ["php", "artisan", "octane:frankenphp"]