# Petty Cash App - Scrum Framework

## Product Vision
Create a simple, user-friendly mobile/web application that digitizes petty cash management, making it easy for small businesses and teams to track cash expenses, maintain receipts, and generate reports without accounting expertise.

## Product Goal
Launch an MVP petty cash tracking app within 3 sprints that allows users to record expenses, capture receipts, and view basic reports.

---

## Product Backlog

### Sprint 1: Core Transaction Management (2 weeks)

#### Epic 1: Basic Expense Recording

**User Story 1.1: Record Cash Expense**
- **As a** user
- **I want to** quickly record a cash expense with amount, date, and description
- **So that** I can track where petty cash is being spent

**Acceptance Criteria:**
- User can enter expense amount (numeric input with 2 decimal places)
- User can select or enter current date/time
- User can add a brief description (max 200 characters)
- User can select from predefined categories (Office Supplies, Food/Beverages, Transportation, Miscellaneous, Other)
- System saves the transaction immediately
- User receives confirmation message

**Story Points:** 5

---

**User Story 1.2: View Transaction List**
- **As a** user
- **I want to** see a list of all recorded expenses
- **So that** I can review what has been spent

**Acceptance Criteria:**
- Display transactions in reverse chronological order (newest first)
- Show date, amount, category, and description for each transaction
- Include running balance display
- Support basic filtering by date range
- List updates in real-time when new expenses are added

**Story Points:** 3

---

**User Story 1.3: Set Initial Cash Balance**
- **As a** user
- **I want to** set the starting petty cash amount
- **So that** the app can track remaining balance accurately

**Acceptance Criteria:**
- User can enter initial cash fund amount on first use
- User can add cash (replenishment) at any time
- System calculates and displays current balance (initial + replenishments - expenses)
- Balance updates automatically after each transaction
- Balance cannot go negative (warning shown)

**Story Points:** 3

---

#### Epic 2: Basic UI/UX

**User Story 1.4: User Authentication**
- **As a** user
- **I want to** create an account and log in securely
- **So that** my petty cash data is private and secure

**Acceptance Criteria:**
- User can register with email and password
- User can log in with credentials
- Password must meet basic security requirements (8+ characters)
- User remains logged in across sessions (remember me)
- User can log out

**Story Points:** 5

---

### Sprint 2: Receipt Management & Enhanced Features (2 weeks)

#### Epic 3: Receipt Documentation

**User Story 2.1: Capture Receipt Photo**
- **As a** user
- **I want to** take or upload a photo of a receipt
- **So that** I have documentation for each expense

**Acceptance Criteria:**
- User can open camera to take receipt photo
- User can upload existing photo from gallery
- Photo is attached to the specific transaction
- Photo is compressed for storage efficiency
- User can view full-size photo by tapping thumbnail
- User can delete or replace receipt photo

**Story Points:** 8

---

**User Story 2.2: Edit/Delete Transactions**
- **As a** user
- **I want to** edit or delete incorrect transactions
- **So that** my records remain accurate

**Acceptance Criteria:**
- User can tap a transaction to edit all fields
- User can delete a transaction with confirmation prompt
- Balance recalculates automatically after edit/delete
- Deleted transactions are removed from list
- Edit history is maintained (optional for transparency)

**Story Points:** 5

---

#### Epic 4: User Management

**User Story 2.3: Multiple Users/Team Members**
- **As a** manager
- **I want to** see who made each expense
- **So that** I can track accountability

**Acceptance Criteria:**
- Each transaction records which user created it
- Transaction list shows user name/initials for each entry
- Users can filter transactions by person
- Admin can view all team transactions
- Regular users see only their own transactions (unless admin)

**Story Points:** 5

---

### Sprint 3: Reporting & Polish (2 weeks)

#### Epic 5: Reporting & Export

**User Story 3.1: Basic Expense Report**
- **As a** user
- **I want to** generate a summary report of expenses
- **So that** I can review spending patterns and reconcile cash

**Acceptance Criteria:**
- User can generate report for custom date range
- Report shows total spent by category
- Report shows total spent by user
- Report includes transaction count
- Report displays beginning balance, total spent, and ending balance
- Report is viewable on screen

**Story Points:** 5

---

**User Story 3.2: Export Data**
- **As a** user
- **I want to** export my expense data
- **So that** I can share it with accounting or import to other systems

**Acceptance Criteria:**
- User can export transactions as CSV file
- Export includes all transaction fields and user information
- CSV can be emailed or shared via standard sharing options
- Export includes receipts as attachment links or separate files
- User can select date range for export

**Story Points:** 5

---

**User Story 3.3: Dashboard Overview**
- **As a** user
- **I want to** see a quick overview of petty cash status
- **So that** I can understand spending at a glance

**Acceptance Criteria:**
- Dashboard shows current balance prominently
- Display spending for current month
- Show top 3 spending categories
- Display recent transactions (last 5)
- Include simple chart/graph of spending by category

**Story Points:** 8

---

## Definition of Done (DoD)

A user story is considered "Done" when:
1. Code is written and follows coding standards
2. Unit tests are written and passing (>80% coverage)
3. Feature has been tested on both iOS and Android (if mobile)
4. UI/UX matches design specifications
5. Code has been reviewed and approved by at least one team member
6. Feature is merged into main branch
7. Documentation is updated
8. Product Owner has accepted the feature

---

## Sprint Ceremonies

### Sprint Planning (Start of each sprint)
- **Duration:** 2 hours
- **Participants:** Scrum Master, Product Owner, Development Team
- **Outcome:** Sprint backlog with committed user stories

### Daily Standup
- **Duration:** 15 minutes
- **Time:** 9:30 AM daily
- **Questions:** What did I do yesterday? What will I do today? Any blockers?

### Sprint Review (End of sprint)
- **Duration:** 1 hour
- **Participants:** Scrum Team + Stakeholders
- **Outcome:** Demo of completed features, gather feedback

### Sprint Retrospective (After review)
- **Duration:** 1 hour
- **Participants:** Scrum Team
- **Outcome:** Identify what went well, what to improve, action items

---

## Backlog Refinement

Additional features for future sprints:
- Push notifications for low balance
- Budget limits and alerts
- OCR for automatic receipt data extraction
- Multi-currency support
- Approval workflows
- Integration with accounting software (QuickBooks, Xero)
- Offline mode with sync
- Advanced analytics and trends
