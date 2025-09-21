FROM php:8.3-fpm

WORKDIR /var/www/html

# -------------------------------------------------------------------
# 1) Install system dependencies
# -------------------------------------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------------------------------------------
# 2) Install PHP extensions
# -------------------------------------------------------------------
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# -------------------------------------------------------------------
# 3) Install Redis via PECL
# -------------------------------------------------------------------
RUN pecl install redis \
    && docker-php-ext-enable redis

# -------------------------------------------------------------------
# 4) Install Composer
# -------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# -------------------------------------------------------------------
# 5) Copy only composer files first (for caching)
# -------------------------------------------------------------------
COPY composer.json composer.lock ./

# -------------------------------------------------------------------
# 6) Copy helpers, artisan, and bootstrap early (needed for autoload + package:discover)
# -------------------------------------------------------------------
COPY app/Helpers ./app/Helpers
COPY artisan ./
COPY bootstrap ./bootstrap

# -------------------------------------------------------------------
# 7) Create Laravel cache dirs before running composer install
# -------------------------------------------------------------------
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p bootstrap/cache

# -------------------------------------------------------------------
# 8) Install PHP dependencies (cached unless composer.json changes)
# -------------------------------------------------------------------
#RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# -------------------------------------------------------------------
# 9) Now copy the rest of the application
# -------------------------------------------------------------------
COPY --chown=www-data:www-data . .

# -------------------------------------------------------------------
# 10) Set permissions for Laravel
# -------------------------------------------------------------------
RUN chmod -R 775 storage bootstrap/cache

# -------------------------------------------------------------------
# 11) Expose PHP-FPM port
# -------------------------------------------------------------------
EXPOSE 9000
