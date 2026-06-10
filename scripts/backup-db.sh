#!/bin/bash
set -e

# ── E-Rapor KM — Database Backup Script ──
# Run daily via cron: 0 3 * * * /www/wwwroot/km.smkabdinegara.sch.id/scripts/backup-db.sh

APP_DIR="/www/wwwroot/km.smkabdinegara.sch.id"
BACKUP_DIR="/www/backup/raporkm"
LOG_FILE="$APP_DIR/storage/logs/backup.log"
RETENTION_DAYS=7
DATE=$(date +%F_%H-%M-%S)

# Load DB credentials from .env
if [ -f "$APP_DIR/.env" ]; then
    export $(grep -E '^DB_' "$APP_DIR/.env" | xargs)
fi

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-raporkm_production}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

mkdir -p "$BACKUP_DIR"

echo "========================================" >> "$LOG_FILE"
echo "Backup started: $(date '+%Y-%m-%d %H:%M:%S')" >> "$LOG_FILE"
echo "========================================" >> "$LOG_FILE"

# Dump database
echo "[1/2] Dumping database: $DB_DATABASE..." >> "$LOG_FILE"
mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
    --single-transaction --quick --lock-tables=false \
    "$DB_DATABASE" | gzip > "$BACKUP_DIR/db_$DATE.sql.gz" 2>> "$LOG_FILE"

if [ $? -eq 0 ]; then
    echo "[1/2] ✓ Dump successful: db_$DATE.sql.gz" >> "$LOG_FILE"
else
    echo "[1/2] ✗ Dump FAILED" >> "$LOG_FILE"
    exit 1
fi

# Delete backups older than RETENTION_DAYS
echo "[2/2] Cleaning backups older than $RETENTION_DAYS days..." >> "$LOG_FILE"
find "$BACKUP_DIR" -name "db_*.sql.gz" -mtime +$RETENTION_DAYS -delete 2>> "$LOG_FILE"

# Show backup size
BACKUP_SIZE=$(du -sh "$BACKUP_DIR/db_$DATE.sql.gz" | cut -f1)
echo "[Done] Backup size: $BACKUP_SIZE" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"

echo "Backup finished: $(date '+%Y-%m-%d %H:%M:%S')" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
