#!/usr/bin/env bash

# Exit immediately if a command fails
set -o errexit

# ─── PHP dependencies ────────────────────────────────────────────────────────
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# ─── Node / frontend assets ──────────────────────────────────────────────────
echo "Installing Node dependencies and building assets..."
npm install
npm run build

# ─── Laravel bootstrap ───────────────────────────────────────────────────────
echo "Caching Laravel config, routes and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── Storage link (public disk) ──────────────────────────────────────────────
php artisan storage:link --force || true

# ─── Database migrations ─────────────────────────────────────────────────────
echo "Running migrations..."
php artisan migrate --force
