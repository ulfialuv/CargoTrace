#!/bin/sh
set -e

# Apache dengarkan port dari platform (default 8080)
PORT="${PORT:-8080}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Cache config/route/view untuk production
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

# Jalankan migrasi (butuh DB sudah tersambung)
php artisan migrate --force

exec apache2-foreground
