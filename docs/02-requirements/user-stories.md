# User Stories - Petty Cash App

## Overview

Dokumen ini merupakan kompilasi user stories untuk Petty Cash App yang bertujuan untuk mendokumentasikan kebutuhan pengguna dalam format standar Agile, yaitu: menjelaskan fitur dari perspektif pengguna dengan acceptance criteria yang jelas.

## Sprint 1: Core Transaction Management

### Epic 1: Basic Expense Recording

---

#### User Story 1.1: Record Cash Expense

**Story ID**: US-1.1  
**Story Points**: 5  
**Status**: ✅ Done

**As a** user  
**I want to** quickly record a cash expense with amount, date, and description  
**So that** I can track where petty cash is being spent

**Acceptance Criteria**:
- [x] User dapat memasukkan jumlah pengeluaran (input numerik dengan 2 desimal)
- [x] User dapat memilih atau memasukkan tanggal/waktu saat ini
- [x] User dapat menambahkan deskripsi singkat (maksimal 200 karakter)
- [x] User dapat memilih dari kategori yang telah ditentukan (Office Supplies, Food/Beverages, Transportation, Miscellaneous, Other)
- [x] Sistem menyimpan transaksi secara langsung
- [x] User menerima konfirmasi pesan

**Technical Notes**:
- Controller: `TransactionController@store`
- Form Request: `StoreTransactionRequest`
- Vue Page: `transactions/Create.vue`

---

#### User Story 1.2: View Transaction List

**Story ID**: US-1.2  
**Story Points**: 3  
**Status**: ✅ Done

**As a** user  
**I want to** see a list of all recorded expenses  
**So that** I can review what has been spent

**Acceptance Criteria**:
- [x] Menampilkan transaksi dalam urutan kronologis terbalik (terbaru di atas)
- [x] Menampilkan tanggal, jumlah, kategori, dan deskripsi untuk setiap transaksi
- [x] Menyertakan tampilan saldo berjalan
- [x] Mendukung filter dasar berdasarkan rentang tanggal
- [x] Daftar diperbarui secara real-time ketika pengeluaran baru ditambahkan

**Technical Notes**:
- Controller: `TransactionController@index`
- Vue Page: `transactions/Index.vue`
- Pagination: 15 items per page

---

#### User Story 1.3: Set Initial Cash Balance

**Story ID**: US-1.3  
**Story Points**: 3  
**Status**: ✅ Done

**As a** user  
**I want to** set the starting petty cash amount  
**So that** the app can track remaining balance accurately

**Acceptance Criteria**:
- [x] User dapat memasukkan jumlah dana kas awal pada penggunaan pertama
- [x] User dapat menambahkan kas (replenishment) kapan saja
- [x] Sistem menghitung dan menampilkan saldo saat ini (awal + replenishment - pengeluaran)
- [x] Saldo diperbarui secara otomatis setelah setiap transaksi
- [x] Saldo tidak boleh negatif (peringatan ditampilkan)

**Technical Notes**:
- Controller: `CashFundController@store`
- Form Request: `StoreCashFundRequest`
- Vue Page: `cash-fund/Create.vue`
- Balance calculation di `TransactionService`

---

#### User Story 1.4: User Authentication

**Story ID**: US-1.4  
**Story Points**: 5  
**Status**: ✅ Done

**As a** user  
**I want to** create an account and log in securely  
**So that** my petty cash data is private and secure

**Acceptance Criteria**:
- [x] User dapat mendaftar dengan email dan password
- [x] User dapat login dengan credentials
- [x] Password harus memenuhi persyaratan keamanan dasar (8+ karakter)
- [x] User tetap login di seluruh sesi (remember me)
- [x] User dapat logout

**Technical Notes**:
- Implemented via Laravel Fortify
- 2FA support enabled
- Password reset via email

---

## Sprint 2: Receipt Management & Enhanced Features

### Epic 3: Receipt Documentation

---

#### User Story 2.1: Capture Receipt Photo

**Story ID**: US-2.1  
**Story Points**: 8  
**Status**: ✅ Done

**As a** user  
**I want to** take or upload a photo of a receipt  
**So that** I have documentation for each expense

**Acceptance Criteria**:
- [x] User dapat membuka kamera untuk mengambil foto receipt
- [x] User dapat upload foto yang sudah ada dari galeri
- [x] Foto dilampirkan ke transaksi spesifik
- [x] Foto dikompresi untuk efisiensi penyimpanan
- [x] User dapat melihat foto ukuran penuh dengan tap thumbnail
- [x] User dapat menghapus atau mengganti foto receipt

**Technical Notes**:
- Storage: Laravel public disk (`storage/app/public/receipts/`)
- Image processing: GD library (native PHP)
- Max file size: 5MB (before compression)
- Compression: 1200px max width, 80% JPEG quality
- Supported formats: JPEG, JPG, PNG, GIF, WebP
- Service: `ReceiptService`
- Components: `ReceiptUploader.vue`, `ReceiptViewer.vue`
- Endpoint untuk delete receipt: `DELETE /transactions/{transaction}/receipt`

---

#### User Story 2.2: Edit/Delete Transactions

**Story ID**: US-2.2  
**Story Points**: 5  
**Status**: ✅ Done

**As a** user  
**I want to** edit or delete incorrect transactions  
**So that** my records remain accurate

**Acceptance Criteria**:
- [x] User dapat tap transaksi untuk mengedit semua field
- [x] User dapat menghapus transaksi dengan prompt konfirmasi
- [x] Saldo dihitung ulang secara otomatis setelah edit/delete
- [x] Transaksi yang dihapus dihilangkan dari daftar
- [ ] Riwayat edit dipelihara (opsional untuk transparansi)

**Technical Notes**:
- Controller: `TransactionController@update`, `TransactionController@destroy`
- Form Request: `UpdateTransactionRequest`
- Vue Page: `transactions/Edit.vue`
- Authorization: user ownership check

---

### Epic 4: User Management

---

#### User Story 2.3: Multiple Users/Team Members

**Story ID**: US-2.3  
**Story Points**: 5  
**Status**: 🔜 Planned

**As a** manager  
**I want to** see who made each expense  
**So that** I can track accountability

**Acceptance Criteria**:
- [ ] Setiap transaksi mencatat user yang membuatnya
- [ ] Daftar transaksi menampilkan nama/inisial user untuk setiap entry
- [ ] User dapat filter transaksi berdasarkan person
- [ ] Admin dapat melihat semua transaksi tim
- [ ] Regular users hanya melihat transaksi sendiri (kecuali admin)

**Technical Notes**:
- Add `role` column to users table
- Implement policy for transaction access
- Add user filter to transaction list

---

## Sprint 3: Reporting & Polish

### Epic 5: Reporting & Export

---

#### User Story 3.1: Basic Expense Report

**Story ID**: US-3.1  
**Story Points**: 5  
**Status**: 🔜 Planned

**As a** user  
**I want to** generate a summary report of expenses  
**So that** I can review spending patterns and reconcile cash

**Acceptance Criteria**:
- [ ] User dapat generate report untuk rentang tanggal kustom
- [ ] Report menampilkan total pengeluaran per kategori
- [ ] Report menampilkan total pengeluaran per user
- [ ] Report menyertakan jumlah transaksi
- [ ] Report menampilkan saldo awal, total pengeluaran, dan saldo akhir
- [ ] Report dapat dilihat di layar

**Technical Notes**:
- New controller: `ReportController`
- New Vue page: `reports/Index.vue`
- Chart library: Chart.js atau similar

---

#### User Story 3.2: Export Data

**Story ID**: US-3.2  
**Story Points**: 5  
**Status**: 🔜 Planned

**As a** user  
**I want to** export my expense data  
**So that** I can share it with accounting or import to other systems

**Acceptance Criteria**:
- [ ] User dapat export transaksi sebagai file CSV
- [ ] Export menyertakan semua field transaksi dan informasi user
- [ ] CSV dapat diemail atau dibagikan via opsi sharing standar
- [ ] Export menyertakan receipts sebagai attachment links atau file terpisah
- [ ] User dapat memilih rentang tanggal untuk export

**Technical Notes**:
- Use Laravel Excel package
- Streaming export for large datasets
- Include receipt URLs in export

---

#### User Story 3.3: Dashboard Overview

**Story ID**: US-3.3  
**Story Points**: 8  
**Status**: ✅ Done

**As a** user  
**I want to** see a quick overview of petty cash status  
**So that** I can understand spending at a glance

**Acceptance Criteria**:
- [x] Dashboard menampilkan saldo saat ini secara prominent
- [x] Menampilkan pengeluaran untuk bulan berjalan
- [x] Menampilkan 3 kategori pengeluaran teratas
- [x] Menampilkan transaksi terbaru (5 terakhir)
- [x] Menyertakan chart/grafik sederhana pengeluaran per kategori

**Technical Notes**:
- Controller: `DashboardController`
- Vue Page: `Dashboard.vue`
- Data aggregation in `TransactionService`

---

## Story Points Summary

| Sprint | Total Points | Completed | Remaining |
|--------|--------------|-----------|-----------|
| Sprint 1 | 16 | 16 | 0 |
| Sprint 2 | 18 | 13 | 5 |
| Sprint 3 | 18 | 8 | 10 |
| **Total** | **52** | **37** | **15** |

## Story Status Legend

| Status | Description |
|--------|-------------|
| ✅ Done | Story completed and accepted |
| 🚧 In Progress | Currently being worked on |
| 🔜 Planned | Scheduled for upcoming sprint |
| ⏸️ On Hold | Temporarily paused |
| ❌ Cancelled | Removed from scope |

---

*User stories ini akan diperbarui seiring dengan progress development dan feedback dari stakeholder.*

