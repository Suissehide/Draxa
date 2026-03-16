# Stage 1: PHP dependencies
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock symfony.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-scripts \
    --no-interaction \
    --ignore-platform-reqs

# Stage 2: Node assets
FROM node:18-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY webpack.config.js ./
COPY assets/ assets/
RUN npm run build

# Stage 3: Production
FROM php:8.1-apache AS app

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_pgsql intl zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set DocumentRoot to Symfony public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Copy vendor
COPY --from=vendor /app/vendor vendor/

# Copy application
COPY . .

# Copy built assets
COPY --from=assets /app/public/build public/build/

# Warmup cache
RUN APP_ENV=prod php bin/console cache:warmup --no-debug

# Set permissions
RUN chown -R www-data:www-data var/ public/
