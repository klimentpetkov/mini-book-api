#!/usr/bin/env bash
set -e

cd /var/www/html

# Composer
if [ ! -d "vendor" ]; then
  composer install --no-interaction --prefer-dist
fi

# .env
if [ ! -f ".env" ]; then
  cp .env.example .env || true
fi

# App key
php artisan key:generate --force || true

# MiniInstall (MySQL, Passport, Seeders, Swagger)
if [ "$SKIP_MINI_INSTALL" != "1" ]; then
    echo "▶ Running mini:install (no --fresh)..."
    php artisan mini:install --no-swagger
else
    echo "⏭ Skipping mini:install due to SKIP_MINI_INSTALL=1"
fi

# Ensure correct permissions for storage
if [ -d "storage" ]; then
    chown -R www-data:www-data storage bootstrap/cache || true
    chmod -R 775 storage bootstrap/cache || true
fi

touch .phpunit.result.cache && chown www-data:www-data .phpunit.result.cache || true

php artisan l5-swagger:generate

touch /var/www/html/.phpunit.result.cache
chown www-data:www-data /var/www/html/.phpunit.result.cache
chmod 664 /var/www/html/.phpunit.result.cache

# Start primary process
exec "$@"
