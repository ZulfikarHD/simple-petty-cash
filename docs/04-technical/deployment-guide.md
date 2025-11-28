# Deployment Guide - Petty Cash App

## Overview

Dokumen ini merupakan panduan deployment Petty Cash App ke environment production yang bertujuan untuk membantu tim dalam melakukan deployment aplikasi, yaitu: mencakup persiapan server, konfigurasi, dan langkah-langkah deployment yang aman.

---

## Server Requirements

### Minimum Specifications

| Resource | Minimum | Recommended |
|----------|---------|-------------|
| CPU | 1 Core | 2 Cores |
| RAM | 1 GB | 2 GB |
| Storage | 10 GB SSD | 20 GB SSD |
| OS | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |

### Software Requirements

| Software | Version | Purpose |
|----------|---------|---------|
| PHP | 8.4+ | Backend runtime |
| Composer | 2.x | PHP dependency manager |
| Node.js | 20+ | Build tools |
| Yarn | 1.22+ | JS dependency manager |
| Nginx | 1.18+ | Web server |
| SQLite/MySQL | - / 8.0+ | Database |
| Git | 2.x | Version control |

---

## Deployment Methods

### Method 1: Manual Deployment (VPS/Dedicated Server)

#### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx php8.4-fpm php8.4-cli php8.4-common \
    php8.4-mysql php8.4-sqlite3 php8.4-xml php8.4-curl php8.4-mbstring \
    php8.4-zip php8.4-bcmath php8.4-gd php8.4-intl \
    git unzip curl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js via NodeSource
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Yarn
npm install -g yarn
```

#### 2. Create Application Directory

```bash
# Create web directory
sudo mkdir -p /var/www/petty-cash
sudo chown -R $USER:www-data /var/www/petty-cash

# Clone repository
cd /var/www/petty-cash
git clone https://github.com/ZulfikarHD/simple-petty-cash.git .
```

#### 3. Install Dependencies

```bash
# Install PHP dependencies (production)
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build
yarn install
yarn build
```

#### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` untuk production:

```env
APP_NAME="Petty Cash"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://your-domain.com

# Database (SQLite untuk simple deployment)
DB_CONNECTION=sqlite
# Atau MySQL untuk production yang lebih robust
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=petty_cash
# DB_USERNAME=petty_cash_user
# DB_PASSWORD=secure_password_here

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

#### 5. Database Setup

```bash
# SQLite
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed default data
php artisan db:seed --class=CategorySeeder --force
```

#### 6. Set Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/petty-cash

# Set directory permissions
sudo find /var/www/petty-cash -type d -exec chmod 755 {} \;
sudo find /var/www/petty-cash -type f -exec chmod 644 {} \;

# Writable directories
sudo chmod -R 775 /var/www/petty-cash/storage
sudo chmod -R 775 /var/www/petty-cash/bootstrap/cache

# Storage link
php artisan storage:link
```

#### 7. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### 8. Configure Nginx

Create `/etc/nginx/sites-available/petty-cash`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/petty-cash/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/petty-cash /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 9. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is enabled by default
sudo systemctl status certbot.timer
```

---

### Method 2: Docker Deployment

#### docker-compose.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: petty-cash-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - petty-cash-network

  nginx:
    image: nginx:alpine
    container_name: petty-cash-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    networks:
      - petty-cash-network
    depends_on:
      - app

networks:
  petty-cash-network:
    driver: bridge
```

#### Dockerfile

```dockerfile
FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
```

---

### Method 3: Laravel Forge / Ploi

Jika menggunakan managed deployment service:

1. Connect repository GitHub
2. Set environment variables
3. Configure deployment script:

```bash
cd /home/forge/your-domain.com
git pull origin main
composer install --no-dev --optimize-autoloader
yarn install
yarn build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

---

## Post-Deployment

### Create Admin User

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Administrator',
    'email' => 'admin@your-domain.com',
    'password' => Hash::make('secure_password'),
    'is_admin' => true,
    'email_verified_at' => now(),
]);
```

### Health Check

Verify deployment dengan mengakses:

- `https://your-domain.com` - Halaman login
- `https://your-domain.com/up` - Health check endpoint

### Monitor Logs

```bash
# Laravel logs
tail -f /var/www/petty-cash/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM logs
tail -f /var/log/php8.4-fpm.log
```

---

## Update Deployment

### Zero-Downtime Update Script

Create `deploy.sh`:

```bash
#!/bin/bash
set -e

echo "🚀 Starting deployment..."

cd /var/www/petty-cash

# Enable maintenance mode
php artisan down --refresh=15

# Pull latest changes
git pull origin main

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install and build frontend
yarn install
yarn build

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queue workers (if any)
php artisan queue:restart

# Disable maintenance mode
php artisan up

echo "✅ Deployment completed!"
```

```bash
chmod +x deploy.sh
./deploy.sh
```

---

## Rollback Procedure

### Quick Rollback

```bash
cd /var/www/petty-cash

# Enable maintenance mode
php artisan down

# Rollback to previous commit
git reset --hard HEAD~1

# Reinstall dependencies
composer install --no-dev --optimize-autoloader
yarn install
yarn build

# Rollback last migration (if needed)
php artisan migrate:rollback

# Clear caches
php artisan optimize:clear

# Disable maintenance mode
php artisan up
```

---

## Security Checklist

### Pre-Deployment

- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secured
- [ ] File permissions set correctly
- [ ] `.env` file not accessible publicly

### Post-Deployment

- [ ] SSL certificate installed
- [ ] Security headers configured
- [ ] Firewall rules applied (80, 443 only)
- [ ] Regular backup scheduled
- [ ] Monitoring configured

### Recommended Security Headers

Add to Nginx config:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';" always;
```

---

## Backup Strategy

### Database Backup (SQLite)

```bash
# Create backup
cp /var/www/petty-cash/database/database.sqlite /backups/database-$(date +%Y%m%d).sqlite

# Automated daily backup (crontab)
0 2 * * * cp /var/www/petty-cash/database/database.sqlite /backups/database-$(date +\%Y\%m\%d).sqlite
```

### Database Backup (MySQL)

```bash
# Create backup
mysqldump -u petty_cash_user -p petty_cash > /backups/petty_cash-$(date +%Y%m%d).sql

# Automated daily backup (crontab)
0 2 * * * mysqldump -u petty_cash_user -pYOUR_PASSWORD petty_cash > /backups/petty_cash-$(date +\%Y\%m\%d).sql
```

### Storage Backup

```bash
# Backup receipts
tar -czvf /backups/receipts-$(date +%Y%m%d).tar.gz /var/www/petty-cash/storage/app/public/receipts
```

---

## Contact

Untuk bantuan deployment atau masalah teknis:

**Author:** Zulfikar Hidayatullah  
**Contact:** +62 857-1583-8733  
**Repository:** [GitHub](https://github.com/ZulfikarHD/simple-petty-cash)

---

*Dokumentasi ini terakhir diperbarui: November 2025*

