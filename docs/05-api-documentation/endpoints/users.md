# User Management API - Petty Cash App

## Overview

User Management API merupakan kumpulan endpoint yang bertujuan untuk mengelola data pengguna aplikasi, yaitu: hanya dapat diakses oleh user dengan role Admin yang mencakup operasi CRUD user dan reset password.

---

## Authentication & Authorization

Semua endpoint User Management memerlukan:
1. **Authentication**: User harus login (Bearer token)
2. **Authorization**: User harus memiliki `is_admin = true`

Jika user bukan admin, akan mendapatkan response `403 Forbidden`.

---

## Endpoints

### 1. List Users

Mendapatkan daftar semua user dengan pagination dan filter.

**Endpoint:** `GET /users`

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| search | string | No | Pencarian berdasarkan nama atau email |
| role | string | No | Filter berdasarkan role: `admin` atau `user` |
| page | integer | No | Nomor halaman (default: 1) |

**Response Success (200):**

```json
{
  "users": {
    "data": [
      {
        "id": 1,
        "name": "Admin User",
        "email": "admin@test.com",
        "is_admin": true,
        "email_verified_at": "2025-11-28T00:00:00.000000Z",
        "created_at": "2025-11-27T00:00:00.000000Z",
        "updated_at": "2025-11-28T00:00:00.000000Z"
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  },
  "filters": {
    "search": null,
    "role": null
  },
  "statistics": {
    "total": 5,
    "admins": 2,
    "users": 3
  }
}
```

---

### 2. Create User Form

Menampilkan form untuk membuat user baru.

**Endpoint:** `GET /users/create`

**Response:** Inertia page `users/Create`

---

### 3. Store User

Membuat user baru.

**Endpoint:** `POST /users`

**Request Body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | Yes | max:255 |
| email | string | Yes | email, unique:users |
| password | string | Yes | min:8, confirmed |
| password_confirmation | string | Yes | same:password |
| is_admin | boolean | No | default: false |

**Example Request:**

```json
{
  "name": "New User",
  "email": "newuser@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "is_admin": false
}
```

**Response Success (302):** Redirect to `/users` with success message

**Response Error (422):**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Email sudah digunakan."],
    "password": ["Password minimal 8 karakter."]
  }
}
```

---

### 4. Edit User Form

Menampilkan form untuk mengedit user.

**Endpoint:** `GET /users/{user}/edit`

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| user | integer | ID user yang akan diedit |

**Response:** Inertia page `users/Edit` with user data

---

### 5. Update User

Mengupdate data user.

**Endpoint:** `PUT /users/{user}`

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| user | integer | ID user yang akan diupdate |

**Request Body:**

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| name | string | Yes | max:255 |
| email | string | Yes | email, unique:users (ignore current) |
| password | string | No | min:8, confirmed (jika diisi) |
| password_confirmation | string | No | same:password |
| is_admin | boolean | No | boolean |

**Example Request:**

```json
{
  "name": "Updated Name",
  "email": "updated@example.com",
  "is_admin": true
}
```

**Response Success (302):** Redirect to `/users` with success message

---

### 6. Delete User

Menghapus user dari sistem.

**Endpoint:** `DELETE /users/{user}`

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| user | integer | ID user yang akan dihapus |

**Constraints:**
- Admin tidak dapat menghapus akun sendiri
- Semua data terkait user (transaksi, cash funds, categories) akan ikut terhapus (cascade delete)

**Response Success (302):** Redirect to `/users` with success message

**Response Error (422):**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "user": ["Anda tidak dapat menghapus akun Anda sendiri."]
  }
}
```

---

### 7. Reset Password

Mereset password user dengan password random baru.

**Endpoint:** `POST /users/{user}/reset-password`

**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| user | integer | ID user yang passwordnya akan direset |

**Constraints:**
- Admin tidak dapat mereset password sendiri (gunakan Settings)

**Response Success (302):** Redirect back with success message containing new password

```
Password berhasil direset. Password baru: {generated_password}
```

**Response Error (422):**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "user": ["Gunakan menu pengaturan untuk mengubah password Anda."]
  }
}
```

---

## Data Model

### User

| Field | Type | Description |
|-------|------|-------------|
| id | integer | Primary key |
| name | string | Nama lengkap user |
| email | string | Email unik |
| email_verified_at | datetime | Timestamp verifikasi email |
| password | string | Hashed password |
| is_admin | boolean | Status admin |
| two_factor_secret | text | 2FA secret (nullable) |
| two_factor_recovery_codes | text | 2FA recovery codes (nullable) |
| two_factor_confirmed_at | datetime | 2FA confirmation timestamp |
| remember_token | string | Remember me token |
| created_at | datetime | Created timestamp |
| updated_at | datetime | Updated timestamp |

---

## Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 401 | Unauthenticated | User belum login |
| 403 | Forbidden | User bukan admin |
| 404 | Not Found | User tidak ditemukan |
| 422 | Validation Error | Input tidak valid |

---

## Related Files

### Backend
- `app/Http/Controllers/UserController.php` - Controller
- `app/Services/UserService.php` - Business logic
- `app/Http/Requests/StoreUserRequest.php` - Store validation
- `app/Http/Requests/UpdateUserRequest.php` - Update validation
- `app/Http/Middleware/EnsureUserIsAdmin.php` - Admin middleware

### Frontend
- `resources/js/pages/users/Index.vue` - List page
- `resources/js/pages/users/Create.vue` - Create page
- `resources/js/pages/users/Edit.vue` - Edit page

### Routes
```php
Route::middleware(['admin'])->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');
});
```

---

*Dokumentasi ini terakhir diperbarui: November 2025*

