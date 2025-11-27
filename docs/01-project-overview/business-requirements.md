# Business Requirements - Petty Cash App

## Overview

Dokumen ini merupakan spesifikasi kebutuhan bisnis untuk Petty Cash App yang bertujuan untuk mendefinisikan kebutuhan dan objektif bisnis secara detail, yaitu: menjelaskan masalah yang ingin diselesaikan, value proposition, dan kriteria sukses dari perspektif bisnis.

## Problem Statement

### Situasi Saat Ini
Banyak bisnis kecil dan tim masih mengelola kas kecil secara manual dengan menggunakan buku catatan fisik atau spreadsheet sederhana, dimana proses ini menimbulkan beberapa masalah, antara lain:

1. **Ketidakakuratan Data** - Pencatatan manual rentan terhadap kesalahan human error
2. **Kurangnya Visibilitas** - Sulit untuk mengetahui saldo kas kecil secara real-time
3. **Rekonsiliasi yang Memakan Waktu** - Proses pencocokan antara catatan dan uang fisik membutuhkan waktu lama
4. **Dokumentasi Tidak Terorganisir** - Bukti transaksi (receipt) sering hilang atau rusak
5. **Tidak Ada Accountability** - Sulit melacak siapa yang melakukan pengeluaran tertentu

### Dampak Bisnis
- Kehilangan waktu produktif 2-4 jam per minggu untuk rekonsiliasi manual
- Potensi kehilangan uang karena pencatatan yang tidak akurat
- Kesulitan dalam audit dan pelaporan keuangan
- Risiko fraud yang lebih tinggi karena kurangnya kontrol

## Business Objectives

### Primary Objectives

| ID | Objective | Description | Priority |
|----|-----------|-------------|----------|
| BO-01 | Digitalisasi Pencatatan | Menggantikan pencatatan manual dengan sistem digital | High |
| BO-02 | Real-time Visibility | Menyediakan informasi saldo dan transaksi secara real-time | High |
| BO-03 | Accountability | Melacak pengguna yang melakukan setiap transaksi | Medium |
| BO-04 | Documentation | Menyimpan bukti digital untuk setiap transaksi | Medium |
| BO-05 | Reporting | Menghasilkan laporan pengeluaran per kategori | Medium |

### Secondary Objectives

| ID | Objective | Description | Priority |
|----|-----------|-------------|----------|
| BO-06 | Time Efficiency | Mengurangi waktu rekonsiliasi sebesar 50% | Low |
| BO-07 | Accessibility | Akses dari berbagai device (mobile/desktop) | Low |
| BO-08 | Scalability | Mendukung multiple users dan teams | Low |

## Value Proposition

### Untuk End Users (Kasir/Admin)
- **Kemudahan** - Pencatatan pengeluaran dalam waktu kurang dari 30 detik
- **Akurasi** - Mengurangi kesalahan pencatatan dengan validasi otomatis
- **Mobilitas** - Akses dari smartphone untuk pencatatan on-the-go

### Untuk Finance Manager
- **Visibilitas** - Dashboard real-time untuk monitoring kas kecil
- **Kontrol** - Tracking pengeluaran per kategori dan per user
- **Efisiensi** - Rekonsiliasi lebih cepat dengan data digital

### Untuk Business Owner
- **Transparansi** - Informasi lengkap tentang penggunaan kas kecil
- **Compliance** - Dokumentasi lengkap untuk keperluan audit
- **Cost Saving** - Mengurangi potensi kehilangan dan fraud

## Functional Requirements Summary

### Epic 1: Basic Expense Recording (Sprint 1)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| BR-01 | User Registration | User dapat mendaftar dengan email dan password | Must Have |
| BR-02 | User Login | User dapat login dengan credentials | Must Have |
| BR-03 | Set Initial Balance | User dapat menetapkan saldo awal kas kecil | Must Have |
| BR-04 | Record Expense | User dapat mencatat pengeluaran dengan detail | Must Have |
| BR-05 | View Transactions | User dapat melihat daftar transaksi | Must Have |
| BR-06 | Add Replenishment | User dapat menambahkan dana ke kas kecil | Must Have |

### Epic 2: Receipt Management (Sprint 2)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| BR-07 | Capture Receipt | User dapat mengambil foto receipt | Should Have |
| BR-08 | Edit Transaction | User dapat mengedit transaksi yang sudah ada | Should Have |
| BR-09 | Delete Transaction | User dapat menghapus transaksi | Should Have |
| BR-10 | User Tracking | Setiap transaksi mencatat user yang membuat | Should Have |

### Epic 3: Reporting (Sprint 3)

| ID | Requirement | Description | Priority |
|----|-------------|-------------|----------|
| BR-11 | Basic Report | User dapat melihat laporan pengeluaran | Should Have |
| BR-12 | Export Data | User dapat export data ke CSV | Could Have |
| BR-13 | Dashboard | User dapat melihat overview di dashboard | Could Have |

## User Personas

### Persona 1: Admin Kantor (Primary User)

**Nama**: Siti (28 tahun)  
**Role**: Admin Kantor  
**Goals**: Mencatat pengeluaran dengan cepat dan akurat  
**Pain Points**: 
- Sering lupa mencatat pengeluaran kecil
- Receipt sering hilang atau rusak
- Kesulitan saat rekonsiliasi akhir bulan

**Needs**:
- Interface sederhana untuk input cepat
- Kemampuan untuk foto receipt
- Filter dan search transaksi

### Persona 2: Finance Manager

**Nama**: Budi (35 tahun)  
**Role**: Finance Manager  
**Goals**: Monitor dan kontrol penggunaan kas kecil  
**Pain Points**:
- Tidak tahu saldo kas kecil secara real-time
- Sulit tracking pengeluaran per kategori
- Proses audit memakan waktu

**Needs**:
- Dashboard dengan informasi real-time
- Laporan per kategori dan periode
- Export data untuk accounting

### Persona 3: Business Owner

**Nama**: Ahmad (45 tahun)  
**Role**: Pemilik Usaha Kecil  
**Goals**: Visibilitas dan kontrol keuangan bisnis  
**Pain Points**:
- Tidak yakin kas kecil digunakan dengan benar
- Kurang dokumentasi untuk audit
- Potensi kebocoran keuangan

**Needs**:
- Transparansi penggunaan dana
- Accountability siapa melakukan apa
- Dokumentasi lengkap

## Business Rules

### Transaction Rules
1. **BR-TRX-01**: Transaksi tidak dapat dibuat jika akan menyebabkan saldo negatif
2. **BR-TRX-02**: Tanggal transaksi tidak boleh di masa depan
3. **BR-TRX-03**: Deskripsi transaksi maksimal 200 karakter
4. **BR-TRX-04**: Setiap transaksi harus memiliki kategori

### User Rules
1. **BR-USR-01**: Password minimal 8 karakter
2. **BR-USR-02**: Email harus unique dalam sistem
3. **BR-USR-03**: User hanya dapat melihat transaksi sendiri (kecuali admin)

### Fund Rules
1. **BR-FND-01**: Saldo awal harus positif
2. **BR-FND-02**: Replenishment harus positif
3. **BR-FND-03**: Setiap perubahan saldo harus tercatat dengan timestamp

## Success Metrics

### Quantitative Metrics

| Metric | Current State | Target | Timeline |
|--------|---------------|--------|----------|
| Transaction Recording Time | 2-5 minutes | < 30 seconds | Launch |
| Reconciliation Time | 2-4 hours/week | < 1 hour/week | Month 1 |
| Data Accuracy | 85% | 99% | Month 1 |
| Receipt Documentation | 40% | 95% | Month 2 |

### Qualitative Metrics
- User satisfaction score (NPS > 40)
- Ease of use rating (> 4/5)
- Feature adoption rate (> 80%)

## Constraints

### Business Constraints
1. MVP harus selesai dalam 6 minggu
2. Budget terbatas pada resources internal
3. Fokus pada single currency (IDR) untuk MVP

### Regulatory Constraints
1. Harus comply dengan peraturan perlindungan data
2. Data keuangan harus disimpan dengan aman
3. Audit trail untuk transaksi harus tersedia

## Assumptions

1. Target users memiliki smartphone atau akses ke komputer
2. Users memiliki koneksi internet yang stabil
3. Users familiar dengan basic web/mobile applications
4. Tidak memerlukan approval workflow untuk MVP
5. Single timezone (WIB) untuk MVP

---

*Dokumen ini akan diperbarui seiring dengan evolusi kebutuhan bisnis dan feedback dari stakeholder.*

