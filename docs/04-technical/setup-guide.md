# Setup Guide - Petty Cash App

## Overview

Dokumen ini merupakan panduan setup development environment untuk Petty Cash App yang bertujuan untuk membantu developer baru dalam mempersiapkan environment development, yaitu: mencakup instalasi dependencies, konfigurasi, dan langkah-langkah untuk menjalankan aplikasi secara lokal.

## Prerequisites

Sebelum memulai, pastikan sistem Anda memiliki software berikut:

| Software | Version | Download |
|----------|---------|----------|
| PHP | 8.4+ | [php.net](https://php.net) |
| Composer | 2.x | [getcomposer.org](https://getcomposer.org) |
| Node.js | 20+ | [nodejs.org](https://nodejs.org) |
| Yarn | 1.22+ | [yarnpkg.com](https://yarnpkg.com) |
| Git | 2.x | [git-scm.com](https://git-scm.com) |

### PHP Extensions Required

Pastikan PHP extensions berikut sudah terinstall:

```bash
# Check installed extensions
php -m

# Required extensions:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_SQLite
- Tokenizer
- XML
```

## Quick Start

### 1. Clone Repository

```bash
git clone https://github.com/your-repo/simple-petty-cash.git
cd simple-petty-cash
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
yarn install
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed default categories
php artisan db:seed --class=CategorySeeder
```

### 6. Generate Wayfinder Routes

```bash
php artisan wayfinder:generate
```

### 7. Start Development Servers

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
yarn dev
```

Aplikasi akan berjalan di `http://localhost:8000`

## Environment Configuration

### .env File

```env
APP_NAME="Petty Cash"
APP_ENV=local
APP_KEY=base64:your-generated-key-here
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database

# Queue (for future use)
QUEUE_CONNECTION=database
```

### Database Options

#### SQLite (Default - Development)

```env
DB_CONNECTION=sqlite
```

#### MySQL (Production)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=petty_cash
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### PostgreSQL (Alternative)

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=petty_cash
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## IDE Setup

### VS Code Extensions (Recommended)

```json
{
    "recommendations": [
        "Vue.volar",
        "bradlc.vscode-tailwindcss",
        "dbaeumer.vscode-eslint",
        "esbenp.prettier-vscode",
        "bmewburn.vscode-intelephense-client",
        "shufo.vscode-blade-formatter"
    ]
}
```

### PhpStorm Plugins

- Laravel IDE Helper
- Vue.js
- Tailwind CSS

## Common Commands

### Artisan Commands

```bash
# Run development server
php artisan serve

# Run migrations
php artisan migrate

# Fresh migration (drop all tables)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Generate Wayfinder types
php artisan wayfinder:generate

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Run specific test
php artisan test --filter=TransactionTest
```

### Yarn Commands

```bash
# Install dependencies
yarn install

# Run development server
yarn dev

# Build for production
yarn build

# Type check
yarn type-check

# Lint code
yarn lint

# Format code
yarn format
```

### Code Quality Commands

```bash
# PHP formatting
vendor/bin/pint

# PHP formatting (only changed files)
vendor/bin/pint --dirty

# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

## Troubleshooting

### Common Issues

#### 1. "Class not found" Error

```bash
# Regenerate autoload
composer dump-autoload

# Clear caches
php artisan optimize:clear
```

#### 2. Vite Manifest Error

```bash
# Build assets
yarn build

# Or run dev server
yarn dev
```

#### 3. Database Connection Error

```bash
# Check if SQLite file exists
ls -la database/database.sqlite

# Create if not exists
touch database/database.sqlite

# Reset database
php artisan migrate:fresh --seed
```

#### 4. Permission Denied

```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### 5. Node Modules Issues

```bash
# Remove and reinstall
rm -rf node_modules
rm yarn.lock
yarn install
```

### Debug Mode

Untuk debugging, set di `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Logging

Logs tersedia di `storage/logs/laravel.log`:

```bash
# Watch logs
tail -f storage/logs/laravel.log
```

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Tests

```bash
# By file
php artisan test tests/Feature/TransactionTest.php

# By method name
php artisan test --filter=test_user_can_create_transaction

# By class
php artisan test --filter=TransactionTest
```

### Database Testing

Tests menggunakan `RefreshDatabase` trait yang akan:
1. Migrate database fresh untuk setiap test
2. Rollback setelah test selesai

## Production Build

### Build Assets

```bash
# Build for production
yarn build
```

### Optimize Laravel

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Or optimize all
php artisan optimize
```

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate strong `APP_KEY`
- [ ] Configure production database
- [ ] Run migrations
- [ ] Build frontend assets
- [ ] Cache configurations
- [ ] Set proper file permissions
- [ ] Configure web server (Nginx/Apache)

---

*Panduan ini akan diperbarui sesuai dengan perubahan stack teknologi atau dependencies.*

