# Glossary - Petty Cash App

## Overview

Dokumen ini merupakan kumpulan istilah dan definisi yang digunakan dalam proyek Petty Cash App, yaitu: bertujuan untuk memastikan pemahaman yang konsisten di seluruh tim terhadap terminologi teknis dan bisnis yang digunakan dalam pengembangan aplikasi.

## Istilah Bisnis

### Kas Kecil (Petty Cash)
Dana tunai dalam jumlah kecil yang disediakan untuk membiayai pengeluaran-pengeluaran rutin yang nilainya relatif kecil dan tidak praktis jika dibayar dengan cek atau transfer bank, contoh: pembelian alat tulis kantor, biaya parkir, atau pembelian makanan untuk rapat.

### Saldo (Balance)
Jumlah dana kas kecil yang tersedia pada waktu tertentu, yang dihitung dari total dana yang disetorkan dikurangi total pengeluaran yang telah tercatat.

### Replenishment
Proses pengisian ulang kas kecil ketika saldo sudah mencapai batas minimum atau habis, dimana dana baru ditambahkan untuk mengembalikan saldo ke jumlah yang ditentukan.

### Transaksi (Transaction)
Setiap aktivitas pengeluaran atau pemasukan yang tercatat dalam sistem kas kecil, yang mencakup informasi seperti tanggal, jumlah, kategori, dan deskripsi.

### Kategori (Category)
Klasifikasi pengeluaran berdasarkan jenis atau tujuannya, antara lain: Office Supplies, Food & Beverages, Transportation, Miscellaneous, dan Other.

### Bukti Transaksi (Receipt)
Dokumen fisik atau digital yang membuktikan terjadinya transaksi, yang biasanya berupa struk pembelian, faktur, atau kwitansi.

### Rekonsiliasi (Reconciliation)
Proses pencocokan antara catatan transaksi dalam sistem dengan uang tunai fisik yang tersedia, yang dilakukan untuk memastikan akurasi pencatatan.

## Istilah Teknis

### Backend
Bagian dari aplikasi yang berjalan di server dan menangani business logic, database operations, dan API endpoints, dimana dalam proyek ini menggunakan Laravel framework.

### Frontend
Bagian dari aplikasi yang berjalan di browser pengguna dan menangani user interface serta interaksi pengguna, dimana dalam proyek ini menggunakan Vue.js dengan Inertia.js.

### API (Application Programming Interface)
Interface yang memungkinkan komunikasi antara frontend dan backend melalui HTTP requests, yang menggunakan format JSON untuk pertukaran data.

### Inertia.js
Library yang menghubungkan backend Laravel dengan frontend Vue.js tanpa memerlukan API terpisah, yang memungkinkan pengembangan SPA (Single Page Application) dengan pendekatan monolithic.

### Wayfinder
Package Laravel yang menggenerate TypeScript types dan route functions secara otomatis, yang memastikan type safety antara backend routes dan frontend code.

### Migration
File yang mendefinisikan perubahan struktur database dalam Laravel, yang memungkinkan version control dan deployment database schema yang konsisten.

### Seeder
Script yang mengisi database dengan data awal atau testing data, yang digunakan untuk setup default categories dan development data.

### Factory
Class yang mendefinisikan cara membuat model instances untuk testing, yang menggunakan Faker untuk generate realistic dummy data.

### Form Request
Class Laravel yang menangani validasi input dari HTTP request, yang memisahkan validation logic dari controller untuk clean code.

### Service Class
Class yang mengenkapsulasi business logic, yang memisahkan logic dari controller untuk better maintainability dan testability.

### Eloquent
ORM (Object-Relational Mapping) Laravel yang memungkinkan interaksi dengan database menggunakan PHP objects, yang menyederhanakan query building dan relationship management.

## Istilah UI/UX

### Dashboard
Halaman utama aplikasi yang menampilkan overview informasi penting, antara lain: saldo saat ini, pengeluaran bulan ini, dan transaksi terakhir.

### iOS-style Design
Pendekatan desain yang mengikuti prinsip-prinsip Apple iOS, yang mencakup spring animations, press feedback, glass effects, dan haptic feedback.

### Spring Animation
Animasi yang menggunakan physics-based motion dengan efek bouncy, yang memberikan feedback visual yang natural dan responsive.

### Haptic Feedback
Umpan balik taktil melalui vibrasi device, yang memberikan konfirmasi fisik saat user melakukan action tertentu.

### Swipe Gesture
Interaksi dengan menggeser jari pada layar sentuh, yang digunakan untuk reveal actions seperti edit atau delete pada transaction list.

### Skeleton Loading
Placeholder visual yang ditampilkan saat content sedang loading, yang menunjukkan struktur layout sebelum actual data ditampilkan.

## Akronim

| Akronim | Kepanjangan | Deskripsi |
|---------|-------------|-----------|
| API | Application Programming Interface | Interface untuk komunikasi antar sistem |
| CRUD | Create, Read, Update, Delete | Operasi dasar pada data |
| CSS | Cascading Style Sheets | Styling untuk web pages |
| ERD | Entity Relationship Diagram | Diagram hubungan antar entitas database |
| HTML | HyperText Markup Language | Markup language untuk web pages |
| HTTP | HyperText Transfer Protocol | Protocol untuk komunikasi web |
| IDE | Integrated Development Environment | Software untuk development |
| JSON | JavaScript Object Notation | Format pertukaran data |
| JWT | JSON Web Token | Standard untuk authentication tokens |
| MVC | Model-View-Controller | Architecture pattern |
| NPS | Net Promoter Score | Metric untuk customer satisfaction |
| ORM | Object-Relational Mapping | Teknik mapping objects ke database |
| SPA | Single Page Application | Web app yang tidak reload page |
| SQL | Structured Query Language | Language untuk database queries |
| UI | User Interface | Tampilan visual aplikasi |
| UX | User Experience | Pengalaman pengguna |
| MVP | Minimum Viable Product | Versi minimal produk yang bisa digunakan |

## Status dan State

### Transaction Status
- **Created** - Transaksi baru dibuat dan tersimpan
- **Updated** - Transaksi telah diperbarui
- **Deleted** - Transaksi telah dihapus

### User Session State
- **Guest** - Pengguna belum login
- **Authenticated** - Pengguna sudah login
- **Verified** - Email pengguna sudah terverifikasi

## Units dan Format

### Currency Format
- Format: IDR (Indonesian Rupiah)
- Display: `Rp 1.000.000`
- Storage: Decimal dengan 2 digit presisi

### Date Format
- Display: `27 Nov 2025` atau `Kam, 27 Nov 2025`
- Storage: `YYYY-MM-DD`
- Timezone: WIB (UTC+7)

### Amount Constraints
- Minimum: Rp 0.01
- Maximum: Rp 9,999,999.99
- Decimal places: 2

---

*Glossary ini akan diperbarui seiring dengan penambahan fitur dan istilah baru dalam proyek.*

