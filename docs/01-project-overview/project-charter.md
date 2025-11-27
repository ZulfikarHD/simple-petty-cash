# Project Charter - Petty Cash App

## Overview

Dokumen ini merupakan project charter untuk Petty Cash App yang bertujuan untuk mendefinisikan tujuan, scope, stakeholder, dan parameter proyek secara keseluruhan, yaitu: memberikan panduan strategis bagi tim development dalam mengembangkan aplikasi manajemen kas kecil yang efektif dan user-friendly.

## Visi Produk

Menciptakan aplikasi mobile/web yang sederhana dan user-friendly untuk mendigitalisasi manajemen kas kecil, sehingga memudahkan bisnis kecil dan tim dalam melacak pengeluaran tunai, menyimpan bukti transaksi, dan menghasilkan laporan tanpa memerlukan keahlian akuntansi.

## Tujuan Proyek

### Tujuan Utama
1. **Digitalisasi Pencatatan** - Menggantikan pencatatan manual kas kecil dengan sistem digital yang terintegrasi
2. **Transparansi Keuangan** - Menyediakan visibilitas real-time terhadap saldo dan pengeluaran kas kecil
3. **Efisiensi Operasional** - Mengurangi waktu yang dibutuhkan untuk rekonsiliasi kas kecil sebesar 50%

### Tujuan Spesifik
- Menyediakan interface yang intuitif untuk pencatatan pengeluaran dalam waktu kurang dari 30 detik
- Memungkinkan pelacakan saldo kas kecil secara real-time
- Menghasilkan laporan pengeluaran per kategori dan periode waktu
- Menyimpan bukti transaksi dalam format digital

## Scope Proyek

### Dalam Scope (In-Scope)

| Fitur | Deskripsi | Sprint |
|-------|-----------|--------|
| User Authentication | Registrasi, login, dan manajemen sesi | Sprint 1 |
| Cash Fund Management | Set saldo awal dan replenishment | Sprint 1 |
| Transaction Recording | Pencatatan pengeluaran dengan kategori | Sprint 1 |
| Transaction History | Daftar dan filter transaksi | Sprint 1 |
| Receipt Capture | Upload foto bukti transaksi | Sprint 2 |
| Basic Reporting | Laporan pengeluaran per kategori | Sprint 3 |
| Data Export | Export data ke CSV | Sprint 3 |

### Di Luar Scope (Out-of-Scope)
- Multi-currency support
- Integration dengan software akuntansi eksternal (QuickBooks, Xero)
- Approval workflow kompleks
- OCR untuk ekstraksi data otomatis dari receipt
- Offline mode dengan sync

## Stakeholder

### Internal Stakeholder

| Role | Nama | Tanggung Jawab |
|------|------|----------------|
| Product Owner | TBD | Prioritisasi backlog, acceptance criteria |
| Scrum Master | TBD | Fasilitasi scrum ceremonies, remove blockers |
| Development Lead | TBD | Technical direction, code review |
| QA Lead | TBD | Testing strategy, quality assurance |

### External Stakeholder

| Role | Deskripsi | Interest |
|------|-----------|----------|
| End Users | Kasir, admin kantor, tim operasional | Kemudahan pencatatan pengeluaran |
| Finance Manager | Manajer keuangan perusahaan | Laporan dan rekonsiliasi |
| Business Owner | Pemilik bisnis kecil | Kontrol dan visibilitas keuangan |

## Constraints dan Assumptions

### Constraints (Batasan)
1. **Timeline**: MVP harus selesai dalam 3 sprint (6 minggu)
2. **Budget**: Menggunakan resources tim internal
3. **Technology**: Harus compatible dengan browser modern (Chrome, Firefox, Safari, Edge)
4. **Compliance**: Harus memenuhi standar keamanan data dasar

### Assumptions (Asumsi)
1. User memiliki akses internet yang stabil
2. User familiar dengan penggunaan aplikasi web dasar
3. Data pengeluaran tidak memerlukan audit trail yang kompleks
4. Single currency (IDR) untuk MVP

## Success Criteria

### Key Performance Indicators (KPIs)

| Metric | Target | Measurement |
|--------|--------|-------------|
| User Adoption | 80% target users aktif dalam 1 bulan | Analytics |
| Transaction Recording Time | < 30 detik per transaksi | User testing |
| System Availability | 99.5% uptime | Monitoring |
| User Satisfaction | NPS > 40 | Survey |
| Bug Rate | < 5 critical bugs post-launch | Issue tracker |

### Definition of Done (DoD)
- Kode telah ditulis sesuai coding standards
- Unit test ditulis dengan coverage > 80%
- Feature telah di-test pada browser target
- UI/UX sesuai dengan design specifications
- Code telah di-review dan di-approve
- Feature telah di-merge ke main branch
- Dokumentasi telah diupdate
- Product Owner telah menerima feature

## Timeline Overview

```
Sprint 1 (Week 1-2): Core Transaction Management
├── User Authentication ✅
├── Cash Fund Setup
├── Transaction Recording
└── Transaction List

Sprint 2 (Week 3-4): Receipt Management & Enhanced Features
├── Receipt Photo Capture
├── Edit/Delete Transactions
└── Multi-user Support

Sprint 3 (Week 5-6): Reporting & Polish
├── Basic Reports
├── Data Export
└── Dashboard Overview
```

## Risks dan Mitigations

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Scope creep | Medium | High | Strict backlog prioritization, change control |
| Technical debt | Medium | Medium | Code review, refactoring sprints |
| User adoption | Low | High | User testing, feedback loops |
| Security vulnerabilities | Low | High | Security review, penetration testing |

## Approval

| Role | Nama | Tanggal | Signature |
|------|------|---------|-----------|
| Project Sponsor | | | |
| Product Owner | | | |
| Development Lead | | | |

---

*Dokumen ini merupakan living document yang dapat diperbarui sesuai dengan perkembangan proyek.*

