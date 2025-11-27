# Database Schema - Petty Cash App

## Overview

Dokumen ini merupakan spesifikasi database schema untuk Petty Cash App yang bertujuan untuk mendefinisikan struktur tabel, relasi antar entitas, dan constraints yang diterapkan, yaitu: memberikan panduan teknis bagi developer dalam memahami model data aplikasi.

## Entity Relationship Diagram

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     users       │       │   categories    │       │   cash_funds    │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │       │ id (PK)         │       │ id (PK)         │
│ name            │       │ name            │       │ amount          │
│ email           │──┐    │ icon            │       │ note            │
│ password        │  │    │ color           │       │ fund_date       │
│ remember_token  │  │    │ is_default      │       │ user_id (FK)    │──┐
│ created_at      │  │    │ user_id (FK)    │──┐    │ created_at      │  │
│ updated_at      │  │    │ created_at      │  │    │ updated_at      │  │
└─────────────────┘  │    │ updated_at      │  │    └─────────────────┘  │
        │            │    └─────────────────┘  │            │            │
        │            │            │            │            │            │
        │    ┌───────┴────────────┴────────────┴────────────┴────────────┘
        │    │
        │    │    ┌─────────────────┐
        │    │    │  transactions   │
        │    │    ├─────────────────┤
        │    │    │ id (PK)         │
        └────┼───→│ user_id (FK)    │
             │    │ category_id (FK)│←─────────────────────────────────────┐
             │    │ amount          │                                      │
             │    │ description     │                                      │
             │    │ transaction_date│                                      │
             │    │ created_at      │                                      │
             │    │ updated_at      │                                      │
             │    └─────────────────┘                                      │
             │                                                             │
             └─────────────────────────────────────────────────────────────┘
```

## Table Definitions

### 1. users

Tabel `users` menyimpan informasi akun pengguna yang terdaftar dalam sistem.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Nama lengkap user |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Email untuk login |
| email_verified_at | TIMESTAMP | NULLABLE | Waktu verifikasi email |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| remember_token | VARCHAR(100) | NULLABLE | Token untuk "remember me" |
| two_factor_secret | TEXT | NULLABLE | Secret untuk 2FA |
| two_factor_recovery_codes | TEXT | NULLABLE | Recovery codes 2FA |
| two_factor_confirmed_at | TIMESTAMP | NULLABLE | Konfirmasi 2FA |
| created_at | TIMESTAMP | NOT NULL | Waktu pembuatan |
| updated_at | TIMESTAMP | NOT NULL | Waktu update terakhir |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE (`email`)

**Migration File:** `0001_01_01_000000_create_users_table.php`

### 2. categories

Tabel `categories` menyimpan kategori pengeluaran yang dapat digunakan untuk klasifikasi transaksi.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| name | VARCHAR(255) | NOT NULL | Nama kategori |
| icon | VARCHAR(255) | NULLABLE | Icon identifier (lucide) |
| color | VARCHAR(255) | NULLABLE | Hex color code |
| is_default | BOOLEAN | DEFAULT FALSE | Kategori default sistem |
| user_id | BIGINT | NULLABLE, FK | Owner untuk custom category |
| created_at | TIMESTAMP | NOT NULL | Waktu pembuatan |
| updated_at | TIMESTAMP | NOT NULL | Waktu update terakhir |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE

**Default Categories:**
| Name | Icon | Color |
|------|------|-------|
| Office Supplies | pencil-ruler | #3B82F6 |
| Food & Beverages | utensils | #F97316 |
| Transportation | car | #8B5CF6 |
| Miscellaneous | box | #6B7280 |
| Other | more-horizontal | #9CA3AF |

**Migration File:** `2025_11_27_085049_create_categories_table.php`

### 3. cash_funds

Tabel `cash_funds` menyimpan catatan penambahan dana ke kas kecil, baik initial fund maupun replenishment.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| amount | DECIMAL(10,2) | NOT NULL | Jumlah dana |
| note | VARCHAR(255) | NULLABLE | Catatan/keterangan |
| fund_date | DATE | NOT NULL | Tanggal penambahan |
| user_id | BIGINT | NOT NULL, FK | User yang menambahkan |
| created_at | TIMESTAMP | NOT NULL | Waktu pembuatan |
| updated_at | TIMESTAMP | NOT NULL | Waktu update terakhir |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE

**Migration File:** `2025_11_27_085056_create_cash_funds_table.php`

### 4. transactions

Tabel `transactions` menyimpan catatan pengeluaran kas kecil.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PK, AUTO_INCREMENT | Primary key |
| amount | DECIMAL(10,2) | NOT NULL | Jumlah pengeluaran |
| description | VARCHAR(200) | NOT NULL | Deskripsi transaksi |
| transaction_date | DATE | NOT NULL | Tanggal transaksi |
| category_id | BIGINT | NOT NULL, FK | Kategori pengeluaran |
| user_id | BIGINT | NOT NULL, FK | User yang membuat |
| created_at | TIMESTAMP | NOT NULL | Waktu pembuatan |
| updated_at | TIMESTAMP | NOT NULL | Waktu update terakhir |

**Indexes:**
- PRIMARY KEY (`id`)
- FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE

**Migration File:** `2025_11_27_085056_create_transactions_table.php`

## Relationships

### One-to-Many Relationships

| Parent | Child | Relationship | Description |
|--------|-------|--------------|-------------|
| users | transactions | 1:N | User memiliki banyak transaksi |
| users | cash_funds | 1:N | User memiliki banyak cash fund entries |
| users | categories | 1:N | User dapat membuat custom categories |
| categories | transactions | 1:N | Kategori memiliki banyak transaksi |

### Eloquent Relationships

```php
// User Model
public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}

public function cashFunds(): HasMany
{
    return $this->hasMany(CashFund::class);
}

public function categories(): HasMany
{
    return $this->hasMany(Category::class);
}

// Transaction Model
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

// Category Model
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}

// CashFund Model
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

## Business Logic Calculations

### Current Balance
```sql
SELECT 
    (SELECT COALESCE(SUM(amount), 0) FROM cash_funds WHERE user_id = ?) -
    (SELECT COALESCE(SUM(amount), 0) FROM transactions WHERE user_id = ?)
    AS current_balance
```

### Spending by Category (Current Month)
```sql
SELECT 
    category_id,
    categories.name as category_name,
    categories.color as category_color,
    SUM(transactions.amount) as total
FROM transactions
JOIN categories ON transactions.category_id = categories.id
WHERE transactions.user_id = ?
    AND MONTH(transaction_date) = MONTH(CURRENT_DATE)
    AND YEAR(transaction_date) = YEAR(CURRENT_DATE)
GROUP BY category_id, categories.name, categories.color
```

## Data Integrity Rules

### Constraints
1. **Amount Validation**: Amount harus positif (> 0)
2. **Date Validation**: Transaction date tidak boleh di masa depan
3. **Description Length**: Maksimal 200 karakter
4. **Category Required**: Setiap transaksi harus memiliki kategori
5. **User Required**: Setiap transaksi dan cash_fund harus memiliki user

### Cascade Delete
- Ketika user dihapus, semua transactions, cash_funds, dan custom categories user tersebut akan ikut terhapus
- Ketika category dihapus, semua transactions dengan category tersebut akan ikut terhapus

## Seeding Data

### Category Seeder
```php
$defaultCategories = [
    ['name' => 'Office Supplies', 'icon' => 'pencil-ruler', 'color' => '#3B82F6', 'is_default' => true],
    ['name' => 'Food & Beverages', 'icon' => 'utensils', 'color' => '#F97316', 'is_default' => true],
    ['name' => 'Transportation', 'icon' => 'car', 'color' => '#8B5CF6', 'is_default' => true],
    ['name' => 'Miscellaneous', 'icon' => 'box', 'color' => '#6B7280', 'is_default' => true],
    ['name' => 'Other', 'icon' => 'more-horizontal', 'color' => '#9CA3AF', 'is_default' => true],
];
```

## Migration Commands

```bash
# Run all migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Seed categories only
php artisan db:seed --class=CategorySeeder
```

---

*Schema ini akan diperbarui seiring dengan penambahan fitur baru yang memerlukan perubahan database.*

