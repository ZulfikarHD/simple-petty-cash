# System Architecture - Petty Cash App

## Overview

Dokumen ini merupakan spesifikasi arsitektur sistem Petty Cash App yang bertujuan untuk menjelaskan struktur high-level aplikasi, komponen-komponen utama, dan interaksi antar komponen, yaitu: memberikan panduan teknis bagi developer dalam memahami arsitektur aplikasi secara keseluruhan.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CLIENT LAYER                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                │
│    │   Browser    │    │   Browser    │    │   Browser    │                │
│    │   (Chrome)   │    │   (Firefox)  │    │   (Safari)   │                │
│    └──────┬───────┘    └──────┬───────┘    └──────┬───────┘                │
│           │                   │                   │                        │
│           └───────────────────┼───────────────────┘                        │
│                               │                                            │
│                               ▼                                            │
│                    ┌──────────────────────┐                                │
│                    │   Vue.js Frontend    │                                │
│                    │   (Inertia.js SPA)   │                                │
│                    └──────────┬───────────┘                                │
│                               │                                            │
└───────────────────────────────┼────────────────────────────────────────────┘
                                │ HTTP/HTTPS
                                ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                           APPLICATION LAYER                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│    ┌─────────────────────────────────────────────────────────────────┐     │
│    │                     Laravel Framework                            │     │
│    │                                                                  │     │
│    │  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │     │
│    │  │   Routes    │  │ Middleware  │  │    Fortify Auth         │  │     │
│    │  │  (web.php)  │→ │ (auth,      │→ │  (Login, Register,      │  │     │
│    │  │             │  │  verified)  │  │   2FA, Password Reset)  │  │     │
│    │  └─────────────┘  └─────────────┘  └─────────────────────────┘  │     │
│    │         │                                                        │     │
│    │         ▼                                                        │     │
│    │  ┌─────────────────────────────────────────────────────────┐    │     │
│    │  │                    Controllers                           │    │     │
│    │  │  ┌───────────────┐ ┌───────────────┐ ┌──────────────┐   │    │     │
│    │  │  │ Dashboard     │ │ Transaction   │ │ CashFund     │   │    │     │
│    │  │  │ Controller    │ │ Controller    │ │ Controller   │   │    │     │
│    │  │  └───────┬───────┘ └───────┬───────┘ └──────┬───────┘   │    │     │
│    │  └──────────┼─────────────────┼────────────────┼───────────┘    │     │
│    │             │                 │                │                 │     │
│    │             └─────────────────┼────────────────┘                 │     │
│    │                               ▼                                  │     │
│    │  ┌─────────────────────────────────────────────────────────┐    │     │
│    │  │                  Service Layer                           │    │     │
│    │  │           ┌─────────────────────────┐                   │    │     │
│    │  │           │   TransactionService    │                   │    │     │
│    │  │           │   - getCurrentBalance   │                   │    │     │
│    │  │           │   - createTransaction   │                   │    │     │
│    │  │           │   - getSpendingByCategory                   │    │     │
│    │  │           └───────────┬─────────────┘                   │    │     │
│    │  └───────────────────────┼─────────────────────────────────┘    │     │
│    │                          │                                       │     │
│    │                          ▼                                       │     │
│    │  ┌─────────────────────────────────────────────────────────┐    │     │
│    │  │                    Model Layer                           │    │     │
│    │  │  ┌────────┐ ┌────────────┐ ┌──────────┐ ┌──────────┐   │    │     │
│    │  │  │  User  │ │ Transaction│ │ CashFund │ │ Category │   │    │     │
│    │  │  └────────┘ └────────────┘ └──────────┘ └──────────┘   │    │     │
│    │  └─────────────────────────────────────────────────────────┘    │     │
│    │                          │                                       │     │
│    └──────────────────────────┼───────────────────────────────────────┘     │
│                               │                                             │
└───────────────────────────────┼─────────────────────────────────────────────┘
                                │ Eloquent ORM
                                ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                              DATA LAYER                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│                    ┌──────────────────────┐                                │
│                    │   SQLite Database    │                                │
│                    │                      │                                │
│                    │  ┌────────────────┐  │                                │
│                    │  │     users      │  │                                │
│                    │  ├────────────────┤  │                                │
│                    │  │   categories   │  │                                │
│                    │  ├────────────────┤  │                                │
│                    │  │   cash_funds   │  │                                │
│                    │  ├────────────────┤  │                                │
│                    │  │  transactions  │  │                                │
│                    │  └────────────────┘  │                                │
│                    │                      │                                │
│                    └──────────────────────┘                                │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Technology Stack

### Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| Vue.js | 3.5.x | Reactive UI framework |
| Inertia.js | 2.x | SPA without API |
| TypeScript | 5.x | Type safety |
| Tailwind CSS | 4.x | Utility-first CSS |
| Vite | 7.x | Build tool |
| Wayfinder | 0.1.x | Type-safe routing |

### Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.4.x | Server-side language |
| Laravel | 12.x | Web application framework |
| Laravel Fortify | 1.x | Authentication |
| Laravel Pint | 1.x | Code formatting |
| PHPUnit | 11.x | Testing framework |

### Database

| Technology | Version | Purpose |
|------------|---------|---------|
| SQLite | - | Relational database |

## Component Architecture

### Controllers

Controllers dalam aplikasi ini bertanggung jawab untuk handling HTTP requests dan mengembalikan Inertia responses.

```
app/Http/Controllers/
├── Controller.php              # Base controller
├── DashboardController.php     # Dashboard overview
├── TransactionController.php   # CRUD transactions
├── CashFundController.php      # Fund management
├── CategoryController.php      # Category listing
└── Settings/
    ├── ProfileController.php   # User profile
    ├── PasswordController.php  # Password update
    └── TwoFactorAuthenticationController.php
```

### Services

Service layer mengenkapsulasi business logic yang kompleks.

```
app/Services/
└── TransactionService.php
    ├── getCurrentBalance()
    ├── getTotalFunds()
    ├── getTotalExpenses()
    ├── canAfford()
    ├── createTransaction()
    ├── updateTransaction()
    ├── deleteTransaction()
    ├── getTransactions()
    ├── getRecentTransactions()
    ├── getSpendingByCategory()
    ├── getCurrentMonthSpending()
    ├── getCategories()
    ├── addFund()
    ├── getFundHistory()
    └── hasInitialFund()
```

### Models

Models merepresentasikan data entities dan relationships.

```
app/Models/
├── User.php
│   ├── transactions(): HasMany
│   ├── cashFunds(): HasMany
│   └── categories(): HasMany
│
├── Transaction.php
│   ├── user(): BelongsTo
│   └── category(): BelongsTo
│
├── CashFund.php
│   └── user(): BelongsTo
│
└── Category.php
    ├── user(): BelongsTo
    └── transactions(): HasMany
```

### Form Requests

Form Requests menangani validation untuk incoming requests.

```
app/Http/Requests/
├── StoreTransactionRequest.php   # Create transaction validation
├── UpdateTransactionRequest.php  # Update transaction validation
└── StoreCashFundRequest.php      # Create fund validation
```

## Frontend Architecture

### Page Components

```
resources/js/pages/
├── Dashboard.vue           # Main dashboard with balance overview
├── Welcome.vue             # Landing page
├── transactions/
│   ├── Index.vue          # Transaction list with filters
│   ├── Create.vue         # Create transaction form
│   └── Edit.vue           # Edit transaction form
├── cash-fund/
│   └── Create.vue         # Add fund form
├── auth/
│   ├── Login.vue
│   ├── Register.vue
│   └── ...
└── settings/
    ├── Profile.vue
    ├── Password.vue
    └── ...
```

### Shared Components

```
resources/js/components/
├── ui/                    # Shadcn/Vue UI components
│   ├── button/
│   ├── card/
│   ├── dialog/
│   ├── input/
│   └── ...
├── AppSidebar.vue         # Main sidebar navigation
├── NavMain.vue            # Navigation menu items
├── NavUser.vue            # User dropdown menu
└── ...
```

### Layouts

```
resources/js/layouts/
├── AppLayout.vue          # Main authenticated layout
└── app/
    └── AppSidebarLayout.vue
```

## Request Flow

### Transaction Creation Flow

```
1. User fills form in Create.vue
         │
         ▼
2. Form submitted via Inertia.js
   POST /transactions
         │
         ▼
3. StoreTransactionRequest validates input
   - amount: required, numeric, min:0.01
   - description: required, max:200
   - transaction_date: required, before_or_equal:today
   - category_id: required, exists:categories
         │
         ▼
4. TransactionController@store handles request
         │
         ▼
5. TransactionService checks balance
   - getCurrentBalance()
   - Validate amount <= balance
         │
         ▼
6. TransactionService creates transaction
   - $user->transactions()->create($data)
         │
         ▼
7. Redirect to transactions.index
   with success message
```

### Dashboard Data Flow

```
1. GET /dashboard request
         │
         ▼
2. DashboardController invoked
         │
         ▼
3. TransactionService gathers data
   ├── getCurrentBalance()
   ├── getTotalFunds()
   ├── getTotalExpenses()
   ├── getCurrentMonthSpending()
   ├── getRecentTransactions(5)
   ├── getSpendingByCategory()
   └── hasInitialFund()
         │
         ▼
4. Inertia::render('Dashboard', $data)
         │
         ▼
5. Vue component receives props
   and renders UI
```

## Security Architecture

### Authentication Flow

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│    Login     │     │   Fortify    │     │   Session    │
│    Page      │────▶│   Auth       │────▶│   Created    │
└──────────────┘     └──────────────┘     └──────────────┘
                            │
                            ▼
                     ┌──────────────┐
                     │  2FA Check   │
                     │  (if enabled)│
                     └──────────────┘
```

### Middleware Stack

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // Protected routes
});
```

### Authorization

- User-owned resources checked via model binding
- `UpdateTransactionRequest` validates ownership
- Controller methods verify user_id matches

## Deployment Architecture

### Development Environment

```
Local Machine
├── PHP 8.4 (artisan serve)
├── Node.js (yarn dev)
└── SQLite (database.sqlite)
```

### Production Environment (Recommended)

```
Production Server
├── Nginx/Apache
├── PHP-FPM 8.4
├── Node.js (build only)
├── PostgreSQL/MySQL
└── Redis (optional, for cache/sessions)
```

## Performance Considerations

### Database Optimization
- Eager loading untuk prevent N+1 queries
- Indexed foreign keys
- Pagination untuk large datasets

### Frontend Optimization
- Vite untuk fast builds
- Component lazy loading
- Asset optimization (minification, compression)

### Caching Strategy
- Session caching
- Config caching
- Route caching

---

*Arsitektur ini akan diperbarui seiring dengan evolusi aplikasi dan penambahan fitur baru.*

