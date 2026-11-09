
# ==========================================
# STAGE 1: PHP + APACHE BASE
# ==========================================
FROM php:8.3-apache AS php-base

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        exif \
        pcntl \
        gd \
        zip \
        intl \
        opcache \
        curl \
        xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

# Configure Apache to serve Laravel's public directory
RUN sed -ri \
    's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf \
    && printf '\n<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' >> /etc/apache2/apache2.conf


# ==========================================
# STAGE 2: COMPOSER DEPENDENCIES
# ==========================================
FROM php-base AS composer-build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Install production dependencies without running Laravel scripts.
# No migrations or APP_KEY generation.
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts


# ==========================================
# STAGE 3: FRONTEND BUILD
# ==========================================
FROM node:22-alpine AS frontend-build

WORKDIR /app

COPY . .

RUN npm install --no-audit --no-fund \
    && npm run build


# ==========================================
# STAGE 4: FINAL APPLICATION
# ==========================================
FROM php-base AS production

WORKDIR /var/www/html

COPY . .

# Copy Composer dependencies
COPY --from=composer-build \
    /var/www/html/vendor \
    ./vendor

# Copy compiled Vite assets
COPY --from=frontend-build \
    /app/public/build \
    ./public/build

# Prepare Laravel writable directories
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
