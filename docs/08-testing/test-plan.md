# Test Plan - Petty Cash App

## Overview

Dokumen ini merupakan rencana testing untuk Petty Cash App yang bertujuan untuk mendefinisikan strategi, scope, dan pendekatan testing yang akan digunakan, yaitu: memastikan kualitas aplikasi melalui berbagai level testing dari unit hingga acceptance testing.

## Test Strategy

### Testing Levels

| Level | Description | Tools | Coverage Target |
|-------|-------------|-------|-----------------|
| Unit Tests | Test individual functions/methods | PHPUnit | 80%+ |
| Feature Tests | Test HTTP requests/responses | PHPUnit + Laravel | 100% critical paths |
| Integration Tests | Test component interactions | PHPUnit | Key integrations |
| E2E Tests | Test full user workflows | Manual | All user stories |

### Testing Principles

1. **Test-Driven Development (TDD)** - Write tests before implementation where practical
2. **Automated Testing** - All unit and feature tests must be automated
3. **Continuous Testing** - Tests run on every commit via CI/CD
4. **Independent Tests** - Each test must be independent and repeatable

## Test Scope

### In Scope

| Module | Test Types | Priority |
|--------|------------|----------|
| Authentication | Feature | High |
| Transaction CRUD | Unit, Feature | High |
| Balance Calculation | Unit | High |
| Cash Fund Management | Feature | High |
| Dashboard Data | Feature | Medium |
| Input Validation | Unit, Feature | High |
| Authorization | Feature | High |

### Out of Scope (MVP)

- Performance testing
- Load testing
- Security penetration testing
- Mobile-specific testing

## Test Environment

### Development Environment

```
PHP: 8.4.x
Laravel: 12.x
Database: SQLite (in-memory for tests)
Test Framework: PHPUnit 11.x
```

### Test Database Configuration

```php
// phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Test Cases

### 1. Authentication Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| AUTH-01 | Login screen renders | 200 OK |
| AUTH-02 | User can authenticate | Redirect to dashboard |
| AUTH-03 | Invalid password rejected | Error message displayed |
| AUTH-04 | User can logout | Redirect to home |
| AUTH-05 | Registration works | Account created |
| AUTH-06 | Rate limiting applied | 429 after X attempts |

### 2. Transaction Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| TRX-01 | View transaction list | 200 OK with data |
| TRX-02 | View create form | 200 OK with categories |
| TRX-03 | Create valid transaction | Redirect, DB updated |
| TRX-04 | Create exceeds balance | Validation error |
| TRX-05 | Update transaction | DB updated |
| TRX-06 | Delete transaction | Record removed |
| TRX-07 | Access other user's transaction | 403 Forbidden |
| TRX-08 | Validate required fields | Validation errors |
| TRX-09 | Validate description length | Error if > 200 chars |
| TRX-10 | Validate future date | Error if future |
| TRX-11 | Balance calculation correct | Correct amount |
| TRX-12 | Category spending calculation | Correct totals |
| TRX-13 | Guest cannot access | Redirect to login |

### 3. Cash Fund Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| FND-01 | View fund form | 200 OK |
| FND-02 | Add initial fund | Balance updated |
| FND-03 | Add replenishment | Balance increased |
| FND-04 | Validate required fields | Validation errors |
| FND-05 | Validate positive amount | Error if <= 0 |
| FND-06 | Validate future date | Error if future |
| FND-07 | Fund history displayed | All entries shown |
| FND-08 | Guest cannot access | Redirect to login |

## Test Implementation

### Feature Test Structure

```php
class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->default()->create();
    }

    public function test_user_can_view_transactions_list(): void
    {
        $this->actingAs($this->user);
        
        // Setup test data
        CashFund::factory()->create(['user_id' => $this->user->id]);
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        // Execute
        $response = $this->get('/transactions');

        // Assert
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('transactions/Index')
            ->has('transactions.data', 3)
        );
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TransactionTest.php

# Run specific test method
php artisan test --filter=test_user_can_create_transaction

# Run with coverage
php artisan test --coverage
```

## Test Results Summary

### Current Status (v0.1.0)

```
Tests:    62 passed (226 assertions)
Duration: 2.23s
Coverage: ~85% (estimated)
```

### Test Distribution

| Module | Tests | Assertions | Status |
|--------|-------|------------|--------|
| Auth | 26 | 78 | ✅ Pass |
| Transaction | 13 | 94 | ✅ Pass |
| Cash Fund | 8 | 28 | ✅ Pass |
| Settings | 15 | 26 | ✅ Pass |

## Acceptance Criteria Verification

### User Story 1.1: Record Cash Expense

| Criteria | Test ID | Status |
|----------|---------|--------|
| Numeric input with 2 decimals | TRX-03 | ✅ |
| Date selection | TRX-03 | ✅ |
| Description max 200 chars | TRX-09 | ✅ |
| Category selection | TRX-03 | ✅ |
| Immediate save | TRX-03 | ✅ |
| Confirmation message | TRX-03 | ✅ |

### User Story 1.2: View Transaction List

| Criteria | Test ID | Status |
|----------|---------|--------|
| Reverse chronological order | TRX-01 | ✅ |
| Show all details | TRX-01 | ✅ |
| Running balance | TRX-01 | ✅ |
| Date filter | TRX-01 | ✅ |
| Real-time update | Manual | ✅ |

### User Story 1.3: Set Initial Balance

| Criteria | Test ID | Status |
|----------|---------|--------|
| Enter initial amount | FND-02 | ✅ |
| Add replenishment | FND-03 | ✅ |
| Calculate balance | TRX-11 | ✅ |
| Auto-update | TRX-11 | ✅ |
| Prevent negative | TRX-04 | ✅ |

## Bug Reporting

### Bug Report Template

```markdown
**Bug ID**: BUG-XXX
**Title**: [Brief description]
**Severity**: Critical/High/Medium/Low
**Status**: New/In Progress/Fixed/Closed

**Steps to Reproduce**:
1. Step 1
2. Step 2
3. Step 3

**Expected Result**: What should happen
**Actual Result**: What actually happened

**Environment**: Browser, OS, etc.
**Screenshots**: If applicable
```

### Bug Severity Levels

| Level | Description | Response Time |
|-------|-------------|---------------|
| Critical | System down, data loss | Immediate |
| High | Major feature broken | Same day |
| Medium | Feature impaired | Next sprint |
| Low | Minor issue, cosmetic | Backlog |

## Continuous Integration

### GitHub Actions Workflow

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

## Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| QA Lead | | | |
| Dev Lead | | | |
| Product Owner | | | |

---

*Test plan ini akan diperbarui seiring dengan penambahan fitur dan test cases baru.*

