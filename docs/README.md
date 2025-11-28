# Petty Cash App - Dokumentasi

## Overview

Petty Cash App merupakan aplikasi manajemen kas kecil berbasis web yang bertujuan untuk mendigitalisasi proses pencatatan pengeluaran kas kecil, yaitu: memudahkan bisnis kecil dan tim dalam melacak pengeluaran tunai, menyimpan bukti transaksi, dan menghasilkan laporan tanpa memerlukan keahlian akuntansi khusus.

## Daftar Isi Dokumentasi

### 1. Project Overview
- [Project Charter](./01-project-overview/project-charter.md) - Tujuan, scope, dan stakeholder proyek
- [Business Requirements](./01-project-overview/business-requirements.md) - Kebutuhan dan objektif bisnis
- [Glossary](./01-project-overview/glossary.md) - Istilah dan definisi
- [Project Timeline](./01-project-overview/project-timeline.md) - Timeline dan milestone

### 2. Requirements
- [Functional Requirements](./02-requirements/functional-requirements.md) - Detail kebutuhan fitur
- [Non-Functional Requirements](./02-requirements/non-functional-requirements.md) - Performance, security, scalability
- [User Stories](./02-requirements/user-stories.md) - Kumpulan user stories
- [Acceptance Criteria](./02-requirements/acceptance-criteria.md) - Kriteria penerimaan fitur

### 3. Design
- [System Architecture](./03-design/system-architecture.md) - Arsitektur sistem
- [Database Schema](./03-design/database-schema.md) - ERD dan struktur tabel
- [API Design](./03-design/api-design.md) - Spesifikasi endpoint API
- [Design System](./03-design/ui-ux/design-system.md) - Colors, typography, components
- [User Flows](./03-design/ui-ux/user-flows.md) - Diagram alur pengguna
- [Security Design](./03-design/security-design.md) - Arsitektur keamanan

### 4. Technical
- [Setup Guide](./04-technical/setup-guide.md) - Panduan setup development environment
- [Coding Standards](./04-technical/coding-standards.md) - Standar dan konvensi kode
- [Git Workflow](./04-technical/git-workflow.md) - Strategi branching dan commit
- [Testing Strategy](./04-technical/testing-strategy.md) - Pendekatan dan standar testing
- [Deployment Guide](./04-technical/deployment-guide.md) - Prosedur deployment
- [Infrastructure](./04-technical/infrastructure.md) - Server, hosting, services

### 5. API Documentation
- [API Overview](./05-api-documentation/api-overview.md) - Informasi umum API
- [Authentication](./05-api-documentation/authentication.md) - Endpoint dan flow autentikasi
- [Transactions](./05-api-documentation/endpoints/transactions.md) - Endpoint transaksi
- [Categories](./05-api-documentation/endpoints/categories.md) - Endpoint kategori
- [Cash Funds](./05-api-documentation/endpoints/cash-funds.md) - Endpoint dana kas
- [Users](./05-api-documentation/endpoints/users.md) - Endpoint user management (Admin)
- [Error Codes](./05-api-documentation/error-codes.md) - Response error API

### 6. User Guides
- [User Manual](./06-user-guides/user-manual.md) - Panduan lengkap pengguna
- [Quick Start](./06-user-guides/quick-start.md) - Panduan memulai
- [FAQ](./06-user-guides/faq.md) - Pertanyaan yang sering diajukan
- [Troubleshooting](./06-user-guides/troubleshooting.md) - Solusi masalah umum

### 7. Development
- [Sprint Planning](./07-development/sprint-planning/) - Perencanaan sprint
- [Technical Decisions](./07-development/technical-decisions.md) - Architecture Decision Records
- [Code Review Guidelines](./07-development/code-review-guidelines.md) - Checklist code review
- [Changelog](./07-development/changelog.md) - Riwayat versi dan perubahan

### 8. Testing
- [Test Plan](./08-testing/test-plan.md) - Strategi testing keseluruhan
- [Test Cases](./08-testing/test-cases/) - Kasus uji per fitur
- [UAT Results](./08-testing/uat-results.md) - Hasil user acceptance testing

### 9. Operations
- [Monitoring](./09-operations/monitoring.md) - Setup monitoring dan logging
- [Backup & Recovery](./09-operations/backup-recovery.md) - Prosedur backup dan recovery
- [Maintenance](./09-operations/maintenance.md) - Prosedur maintenance
- [Incident Response](./09-operations/incident-response.md) - Penanganan insiden

## Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| Backend | Laravel | 12.x |
| Frontend | Vue.js + Inertia.js | 3.x / 2.x |
| Styling | Tailwind CSS | 4.x |
| Database | SQLite | - |
| Authentication | Laravel Fortify | 1.x |
| Type Safety | TypeScript + Wayfinder | - |
| Testing | PHPUnit | 11.x |

## Quick Links

- **Repository**: [GitHub](https://github.com/ZulfikarHD/simple-petty-cash)
- **Issue Tracker**: [GitHub Issues](https://github.com/ZulfikarHD/simple-petty-cash/issues)
- **CI/CD**: GitHub Actions

## Kontak Tim

**Author:** Zulfikar Hidayatullah  
**Contact:** +62 857-1583-8733

Untuk pertanyaan teknis atau feedback, silakan hubungi melalui issue tracker atau kontak di atas.

---

*Dokumentasi ini terakhir diperbarui: November 2025*

