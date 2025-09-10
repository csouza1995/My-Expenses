# Testing Guide - Expense Management System

## 📋 Overview

This project uses **Codeception** for automated testing with complete coverage:
- **Unit Tests** (24 tests)
- **Functional Tests** (23 tests) 
- **Acceptance Tests** (16 tests)
- **API Tests** (27 tests)

## 🚀 Quick Setup

### Prerequisites
- PHP 7.4+
- Composer
- SQLite3

### First Setup
```bash
# 1. Clone the repository
git clone <repository-url>
cd exam/src

# 2. Install dependencies
composer install

# 3. Tests auto-configure on first run!
vendor/bin/codecept run
```

## 🔄 Test Database

### Automatic Configuration
The system automatically configures the test database:
- **Location**: `tests/_output/test.db`
- **Schema**: Created automatically if it doesn't exist
- **Test data**: Inserted automatically

### Test Credentials
```
Email: tester@example.com
Password: ABCdef123!@#
```

### Manual Reset (if needed)
```bash
# Complete database reset
php tests/_data/reset_test_db.php

# Or delete the file and run tests
rm tests/_output/test.db
vendor/bin/codecept run
```

## 🧪 Running Tests

### All Tests
```bash
vendor/bin/codecept run
```

### By Category
```bash
# Unit Tests
vendor/bin/codecept run unit

# Functional Tests  
vendor/bin/codecept run functional

# Acceptance Tests
vendor/bin/codecept run acceptance

# API Tests
vendor/bin/codecept run api
```

### Specific Tests
```bash
# Specific test
vendor/bin/codecept run api:ApiAuthCest:testLoginSuccess

# By group/tag
vendor/bin/codecept run -g auth
```

### With Verbose/Debug
```bash
# More details
vendor/bin/codecept run --debug

# Stop on first error
vendor/bin/codecept run --fail-fast
```

## 📊 Test Structure

### Unit Tests (`tests/unit/`)
- **LoginFormTest**: Login form validation
- **UserTest**: User model and authentication
- **AlertTest**: Alert widget tests

### Functional Tests (`tests/functional/`)
- **LoginFormCest**: Complete login flow
- **ExpenseFormCest**: Expense management via forms

### Acceptance Tests (`tests/acceptance/`)
- **LoginCest**: End-to-end login
- **ExpenseCest**: Expense listing
- **HomeCest**: Main navigation
- **SecurityCest**: Application security

### API Tests (`tests/api/`)
- **ApiAuthCest**: JWT authentication
- **ApiExpenseCest**: Expense CRUD via API

## 🛠 Test Data

### Test User
```php
ID: 1
Name: Test User
Email: tester@example.com
Password: ABCdef123!@#
```

### Test Categories
1. Food
2. Transport  
3. Entertainment
4. Health
5. Education

### Sample Expenses
- Lunch at restaurant - $25.50 (Food)
- Bus ticket - $3.75 (Transport)
- Movie tickets - $18.00 (Entertainment)

## 🔧 Troubleshooting

### Corrupted Database
```bash
# Complete reset
php tests/_data/reset_test_db.php
```

### Authentication Failures
Check if test user exists:
```bash
sqlite3 tests/_output/test.db "SELECT email FROM users WHERE email='tester@example.com';"
```

### Clean Slate
```bash
# Remove all test files
rm -rf tests/_output/*
vendor/bin/codecept run
```

## 📝 Adding New Tests

### Unit Test
```bash
vendor/bin/codecept generate:test unit NewModelTest
```

### Functional Test  
```bash
vendor/bin/codecept generate:cest functional NewFeatureCest
```

### API Test
```bash
vendor/bin/codecept generate:cest api NewApiCest
```

## 📈 Current Test Status

### ✅ Fully Working (100%)
- **Acceptance Tests**: 16/16 ✅ - End-to-end navigation and login
- **Unit Tests**: 24/24 ✅ (100%) - Model validation and business logic
- **Functional Tests**: 23/23 ✅ (100%) - Complete application flows
- **API Tests**: 27/27 ✅ (100%) - Authentication and CRUD operations

### 🎯 Overall Status: 90/90 tests passing (100%)

## 🔍 Debug and Logs

### Debug Files
- `tests/_output/`: Screenshots and failure logs
- `runtime/logs/`: Application logs

### API Debug
```bash
# View API requests/responses
vendor/bin/codecept run api --debug
```

## 🚨 CI/CD

### For Continuous Integration
```bash
# CI Script
#!/bin/bash
composer install --no-dev --optimize-autoloader
php tests/_data/reset_test_db.php
vendor/bin/codecept run --xml
```

---

**Note**: The testing system is completely automated. After `git pull`, simply run `vendor/bin/codecept run` and everything will be configured automatically!
