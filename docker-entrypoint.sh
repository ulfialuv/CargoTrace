#!/bin/sh
set -e

# Laravel membaca DB_URL, sedangkan EthioDeploy memberi DATABASE_URL
if [ -n "$DATABASE_URL" ] && [ -z "$DB_URL" ]; then
  export DB_URL="$DATABASE_URL"
fi


PORT="${PORT:-3000}"
cd /var/www/html

log()  { echo "[entrypoint] $*"; }
fail() { echo "[entrypoint][ERROR] $*" >&2; exit 1; }

log "CargoTrace mulai (APP_ENV=${APP_ENV:-belum-diisi}, PORT=${PORT}, DB_CONNECTION=${DB_CONNECTION:-belum-diisi})"

# --- Apache mendengarkan port yang diberikan platform ---
sed -ri "s/Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# --- Pemeriksaan konfigurasi wajib ---
[ -n "${APP_KEY:-}" ] || fail "APP_KEY belum diisi. Di komputermu jalankan 'php artisan key:generate --show', lalu isi hasilnya sebagai environment variable APP_KEY."

if [ "${DB_CONNECTION:-}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    mkdir -p "$(dirname "$DB_FILE")"
    touch "$DB_FILE"
    chown www-data:www-data "$DB_FILE" "$(dirname "$DB_FILE")"
    log "Memakai SQLite di $DB_FILE (data hilang saat container dibuat ulang)"
else
    [ -n "${DB_HOST:-}" ]     || fail "DB_HOST belum diisi. Isi DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD dari penyedia database MySQL."
    [ -n "${DB_DATABASE:-}" ] || fail "DB_DATABASE belum diisi."
fi

# --- Persiapan Laravel ---
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache || log "route:cache dilewati (ada route berbentuk closure), tidak masalah"
php artisan view:cache  || log "view:cache dilewati"

# --- Migrasi database (dicoba ulang bila database belum siap) ---
tries=0
until php artisan migrate --force; do
    tries=$((tries + 1))
    [ "$tries" -lt 5 ] || fail "Migrasi gagal 5 kali. Periksa DB_HOST / DB_PORT / DB_USERNAME / DB_PASSWORD, dan MYSQL_ATTR_SSL_CA bila database mewajibkan TLS. Detail ada di baris di atas."
    log "Migrasi gagal, mencoba lagi dalam 5 detik ($tries/5)..."
    sleep 5
done

# --- Data demo (opsional, aman diulang) ---
if [ "${RUN_SEED:-false}" = "true" ]; then
    log "Mengisi data demo (RUN_SEED=true)"
    php artisan db:seed --force || log "Seeding gagal (aplikasi tetap dijalankan)"
fi

# Berkas yang dibuat artisan (root) harus bisa ditulis Apache (www-data)
chown -R www-data:www-data storage bootstrap/cache

log "Siap. Menjalankan Apache di port ${PORT}"
exec apache2-foreground
