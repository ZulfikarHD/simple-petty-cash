# Sprint 1 Planning - Core Transaction Management

## Overview

Dokumen ini merupakan catatan perencanaan Sprint 1 untuk Petty Cash App yang bertujuan untuk mendokumentasikan scope, goals, dan deliverables sprint pertama, yaitu: membangun fondasi core functionality untuk pencatatan transaksi kas kecil.

## Sprint Information

| Item | Detail |
|------|--------|
| Sprint Number | 1 |
| Duration | 2 weeks |
| Start Date | Week 1 |
| End Date | Week 2 |
| Sprint Goal | Deliver core transaction management functionality |

## Sprint Goal

Membangun sistem dasar untuk:
1. User authentication (login/register)
2. Manajemen saldo kas kecil
3. Pencatatan pengeluaran
4. Tampilan daftar transaksi

## Sprint Backlog

### User Stories

| ID | Story | Points | Owner | Status |
|----|-------|--------|-------|--------|
| US-1.1 | Record Cash Expense | 5 | Dev Team | ✅ Done |
| US-1.2 | View Transaction List | 3 | Dev Team | ✅ Done |
| US-1.3 | Set Initial Cash Balance | 3 | Dev Team | ✅ Done |
| US-1.4 | User Authentication | 5 | Dev Team | ✅ Done |

**Total Story Points**: 16

### Technical Tasks

| Task | Story | Estimate | Status |
|------|-------|----------|--------|
| Setup Laravel project | - | 2h | ✅ Done |
| Configure Fortify auth | US-1.4 | 4h | ✅ Done |
| Create database migrations | All | 2h | ✅ Done |
| Create Eloquent models | All | 2h | ✅ Done |
| Create model factories | All | 2h | ✅ Done |
| Create CategorySeeder | US-1.1 | 1h | ✅ Done |
| Create TransactionService | All | 4h | ✅ Done |
| Create TransactionController | US-1.1, 1.2 | 4h | ✅ Done |
| Create CashFundController | US-1.3 | 2h | ✅ Done |
| Create Form Requests | All | 2h | ✅ Done |
| Setup routes | All | 1h | ✅ Done |
| Create Dashboard.vue | US-1.2 | 4h | ✅ Done |
| Create transactions/Index.vue | US-1.2 | 4h | ✅ Done |
| Create transactions/Create.vue | US-1.1 | 3h | ✅ Done |
| Create transactions/Edit.vue | US-1.1 | 2h | ✅ Done |
| Create cash-fund/Create.vue | US-1.3 | 2h | ✅ Done |
| Update navigation | All | 1h | ✅ Done |
| Write feature tests | All | 4h | ✅ Done |

## Definition of Done

- [x] Code written and follows coding standards
- [x] Unit tests written and passing (>80% coverage)
- [x] Feature tested on Chrome, Firefox, Safari
- [x] UI matches iOS design specifications
- [x] Code reviewed and approved
- [x] Merged to main branch
- [x] Documentation updated

## Technical Decisions

### Architecture Decisions

| Decision | Rationale |
|----------|-----------|
| Use Service class | Separate business logic from controllers |
| SQLite for MVP | Simpler setup, easy migration to MySQL later |
| Inertia.js | No separate API needed, type-safe with Wayfinder |
| Tailwind CSS v4 | Modern styling, easy dark mode |

### Design Decisions

| Decision | Rationale |
|----------|-----------|
| iOS-style UI | Familiar, modern look, good UX patterns |
| Indonesian labels | Target market is Indonesian users |
| Category with colors | Visual differentiation, better UX |
| Balance validation | Prevent negative balance issues |

## Risks and Mitigations

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Scope creep | Medium | High | Strict backlog discipline |
| Auth complexity | Low | Medium | Use battle-tested Fortify |
| UI polish delays | Medium | Low | Focus on functionality first |

## Daily Standup Notes

### Day 1-3: Setup & Infrastructure
- Project scaffolding complete
- Auth system configured
- Database schema designed and migrated

### Day 4-6: Backend Development
- Models and relationships created
- Service class implemented
- Controllers and validation ready

### Day 7-10: Frontend Development
- Dashboard page built
- Transaction CRUD pages complete
- Cash fund management ready

### Day 11-14: Testing & Polish
- Feature tests written (21 tests)
- UI polish and animations
- Bug fixes and refinements

## Sprint Metrics

### Velocity

| Metric | Value |
|--------|-------|
| Committed Points | 16 |
| Completed Points | 16 |
| Velocity | 16 |

### Quality Metrics

| Metric | Value |
|--------|-------|
| Tests Written | 21 |
| Tests Passing | 21 (100%) |
| Assertions | 94 |
| Bugs Found | 0 critical |

## Sprint Review Notes

### Demo Items
1. User registration and login
2. Initial balance setup
3. Transaction recording flow
4. Transaction list with filtering
5. Dashboard overview
6. Edit/Delete functionality

### Feedback Received
- Dashboard looks great with iOS styling
- Need to add receipt capture (Sprint 2)
- Consider adding charts/graphs
- Export functionality requested

### Action Items
- Plan receipt capture for Sprint 2
- Investigate charting libraries
- Research CSV export options

## Sprint Retrospective

### What Went Well
- Clear requirements from scrum.md
- Fortify saved time on auth
- Tailwind v4 made styling efficient
- Test-first approach caught bugs early

### What Could Improve
- More frontend planning upfront
- Better estimation for UI polish
- Earlier integration testing

### Action Items for Next Sprint
- Create wireframes before coding
- Add buffer for UI work
- Start integration tests earlier

## Artifacts

### Files Created

**Backend:**
- 3 migrations
- 3 models + factories
- 1 seeder
- 1 service class
- 4 controllers
- 3 form requests

**Frontend:**
- 5 Vue pages
- Updated navigation
- TypeScript types

**Tests:**
- 2 test files
- 21 test methods

### Documentation Created
- Updated README
- API documentation (partial)
- Test documentation

---

*Sprint 1 completed successfully with all planned stories delivered.*

