# ── Stage 1: Node – build frontend assets ────────────────────────────────────
FROM node:20-alpine AS node_build

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ── Stage 2: PHP – production image ──────────────────────────────────────────
FROM php:8.4-cli-alpine

# Install system dependencies + PHP extensions
RUN apk add --no-cache \
        bash \
        curl \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        zip \
    && docker-php-ext-install \
        bcmath \
        mbstring \
        pdo \
        pdo_pgsql \
        zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy PHP source
COPY . .

# Copy built frontend assets from Node stage
COPY --from=node_build /app/public/build ./public/build

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Storage permissions
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=8080
