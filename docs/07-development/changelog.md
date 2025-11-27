# Changelog - Petty Cash App

## Overview

Dokumen ini merupakan catatan perubahan dan riwayat versi Petty Cash App yang bertujuan untuk mendokumentasikan semua perubahan signifikan pada aplikasi, yaitu: mencakup fitur baru, perbaikan bug, dan breaking changes dari setiap release.

Format changelog mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), dan versioning mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- Receipt photo capture dan upload
- Multi-user support dengan role management
- Advanced reporting dengan export PDF
- Budget limits dan alerts

---

## [0.1.0] - 2025-11-27

### Added

#### Authentication (Laravel Fortify)
- User registration dengan email dan password
- User login dan logout
- Remember me functionality
- Password reset via email
- Email verification
- Two-factor authentication (2FA) support
- Password confirmation untuk sensitive actions

#### Cash Fund Management
- Set initial cash balance
- Add fund replenishment
- View fund history
- Calculate current balance automatically

#### Transaction Management
- Create expense transaction dengan detail:
  - Amount (decimal, 2 places)
  - Description (max 200 characters)
  - Transaction date
  - Category
- View transaction list dengan pagination
- Filter transactions by:
  - Date range (start date, end date)
  - Category
- Edit existing transactions
- Delete transactions dengan confirmation
- Running balance display
- Balance validation (prevent negative balance)

#### Categories
- 5 default categories:
  - Office Supplies (blue)
  - Food & Beverages (orange)
  - Transportation (purple)
  - Miscellaneous (gray)
  - Other (light gray)
- Category icon dan color support
- System default vs user custom categories

#### Dashboard
- Current balance display (hero card)
- Total funds summary
- Total expenses summary
- Current month spending
- Spending breakdown by category (visual chart)
- Recent transactions (last 5)
- Quick action buttons:
  - Catat Pengeluaran
  - Tambah Dana
- Setup banner untuk new users

#### User Interface
- iOS-style design principles:
  - Spring animations
  - Press feedback (scale 0.97)
  - Glass effect cards
  - Staggered entrance animations
- Responsive design (mobile & desktop)
- Dark mode support
- Indonesian language UI
- Swipe-to-reveal actions (mobile)

#### Technical Infrastructure
- Laravel 12 backend
- Vue.js 3 + Inertia.js 2 frontend
- Tailwind CSS 4 styling
- TypeScript type safety
- Wayfinder route generation
- SQLite database
- PHPUnit testing (62 tests, 226 assertions)

### Backend Files Created
- `app/Models/Category.php`
- `app/Models/CashFund.php`
- `app/Models/Transaction.php`
- `app/Services/TransactionService.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/TransactionController.php`
- `app/Http/Controllers/CashFundController.php`
- `app/Http/Controllers/CategoryController.php`
- `app/Http/Requests/StoreTransactionRequest.php`
- `app/Http/Requests/UpdateTransactionRequest.php`
- `app/Http/Requests/StoreCashFundRequest.php`
- `database/migrations/*_create_categories_table.php`
- `database/migrations/*_create_cash_funds_table.php`
- `database/migrations/*_create_transactions_table.php`
- `database/factories/CategoryFactory.php`
- `database/factories/CashFundFactory.php`
- `database/factories/TransactionFactory.php`
- `database/seeders/CategorySeeder.php`

### Frontend Files Created
- `resources/js/pages/Dashboard.vue`
- `resources/js/pages/transactions/Index.vue`
- `resources/js/pages/transactions/Create.vue`
- `resources/js/pages/transactions/Edit.vue`
- `resources/js/pages/cash-fund/Create.vue`

### Tests Added
- `tests/Feature/TransactionTest.php` (13 tests)
- `tests/Feature/CashFundTest.php` (8 tests)

### Changed
- Updated `app/Models/User.php` dengan relationships
- Updated `routes/web.php` dengan new routes
- Updated `resources/js/components/AppSidebar.vue` dengan navigation
- Updated `resources/js/types/index.d.ts` dengan new types

---

## Version History Summary

| Version | Date | Highlights |
|---------|------|------------|
| 0.1.0 | 2025-11-27 | Initial release - Core transaction management |

---

## Upgrade Notes

### Upgrading to 0.1.0

Ini adalah release pertama. Untuk fresh installation:

```bash
# Clone repository
git clone https://github.com/your-repo/simple-petty-cash.git

# Install dependencies
composer install
yarn install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed --class=CategorySeeder

# Build frontend
yarn build
```

---

## Contributing

Untuk berkontribusi pada changelog:

1. Tambahkan entry di section `[Unreleased]`
2. Gunakan format yang konsisten
3. Sertakan link ke PR atau issue jika ada
4. Update version number saat release

### Entry Format

```markdown
### Added
- Fitur baru yang ditambahkan

### Changed
- Perubahan pada fitur existing

### Deprecated
- Fitur yang akan dihapus di versi mendatang

### Removed
- Fitur yang dihapus

### Fixed
- Bug fixes

### Security
- Security-related changes
```

---

*Changelog ini diperbarui setiap kali ada release baru.*

