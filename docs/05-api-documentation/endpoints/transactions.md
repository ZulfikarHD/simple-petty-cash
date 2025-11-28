# Transactions API - Petty Cash App

## Overview

Dokumen ini merupakan spesifikasi API untuk modul transaksi Petty Cash App yang bertujuan untuk mendokumentasikan endpoint, request/response format, dan validation rules, yaitu: memberikan panduan teknis bagi developer dalam mengintegrasikan dengan sistem transaksi.

## Base URL

```
http://localhost:8000
```

## Authentication

Semua endpoint memerlukan authentication via Laravel session/cookies.

```
Cookie: laravel_session=...
```

## Endpoints

### List Transactions

Mengambil daftar transaksi dengan pagination dan filter.

**Endpoint**: `GET /transactions`

**Query Parameters**:

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| start_date | date | No | Filter dari tanggal (YYYY-MM-DD) |
| end_date | date | No | Filter sampai tanggal (YYYY-MM-DD) |
| category_id | integer | No | Filter by category ID |
| page | integer | No | Halaman pagination (default: 1) |

**Example Request**:

```bash
GET /transactions?start_date=2025-11-01&end_date=2025-11-30&category_id=1&page=1
```

**Response (Inertia)**:

```javascript
{
    component: 'transactions/Index',
    props: {
        transactions: {
            data: [
                {
                    id: 1,
                    amount: "50000.00",
                    description: "Beli alat tulis",
                    transaction_date: "2025-11-27",
                    category_id: 1,
                    user_id: 1,
                    receipt_path: "receipts/receipt_1_20251128_120000_abc12345.jpg",
                    receipt_url: "http://localhost:8000/storage/receipts/receipt_1_20251128_120000_abc12345.jpg",
                    has_receipt: true,
                    category: {
                        id: 1,
                        name: "Office Supplies",
                        icon: "pencil-ruler",
                        color: "#3B82F6"
                    },
                    created_at: "2025-11-27T08:00:00.000000Z",
                    updated_at: "2025-11-27T08:00:00.000000Z"
                }
            ],
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 1,
            // ... pagination links
        },
        categories: [...],
        currentBalance: 950000,
        filters: {
            start_date: "2025-11-01",
            end_date: "2025-11-30",
            category_id: 1
        }
    }
}
```

---

### Create Transaction Form

Menampilkan form untuk membuat transaksi baru.

**Endpoint**: `GET /transactions/create`

**Response (Inertia)**:

```javascript
{
    component: 'transactions/Create',
    props: {
        categories: [
            {
                id: 1,
                name: "Office Supplies",
                icon: "pencil-ruler",
                color: "#3B82F6",
                is_default: true
            },
            // ... other categories
        ],
        currentBalance: 1000000
    }
}
```

---

### Store Transaction

Menyimpan transaksi baru dengan optional receipt photo.

**Endpoint**: `POST /transactions`

**Content-Type**: `multipart/form-data` (jika upload receipt) atau `application/json`

**Request Body**:

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| amount | numeric | Yes | min:0.01, max:9999999.99 |
| description | string | Yes | max:200 |
| transaction_date | date | Yes | before_or_equal:today |
| category_id | integer | Yes | exists:categories,id |
| receipt | file | No | image, mimes:jpeg,jpg,png,gif,webp, max:5120 |

**Example Request (tanpa receipt)**:

```bash
POST /transactions
Content-Type: application/json

{
    "amount": 50000,
    "description": "Beli alat tulis kantor",
    "transaction_date": "2025-11-27",
    "category_id": 1
}
```

**Example Request (dengan receipt)**:

```bash
POST /transactions
Content-Type: multipart/form-data

amount: 50000
description: Beli alat tulis kantor
transaction_date: 2025-11-27
category_id: 1
receipt: [file binary]
```

**Success Response**: `302 Redirect to /transactions`

**Error Response (Validation)**:

```javascript
{
    errors: {
        amount: ["Jumlah pengeluaran harus diisi."],
        description: ["Deskripsi harus diisi."]
    }
}
```

**Error Response (Insufficient Balance)**:

```javascript
{
    errors: {
        amount: ["Saldo tidak mencukupi. Saldo saat ini: Rp 100.000"]
    }
}
```

---

### Edit Transaction Form

Menampilkan form untuk edit transaksi.

**Endpoint**: `GET /transactions/{transaction}/edit`

**Path Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| transaction | integer | Transaction ID |

**Response (Inertia)**:

```javascript
{
    component: 'transactions/Edit',
    props: {
        transaction: {
            id: 1,
            amount: "50000.00",
            description: "Beli alat tulis",
            transaction_date: "2025-11-27",
            category_id: 1,
            receipt_path: "receipts/receipt_1_20251128_120000_abc12345.jpg",
            receipt_url: "http://localhost:8000/storage/receipts/receipt_1_20251128_120000_abc12345.jpg",
            has_receipt: true,
            category: {...}
        },
        categories: [...],
        currentBalance: 950000
    }
}
```

**Error Response (403 Forbidden)**:

Jika user mencoba mengakses transaksi milik user lain.

---

### Update Transaction

Memperbarui transaksi yang ada, termasuk upload/replace/remove receipt.

**Endpoint**: `PUT /transactions/{transaction}`

**Content-Type**: `multipart/form-data` (jika upload receipt) atau `application/json`

**Path Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| transaction | integer | Transaction ID |

**Request Body** (semua field opsional):

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| amount | numeric | No | min:0.01, max:9999999.99 |
| description | string | No | max:200 |
| transaction_date | date | No | before_or_equal:today |
| category_id | integer | No | exists:categories,id |
| receipt | file | No | image, mimes:jpeg,jpg,png,gif,webp, max:5120 |
| remove_receipt | boolean | No | Set true untuk menghapus receipt existing |

**Example Request (update data)**:

```bash
PUT /transactions/1
Content-Type: application/json

{
    "amount": 75000,
    "description": "Beli alat tulis dan kertas HVS"
}
```

**Example Request (replace receipt)**:

```bash
POST /transactions/1
Content-Type: multipart/form-data

_method: PUT
amount: 75000
description: Beli alat tulis dan kertas HVS
receipt: [file binary]
```

**Example Request (remove receipt)**:

```bash
PUT /transactions/1
Content-Type: application/json

{
    "remove_receipt": true
}
```

**Success Response**: `302 Redirect to /transactions`

---

### Delete Transaction Receipt

Menghapus receipt dari transaksi tanpa menghapus transaksi itu sendiri.

**Endpoint**: `DELETE /transactions/{transaction}/receipt`

**Path Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| transaction | integer | Transaction ID |

**Example Request**:

```bash
DELETE /transactions/1/receipt
```

**Success Response**: `302 Redirect back`

**Error Response (403 Forbidden)**:

Jika user mencoba menghapus receipt transaksi milik user lain.

---

### Delete Transaction

Menghapus transaksi.

**Endpoint**: `DELETE /transactions/{transaction}`

**Path Parameters**:

| Parameter | Type | Description |
|-----------|------|-------------|
| transaction | integer | Transaction ID |

**Example Request**:

```bash
DELETE /transactions/1
```

**Success Response**: `302 Redirect to /transactions`

---

## Data Models

### Transaction

```typescript
interface Transaction {
    id: number;
    amount: string;           // Decimal as string
    description: string;
    transaction_date: string; // YYYY-MM-DD
    category_id: number;
    user_id: number;
    receipt_path: string | null;  // Path file di storage
    receipt_url: string | null;   // Full URL untuk akses gambar
    has_receipt: boolean;         // Computed: apakah ada receipt
    category?: Category;
    user?: User;
    created_at: string;
    updated_at: string;
}
```

### Category

```typescript
interface Category {
    id: number;
    name: string;
    icon: string | null;
    color: string | null;
    is_default: boolean;
    user_id: number | null;
}
```

## Validation Messages (Indonesian)

| Field | Rule | Message |
|-------|------|---------|
| amount | required | Jumlah pengeluaran harus diisi. |
| amount | numeric | Jumlah harus berupa angka. |
| amount | min | Jumlah minimal adalah 0.01. |
| description | required | Deskripsi harus diisi. |
| description | max | Deskripsi maksimal 200 karakter. |
| transaction_date | required | Tanggal transaksi harus diisi. |
| transaction_date | before_or_equal | Tanggal tidak boleh di masa depan. |
| category_id | required | Kategori harus dipilih. |
| category_id | exists | Kategori tidak valid. |
| receipt | image | File harus berupa gambar. |
| receipt | mimes | Format gambar harus JPEG, PNG, GIF, atau WebP. |
| receipt | max | Ukuran gambar maksimal 5MB. |

## Wayfinder Usage

```typescript
import { 
    index, 
    create, 
    store, 
    edit, 
    update, 
    destroy,
    destroyReceipt
} from '@/actions/App/Http/Controllers/TransactionController';

// List transactions
index()                    // { url: '/transactions', method: 'get' }
index({ query: { page: 2 }}) // { url: '/transactions?page=2', method: 'get' }

// Create form
create()                   // { url: '/transactions/create', method: 'get' }

// Store (use with Inertia form or FormData for receipt)
store()                    // { url: '/transactions', method: 'post' }

// Edit form
edit(1)                    // { url: '/transactions/1/edit', method: 'get' }
edit({ id: 1 })           // Same result

// Update (use FormData for receipt upload)
update(1)                  // { url: '/transactions/1', method: 'put' }

// Delete transaction
destroy(1)                 // { url: '/transactions/1', method: 'delete' }

// Delete receipt only
destroyReceipt(1)          // { url: '/transactions/1/receipt', method: 'delete' }
```

### Upload Receipt Example

```typescript
// Creating transaction with receipt using FormData
const formData = new FormData();
formData.append('amount', '50000');
formData.append('description', 'Beli alat tulis');
formData.append('transaction_date', '2025-11-28');
formData.append('category_id', '1');
formData.append('receipt', fileInput.files[0]);

router.post(store().url, formData, {
    forceFormData: true,
});
```

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success (GET requests) |
| 302 | Redirect (successful POST/PUT/DELETE) |
| 403 | Forbidden (unauthorized access) |
| 404 | Transaction not found |
| 422 | Validation error |
| 500 | Server error |

---

*API documentation ini akan diperbarui seiring dengan penambahan endpoint baru.*

