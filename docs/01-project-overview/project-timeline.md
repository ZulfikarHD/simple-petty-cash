# Project Timeline - Petty Cash App

## Overview

Dokumen ini merupakan timeline dan milestone proyek Petty Cash App yang bertujuan untuk memberikan gambaran jadwal pengembangan secara keseluruhan, yaitu: mencakup fase-fase development, target delivery, dan milestone penting yang harus dicapai.

## Timeline Overview

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           PETTY CASH APP TIMELINE                           │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  Week 1-2        Week 3-4        Week 5-6        Week 7+                   │
│  ┌──────┐        ┌──────┐        ┌──────┐        ┌──────┐                  │
│  │Sprint│        │Sprint│        │Sprint│        │Post  │                  │
│  │  1   │   →    │  2   │   →    │  3   │   →    │Launch│                  │
│  └──────┘        └──────┘        └──────┘        └──────┘                  │
│                                                                             │
│  Core TX         Receipt         Reporting       Maintenance               │
│  Management      Management      & Polish        & Enhancements            │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Sprint 1: Core Transaction Management (Week 1-2)

### Objectives
- Implementasi sistem autentikasi user
- Membangun core transaction recording functionality
- Setup initial balance dan fund management

### Deliverables

| Story | Description | Story Points | Status |
|-------|-------------|--------------|--------|
| 1.1 | Record Cash Expense | 5 | ✅ Done |
| 1.2 | View Transaction List | 3 | ✅ Done |
| 1.3 | Set Initial Cash Balance | 3 | ✅ Done |
| 1.4 | User Authentication | 5 | ✅ Done |

### Milestones

| Milestone | Target Date | Status |
|-----------|-------------|--------|
| Sprint 1 Planning | Day 1 | ✅ Done |
| Backend Models & Migrations | Day 3 | ✅ Done |
| Controllers & Routes | Day 5 | ✅ Done |
| Frontend Pages | Day 8 | ✅ Done |
| Testing & Bug Fixes | Day 10 | ✅ Done |
| Sprint 1 Review | Day 14 | ✅ Done |

### Total Story Points: 16

## Sprint 2: Receipt Management & Enhanced Features (Week 3-4)

### Objectives
- Implementasi capture dan upload receipt photo
- Membangun edit dan delete functionality untuk transaksi
- Menambahkan multi-user tracking

### Deliverables

| Story | Description | Story Points | Status |
|-------|-------------|--------------|--------|
| 2.1 | Capture Receipt Photo | 8 | 🔜 Planned |
| 2.2 | Edit/Delete Transactions | 5 | ✅ Done |
| 2.3 | Multiple Users/Team Members | 5 | 🔜 Planned |

### Milestones

| Milestone | Target Date | Status |
|-----------|-------------|--------|
| Sprint 2 Planning | Day 15 | ⏳ Pending |
| Receipt Upload Feature | Day 18 | ⏳ Pending |
| Edit/Delete Functionality | Day 21 | ⏳ Pending |
| Multi-user Implementation | Day 24 | ⏳ Pending |
| Testing & Bug Fixes | Day 26 | ⏳ Pending |
| Sprint 2 Review | Day 28 | ⏳ Pending |

### Total Story Points: 18

## Sprint 3: Reporting & Polish (Week 5-6)

### Objectives
- Membangun reporting functionality
- Implementasi data export
- Polish UI/UX dan dashboard

### Deliverables

| Story | Description | Story Points | Status |
|-------|-------------|--------------|--------|
| 3.1 | Basic Expense Report | 5 | 🔜 Planned |
| 3.2 | Export Data | 5 | 🔜 Planned |
| 3.3 | Dashboard Overview | 8 | ✅ Done |

### Milestones

| Milestone | Target Date | Status |
|-----------|-------------|--------|
| Sprint 3 Planning | Day 29 | ⏳ Pending |
| Report Generation | Day 32 | ⏳ Pending |
| Export Functionality | Day 35 | ⏳ Pending |
| Dashboard Enhancement | Day 38 | ⏳ Pending |
| Final Testing | Day 40 | ⏳ Pending |
| Sprint 3 Review | Day 42 | ⏳ Pending |

### Total Story Points: 18

## Release Schedule

### MVP Release (End of Sprint 3)
- **Target Date**: Week 6
- **Scope**: All Sprint 1-3 features
- **Environment**: Production

### Version Releases

| Version | Features | Target Date |
|---------|----------|-------------|
| v0.1.0 | Core transaction management | End Week 2 |
| v0.2.0 | Receipt management | End Week 4 |
| v1.0.0 | Full MVP release | End Week 6 |

## Dependencies

### External Dependencies
- Laravel Fortify untuk authentication
- Vue.js dan Inertia.js untuk frontend
- Tailwind CSS untuk styling
- SQLite untuk database

### Internal Dependencies

```
User Authentication (1.4)
        ↓
Set Initial Balance (1.3) → Record Expense (1.1) → View Transactions (1.2)
        ↓                           ↓
Add Replenishment        Edit/Delete (2.2) → Basic Report (3.1)
                                    ↓
                        Receipt Capture (2.1) → Export Data (3.2)
```

## Risk Timeline

| Risk | Impact Period | Mitigation Timeline |
|------|---------------|---------------------|
| Scope creep | Week 2-4 | Continuous backlog grooming |
| Technical debt | Week 3-6 | Dedicated refactoring time |
| Integration issues | Week 4-5 | Early integration testing |

## Communication Schedule

### Regular Meetings

| Meeting | Frequency | Duration | Participants |
|---------|-----------|----------|--------------|
| Daily Standup | Daily | 15 min | Dev Team |
| Sprint Planning | Bi-weekly | 2 hours | Full Team |
| Sprint Review | Bi-weekly | 1 hour | Team + Stakeholders |
| Retrospective | Bi-weekly | 1 hour | Dev Team |

### Reporting Schedule

| Report | Frequency | Audience |
|--------|-----------|----------|
| Sprint Progress | Weekly | Product Owner |
| Burndown Chart | Daily | Scrum Master |
| Quality Metrics | Per Sprint | QA Lead |

## Post-Launch Roadmap (Future)

### Phase 2: Enhanced Features (Week 7-10)
- Push notifications untuk low balance
- Budget limits dan alerts
- Advanced filtering dan search

### Phase 3: Integrations (Week 11-14)
- Integration dengan accounting software
- API untuk third-party access
- Webhook notifications

### Phase 4: Mobile & Offline (Week 15+)
- Native mobile app (React Native/Flutter)
- Offline mode dengan sync
- OCR untuk automatic receipt data extraction

---

*Timeline ini akan diperbarui sesuai dengan progress aktual dan perubahan prioritas.*

