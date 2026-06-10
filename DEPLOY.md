# Tutorial Auto-Deploy: GitHub Actions + SSH

Tutorial lengkap untuk setup auto-deploy aplikasi E-Rapor KM ke server aaPanel menggunakan GitHub Actions + SSH.

**Alur Kerja:**

```
Local: git push origin master
           |
GitHub Actions: build frontend (Vite)
           |
SSH ke server aaPanel (port 2222)
           |
Server: git pull + composer install + migrate + cache
           |
Aplikasi live otomatis terupdate
```

---

## Prasyarat

### Di Server (aaPanel)

- PHP 8.3+ (dengan extension: mbstring, xml, curl, mysql, zip, gd, bcmath)
- Composer 2.x
- Node.js 20+ dan npm
- Git
- MySQL / MariaDB
- SSH akses root (port 2222)

### Di Local

- Git
- SSH client (bawaan Windows/Mac/Linux)

### Di GitHub

- Repository sudah ada (private atau public)
- Akses ke Settings > Secrets and Variables > Actions

---

## Step 1: Generate SSH Key Pair

### 1.1 Generate Key di Komputer Local

Buka terminal/PowerShell di komputer local:

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/deploy_raporkm
```

Tekan Enter 2x (tanpa passphrase).

Akan terbuat 2 file:
- `~/.ssh/deploy_raporkm` (private key) -- untuk GitHub Secret
- `~/.ssh/deploy_raporkm.pub` (public key) -- untuk server

### 1.2 Copy Public Key ke Server

```bash
ssh -p 2222 root@IP_SERVER_KAMU "mkdir -p ~/.ssh && chmod 700 ~/.ssh"
```

```bash
cat ~/.ssh/deploy_raporkm.pub | ssh -p 2222 root@IP_SERVER_KAMU "cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
```

### 1.3 Test Koneksi SSH

```bash
ssh -p 2222 -i ~/.ssh/deploy_raporkm root@IP_SERVER_KAMU "echo 'SSH berhasil!'"
```

Jika muncul `SSH berhasil!`, lanjut ke step berikutnya.

---

## Step 2: Setup Server (Pertama Kali)

### 2.1 Clone Repository ke Server

SSH ke server:

```bash
ssh -p 2222 root@IP_SERVER_KAMU
```

Clone repo:

```bash
cd /www/wwwroot
git clone https://github.com/USERNAME/REPO_NAME.git km.smkabdinegara.sch.id
```

Atau jika folder sudah ada dari aaPanel:

```bash
cd /www/wwwroot/km.smkabdinegara.sch.id
git init
git remote add origin https://github.com/USERNAME/REPO_NAME.git
git fetch origin
git checkout -t origin/master
```

### 2.2 Install Dependencies

```bash
cd /www/wwwroot/km.smkabdinegara.sch.id

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies dan build
npm ci
npm run build
```

### 2.3 Setup Environment Production

```bash
cp .env.example .env
```

Edit file `.env` sesuai production:

```bash
nano .env
```

Isi yang perlu diubah:

```ini
APP_NAME="E-Rapor KM"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://km.smkabdinegara.sch.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=raporkm_production
DB_USERNAME=raporkm_user
DB_PASSWORD=PASSWORD_KUAT_KAMU

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Generate app key:

```bash
php artisan key:generate
```

### 2.4 Setup Database

```bash
php artisan migrate --force
php artisan storage:link
```

### 2.5 Set Permissions

```bash
chown -R www:www /www/wwwroot/km.smkabdinegara.sch.id
chmod -R 755 /www/wwwroot/km.smkabdinegara.sch.id
chmod -R 775 /www/wwwroot/km.smkabdinegara.sch.id/storage
chmod -R 775 /www/wwwroot/km.smkabdinegara.sch.id/bootstrap/cache
```

> **Catatan:** Di aaPanel, user web biasanya `www`. Cek dengan `ls -la /www/wwwroot/` untuk memastikan.

### 2.6 Cache Config (Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2.7 Konfigurasi aaPanel

1. Buka aaPanel > Website > Add Site
2. Domain: `km.smkabdinegara.sch.id`
3. Root Directory: `/www/wwwroot/km.smkabdinegara.sch.id/public`
4. PHP Version: 8.3
5. Setup SSL via Cloudflare (DNS only atau Full Strict)

**Nginx Config** - tambahkan di dalam block `server`:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ /\.(?!well-known).* {
    deny all;
}
```

---

## Step 3: Buat Deploy Script di Server

SSH ke server dan buat file deploy script:

```bash
nano /www/wwwroot/km.smkabdinegara.sch.id/deploy.sh
```

Isi dengan:

```bash
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
echo "[1/7] Git pull..." >> "$LOG_FILE"
git pull origin master 2>> "$LOG_FILE"

# Install PHP dependencies
echo "[2/7] Composer install..." >> "$LOG_FILE"
composer install --no-dev --optimize-autoloader --no-interaction 2>> "$LOG_FILE"

# Install Node dependencies dan build assets
echo "[3/7] npm ci + build..." >> "$LOG_FILE"
npm ci --production=false 2>> "$LOG_FILE"
npm run build 2>> "$LOG_FILE"

# Run migrations
echo "[4/7] Migrate..." >> "$LOG_FILE"
$PHP_BIN artisan migrate --force 2>> "$LOG_FILE"

# Cache config, routes, views
echo "[5/7] Cache..." >> "$LOG_FILE"
$PHP_BIN artisan config:cache 2>> "$LOG_FILE"
$PHP_BIN artisan route:cache 2>> "$LOG_FILE"
$PHP_BIN artisan view:cache 2>> "$LOG_FILE"

# Storage link
echo "[6/7] Storage link..." >> "$LOG_FILE"
$PHP_BIN artisan storage:link 2>> "$LOG_FILE" || true

# Fix permissions
echo "[7/7] Fix permissions..." >> "$LOG_FILE"
chown -R www:www "$APP_DIR"
chmod -R 755 "$APP_DIR"
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"

# Maintenance mode OFF
$PHP_BIN artisan up 2>> "$LOG_FILE"

echo "Deploy finished: $(date '+%Y-%m-%d %H:%M:%S')" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"
```

Set executable:

```bash
chmod +x /www/wwwroot/km.smkabdinegara.sch.id/deploy.sh
```

> **Catatan:** Path PHP (`/www/server/php/83/bin/php`) bisa berbeda di server kamu.
> Cek dengan: `which php` atau `ls /www/server/php/`

---

## Step 4: Konfigurasi GitHub Secrets

1. Buka repository di GitHub
2. Klik **Settings** > **Secrets and variables** > **Actions**
3. Klik **New repository secret**
4. Tambahkan secret berikut satu per satu:

| Secret Name | Value | Contoh |
|-------------|-------|--------|
| `SSH_HOST` | IP server aaPanel | `103.xxx.xxx.xxx` |
| `SSH_USERNAME` | Username SSH | `root` |
| `SSH_PORT` | Port SSH | `2222` |
| `SSH_PRIVATE_KEY` | Isi file private key | (lihat cara di bawah) |
| `DEPLOY_PATH` | Path aplikasi di server | `/www/wwwroot/km.smkabdinegara.sch.id` |

### Cara Copy Private Key

Di komputer local, jalankan:

**Windows (PowerShell):**

```powershell
Get-Content ~/.ssh/deploy_raporkm
```

**Mac/Linux:**

```bash
cat ~/.ssh/deploy_raporkm
```

Copy **SEMUA** output termasuk baris `-----BEGIN OPENSSH PRIVATE KEY-----` dan `-----END OPENSSH PRIVATE KEY-----`.

Paste ke value secret `SSH_PRIVATE_KEY` di GitHub.

---

## Step 5: Buat GitHub Actions Workflow

File ini sudah dibuat otomatis di repo: `.github/workflows/deploy.yml`

Jika belum ada, buat manual:

1. Di repo local, buat folder `.github/workflows/`
2. Buat file `deploy.yml` di dalamnya (lihat file di repo)

**Yang dilakukan workflow ini:**

1. Trigger otomatis saat push ke branch `master`
2. Build frontend assets (Vite) di GitHub Actions
3. SSH ke server aaPanel
4. Jalankan `deploy.sh`

**Trigger manual juga bisa:** Buka tab Actions > Deploy to Production > Run workflow

---

## Step 6: Testing Deploy Pertama Kali

### 6.1 Push Perubahan

```bash
git add .
git commit -m "setup: auto-deploy via GitHub Actions"
git push origin master
```

### 6.2 Monitor di GitHub

1. Buka repository di GitHub
2. Klik tab **Actions**
3. Klik workflow run yang baru muncul
4. Pantau progress (biasanya 1-3 menit)

### 6.3 Verifikasi di Server

SSH ke server dan cek:

```bash
# Cek log deploy
tail -50 /www/wwwroot/km.smkabdinegara.sch.id/storage/logs/deploy.log

# Cek apakah maintenance mode sudah off
curl -I https://km.smkabdinegara.sch.id
```

### 6.4 Test di Browser

Buka `https://km.smkabdinegara.sch.id` dan pastikan aplikasi berjalan normal.

---

## Troubleshooting

### Error: Permission denied (publickey)

**Penyebab:** SSH key tidak terdaftar di server.

**Solusi:**

```bash
# Di server, cek authorized_keys
cat ~/.ssh/authorized_keys

# Pastikan public key ada di sana
# Jika tidak, tambahkan manual:
echo "ISI_PUBLIC_KEY" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

### Error: Host key verification failed

**Penyebab:** Server belum dikenal oleh GitHub Actions runner.

**Solusi:** Workflow sudah menggunakan `StrictHostKeyChecking=no` untuk mengatasi ini.

### Error: Composer not found

**Penyebab:** Composer tidak ada di PATH server.

**Solusi:**

```bash
# Cek lokasi composer
which composer

# Jika tidak ada, install:
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### Error: npm/node not found

**Penyebab:** Node.js belum terinstall di server.

**Solusi:** Install via aaPanel > App Store > Node.js Manager

Atau manual:

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs
```

### Error: Migration failed

**Penyebab:** Database belum dibuat atau credentials salah.

**Solusi:**

```bash
# Cek koneksi database
cd /www/wwwroot/km.smkabdinegara.sch.id
php artisan db:show

# Jika error, cek .env
nano .env
```

### Error: Vite manifest not found

**Penyebab:** Frontend belum di-build.

**Solusi:**

```bash
cd /www/wwwroot/km.smkabdinegara.sch.id
npm ci
npm run build
```

### Rollback ke Versi Sebelumnya

Jika deploy bermasalah, SSH ke server:

```bash
cd /www/wwwroot/km.smkabdinegara.sch.id

# Lihat commit terakhir
git log --oneline -5

# Rollback ke commit tertentu
git checkout COMMIT_HASH -- .
php artisan migrate:rollback --force
php artisan config:cache
php artisan up
```

---

## Tips Tambahan

### Jangan Push Secret ke GitHub

Pastikan file-file berikut ada di `.gitignore`:

```
.env
.env.backup
.env.production
```

### Monitoring Deploy

Cek log deploy terakhir di server:

```bash
tail -f /www/wwwroot/km.smkabdinegara.sch.id/storage/logs/deploy.log
```

### Notifikasi Deploy (Opsional)

Bisa tambahkan step di workflow untuk kirim notifikasi via:
- **Telegram Bot** -- kirim pesan saat deploy sukses/gagal
- **Email** -- via SMTP
- **Discord Webhook** -- kirim ke channel dev

### Cloudflare ZeroTrust

Karena ZeroTrust hanya protect domain utama (bukan SSH), workflow GitHub Actions bisa langsung SSH ke IP server tanpa masalah. Pastikan:
- Port 2222 dibuka di firewall server (aaPanel > Security > Firewall)
- Cloudflare proxy mode: DNS only (grey cloud) tidak mempengaruhi SSH

### Jadwal Maintenance

Untuk update yang besar (migration destructive), sebaiknya:
1. SSH ke server manual
2. `php artisan down --retry=60`
3. Backup database: `mysqldump -u root -p raporkm_production > backup.sql`
4. Jalankan deploy
5. `php artisan up`

---

## Referensi

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [aaPanel Documentation](https://www.aapanel.com/new/download.html)
- [Cloudflare ZeroTrust](https://developers.cloudflare.com/cloudflare-one/)
