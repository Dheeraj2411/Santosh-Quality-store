FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm
RUN apt-get update && apt-get install -y nodejs npm
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev 2>/dev/null || \
    (composer update --no-interaction && composer install --no-interaction)

# Install Node dependencies and build
RUN npm install --legacy-peer-deps && npm run build

# Set storage permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD sh -c "php artisan storage:link --force 2>/dev/null; php artisan serve --host=0.0.0.0 --port=8000"
