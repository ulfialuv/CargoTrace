#!/bin/sh
set -e

# Gunakan port yang diberikan EthioDeploy
PORT="${PORT:-3000}"

# Atur Apache agar mendengarkan port tersebut
sed -ri "s/Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Cache konfigurasi Laravel
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

# Jalankan migrasi database
php artisan migrate --force

# Jalankan Apache
exec apache2-foreground
