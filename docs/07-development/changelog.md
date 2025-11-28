# Changelog - Petty Cash App

## Overview

Dokumen ini merupakan catatan perubahan dan riwayat versi Petty Cash App yang bertujuan untuk mendokumentasikan semua perubahan signifikan pada aplikasi, yaitu: mencakup fitur baru, perbaikan bug, dan breaking changes dari setiap release.

Format changelog mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), dan versioning mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- Advanced reporting dengan export PDF
- Budget limits dan alerts
- OCR untuk automatic receipt data extraction
- Push notifications untuk low balance

---

## [0.4.0] - 2025-11-28

### Added

#### User Management CRUD (Admin Only)
- **User List Page**: Halaman daftar user dengan iOS-style design:
  - Search by nama atau email
  - Filter by role (Admin/User)
  - Statistik cards (Total, Admin, User)
  - Swipe-to-delete pada mobile
  - Pagination
- **Create User**: Form tambah user baru dengan:
  - Validasi email unique
  - Password confirmation
  - Toggle role admin
- **Edit User**: Form edit user dengan:
  - Update nama, email
  - Optional password change
  - Toggle role admin (tidak bisa untuk diri sendiri)
- **Reset Password**: Admin dapat reset password user lain dengan generated random password
- **Delete User**: Hapus user dengan cascade delete (transaksi, cash funds, categories)
- **Admin Middleware**: `EnsureUserIsAdmin` middleware untuk proteksi akses

#### Settings Page Redesign
- **New Layout**: iOS-style navigation dengan:
  - Horizontal pill navigation pada mobile
  - Sidebar navigation pada desktop
  - Card-based content design
- **Profile Page**: Redesign dengan Card components dan Bahasa Indonesia
- **Password Page**: Redesign dengan tips keamanan password
- **Appearance Page**: Redesign dengan theme cards yang interaktif
- **Delete Account**: Redesign dengan warning card yang lebih jelas

#### UI/UX Improvements
- Updated sidebar footer links ke GitHub repo yang benar
- Mobile-optimized settings navigation (horizontal scrollable pills)
- Consistent Card-based design across settings pages
- Indonesian language untuk semua label settings

### Backend Files Created
- `app/Http/Middleware/EnsureUserIsAdmin.php` - Admin authorization middleware
- `app/Services/UserService.php` - User CRUD business logic
- `app/Http/Controllers/UserController.php` - User management controller
- `app/Http/Requests/StoreUserRequest.php` - Create user validation
- `app/Http/Requests/UpdateUserRequest.php` - Update user validation

### Frontend Files Created
- `resources/js/pages/users/Index.vue` - User list with search, filter, stats
- `resources/js/pages/users/Create.vue` - Create user form
- `resources/js/pages/users/Edit.vue` - Edit user form with reset password

### Changed
- Updated `bootstrap/app.php` dengan admin middleware alias
- Updated `routes/web.php` dengan user management routes
- Updated `resources/js/layouts/settings/Layout.vue` dengan iOS-style design
- Updated `resources/js/pages/settings/Profile.vue` dengan Card components
- Updated `resources/js/pages/settings/Password.vue` dengan tips section
- Updated `resources/js/pages/settings/Appearance.vue` dengan theme cards
- Updated `resources/js/components/DeleteUser.vue` dengan modern design
- Updated `resources/js/components/AppSidebar.vue` dengan admin nav dan GitHub links
- Updated `resources/js/components/NavMain.vue` dengan optional label prop

### Tests Added
- `tests/Feature/UserManagementTest.php` (21 tests) - Full coverage untuk user CRUD

### Technical Details
- Admin middleware registered dengan alias 'admin' di bootstrap/app.php
- User management routes dilindungi oleh middleware ['auth', 'verified', 'admin']
- Password reset menggenerate 12 karakter random password
- Self-protection: Admin tidak bisa delete/reset password diri sendiri

---

## [0.3.0] - 2025-11-28

### Added

#### User Management (Epic 4)
- **Admin Role**: Kolom `is_admin` pada tabel users untuk membedakan admin dan user biasa
- **User Tracking**: Setiap transaksi menampilkan user yang membuatnya (untuk admin)
- **Filter by User**: Admin dapat filter transaksi berdasarkan user tertentu
- **Multi-user View**: Admin dapat melihat semua transaksi dari seluruh user

#### Reporting & Export (Epic 5)
- **Reports Page**: Halaman laporan dengan tampilan summary dan detail transaksi
- **Summary Statistics**:
  - Total transaksi
  - Total pengeluaran
  - Saldo awal periode
  - Saldo akhir periode
- **Category Breakdown**: Visualisasi pengeluaran per kategori dengan progress bar
- **User Breakdown**: Visualisasi pengeluaran per user (untuk admin)
- **Date Range Filter**: Filter laporan berdasarkan periode tanggal
- **Category Filter**: Filter laporan berdasarkan kategori
- **CSV Export**: Export laporan ke format CSV dengan:
  - Informasi periode
  - Summary statistik
  - Breakdown per kategori
  - Breakdown per user (untuk admin)
  - Detail transaksi lengkap

#### Authentication Improvements
- **Login Page Redesign**: Tampilan login dengan iOS-style design:
  - Glass morphism effect
  - Animated background gradient orbs
  - Spring animations
  - Responsive dark mode
- **Registration Disabled**: Fitur registrasi dinonaktifkan, hanya admin yang dapat membuat user baru
- **Welcome Page Removed**: Halaman welcome dihapus, redirect langsung ke login/dashboard

### Backend Files Created
- `app/Http/Controllers/ReportController.php` - Controller untuk report generation dan export
- `app/Services/ReportService.php` - Service untuk kalkulasi report dan CSV generation
- `database/migrations/*_add_is_admin_to_users_table.php` - Migration untuk kolom is_admin

### Frontend Files Created
- `resources/js/pages/reports/Index.vue` - Halaman laporan dengan summary dan detail

### Changed
- Updated `app/Models/User.php` dengan is_admin attribute dan isAdmin() method
- Updated `app/Services/TransactionService.php` dengan admin access untuk semua transaksi
- Updated `app/Http/Controllers/TransactionController.php` dengan user filter dan isAdmin flag
- Updated `resources/js/components/AppSidebar.vue` dengan menu Laporan
- Updated `resources/js/pages/transactions/Index.vue` dengan user display dan filter
- Updated `resources/js/pages/auth/Login.vue` dengan desain baru iOS-style
- Updated `resources/js/types/index.d.ts` dengan is_admin, ReportFilters, ReportSummary
- Updated `config/fortify.php` dengan Features::registration() dinonaktifkan
- Updated `routes/web.php` dengan report routes dan redirect untuk home

### Removed
- `resources/js/pages/Welcome.vue` - Welcome page dihapus
- `resources/js/pages/auth/Register.vue` - Register page dihapus

### Technical Details
- Navigation menu ditambah dengan link ke halaman Laporan
- Admin dapat melihat dan filter semua transaksi
- User biasa hanya dapat melihat transaksinya sendiri
- Export CSV menggunakan StreamedResponse untuk efisiensi memory
- Filter berlaku untuk tampilan dan export secara konsisten

---

## [0.2.0] - 2025-11-28

### Added

#### Receipt Documentation (Epic 3)
- **Receipt Photo Capture**: User dapat mengambil foto struk langsung dari kamera device
- **Gallery Upload**: User dapat upload foto struk dari galeri dengan drag & drop support
- **Image Compression**: Foto dikompresi otomatis (max 1200px width, 80% JPEG quality) untuk efisiensi storage
- **Full-Screen Viewer**: Tampilan foto struk full-screen dengan fitur:
  - Zoom in/out controls
  - Rotation
  - Download functionality
  - Pan/drag saat zoomed
  - Keyboard shortcuts
- **Receipt Indicator**: Ikon hijau pada daftar transaksi untuk menandakan transaksi yang memiliki struk
- **Receipt Management**: User dapat view, replace, atau delete receipt dari transaksi

#### Backend Files Created
- `app/Services/ReceiptService.php` - Service untuk handling upload, compression, dan deletion receipt
- `database/migrations/*_add_receipt_path_to_transactions_table.php` - Migration untuk kolom receipt_path

#### Frontend Files Created
- `resources/js/components/ReceiptUploader.vue` - Komponen upload dengan camera/gallery picker
- `resources/js/components/ReceiptViewer.vue` - Komponen full-screen image viewer

#### Tests Added
- `tests/Feature/ReceiptUploadTest.php` (13 tests) - Test coverage untuk receipt functionality

### Changed
- Updated `app/Models/Transaction.php` dengan receipt_path fillable dan accessors (receipt_url, has_receipt)
- Updated `app/Http/Controllers/TransactionController.php` dengan receipt handling di store, update, dan destroy
- Updated `app/Http/Requests/StoreTransactionRequest.php` dengan receipt validation
- Updated `app/Http/Requests/UpdateTransactionRequest.php` dengan receipt dan remove_receipt validation
- Updated `routes/web.php` dengan endpoint `DELETE /transactions/{transaction}/receipt`
- Updated `resources/js/pages/transactions/Create.vue` dengan ReceiptUploader integration
- Updated `resources/js/pages/transactions/Edit.vue` dengan receipt view/replace/delete
- Updated `resources/js/pages/transactions/Index.vue` dengan receipt indicator dan viewer
- Updated `resources/js/types/index.d.ts` dengan receipt_path, receipt_url, has_receipt fields

### Technical Details
- Storage: Laravel public disk (`storage/app/public/receipts/`)
- Supported formats: JPEG, JPG, PNG, GIF, WebP
- Max file size: 5MB (before compression)
- Image compression: GD library (max 1200px width, 80% quality)
- File naming: `receipt_{user_id}_{timestamp}_{random}.jpg`

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
- PHPUnit testing (68 tests, 238 assertions)

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
| 0.4.0 | 2025-11-28 | User Management CRUD, Settings redesign iOS-style |
| 0.3.0 | 2025-11-28 | User roles, Reports page, CSV export, Login redesign |
| 0.2.0 | 2025-11-28 | Receipt documentation - Photo capture & upload |
| 0.1.0 | 2025-11-27 | Initial release - Core transaction management |

---

## Upgrade Notes

### Upgrading to 0.1.0

Ini adalah release pertama. Untuk fresh installation:

```bash
# Clone repository
git clone https://github.com/ZulfikarHD/simple-petty-cash.git

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

