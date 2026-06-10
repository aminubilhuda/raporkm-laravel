#!/bin/bash
set -e

APP_DIR="/www/wwwroot/km.smkabdinegara.sch.id"
LOG_FILE="$APP_DIR/storage/logs/deploy.log"
PHP_BIN="/www/server/php/83/bin/php"

echo "========================================" >> "$LOG_FILE"
echo "Deploy started: $(date '+%Y-%m-%d %H:%M:%S')" >> "$LOG_FILE"
echo "========================================" >> "$LOG_FILE"

cd "$APP_DIR"

# Maintenance mode ON
$PHP_BIN artisan down --retry=30 2>> "$LOG_FILE" || true

# Pull latest code
echo "[1/8] Git pull..." >> "$LOG_FILE"
git pull origin master 2>> "$LOG_FILE"

# Install PHP dependencies
echo "[2/8] Composer install..." >> "$LOG_FILE"
composer install --no-dev --optimize-autoloader --no-interaction 2>> "$LOG_FILE"

# Install Node dependencies dan build assets
echo "[3/8] npm ci + build..." >> "$LOG_FILE"
npm ci --production=false 2>> "$LOG_FILE"
npm run build 2>> "$LOG_FILE"

# Run migrations
echo "[4/8] Migrate..." >> "$LOG_FILE"
$PHP_BIN artisan migrate --force 2>> "$LOG_FILE"

# Cache config, routes, views
echo "[5/8] Cache..." >> "$LOG_FILE"
$PHP_BIN artisan config:cache 2>> "$LOG_FILE"
$PHP_BIN artisan route:cache 2>> "$LOG_FILE"
$PHP_BIN artisan view:cache 2>> "$LOG_FILE"

# Storage link
echo "[6/8] Storage link..." >> "$LOG_FILE"
$PHP_BIN artisan storage:link 2>> "$LOG_FILE" || true

# Restart queue worker
echo "[7/8] Restart queue worker..." >> "$LOG_FILE"
systemctl restart raporkm-queue-worker 2>> "$LOG_FILE" || true

# Fix permissions
echo "[8/8] Fix permissions..." >> "$LOG_FILE"
chown -R www:www "$APP_DIR"
chmod -R 755 "$APP_DIR"
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

# Maintenance mode OFF
$PHP_BIN artisan up 2>> "$LOG_FILE"

echo "Deploy finished: $(date '+%Y-%m-%d %H:%M:%S')" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
