# Test Suite Documentation

## Overview

This document describes the comprehensive test suite for the Multi-Tenant Flat & Bill Management System. The test suite ensures the reliability, security, and functionality of the multi-tenant architecture.

## Test Structure

### 1. Unit Tests (`tests/Unit/`)

Unit tests focus on testing individual components in isolation.

#### Models Tests
- **UserTest.php**: Tests user model functionality, role checking, relationships, and password hashing
- **BuildingTest.php**: Tests building model, relationships, scopes, and cascade deletion
- **FlatTest.php**: Tests flat model, relationships, scopes, and data validation
- **TenantTest.php**: Tests tenant model, relationships, scopes, and assignment logic
- **BillTest.php**: Tests bill model, relationships, scopes, status management, and amount casting

#### Middleware Tests
- **MultiTenantMiddlewareTest.php**: Tests multi-tenant middleware functionality and data isolation

### 2. Feature Tests (`tests/Feature/`)

Feature tests test the complete functionality of features from the user's perspective.

#### Authentication Tests
- **AuthenticationTest.php**: Tests login, registration, logout, and role-based access

#### Dashboard Tests
- **DashboardTest.php**: Tests dashboard functionality for both admin and house owner roles

#### Resource Management Tests
- **BuildingTest.php**: Tests building CRUD operations and admin-only access
- **FlatTest.php**: Tests flat management with multi-tenant isolation
- **TenantTest.php**: Tests tenant management and assignment logic
- **BillTest.php**: Tests bill management, email notifications, and payment tracking

### 3. Integration Tests (`tests/Integration/`)

Integration tests verify that different parts of the system work together correctly.

#### Multi-Tenant Tests
- **MultiTenantTest.php**: Tests complete multi-tenant data isolation and security

## Test Coverage

### Core Functionality
- ✅ User authentication and authorization
- ✅ Role-based access control (Admin vs House Owner)
- ✅ Multi-tenant data isolation
- ✅ CRUD operations for all entities
- ✅ Email notifications
- ✅ Data validation and constraints
- ✅ Cascade deletion
- ✅ Custom scopes and relationships

### Security Tests
- ✅ Data isolation between tenants
- ✅ Authorization checks for all operations
- ✅ Input validation and sanitization
- ✅ SQL injection prevention
- ✅ CSRF protection

### Business Logic Tests
- ✅ Bill status management
- ✅ Tenant assignment to flats
- ✅ Due amount calculations
- ✅ Email notification triggers
- ✅ Multi-tenant scoping

## Running Tests

### Prerequisites
```bash
# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Install test dependencies
composer require --dev phpunit/phpunit
```

### Running Individual Test Suites

```bash
# Run unit tests
php artisan test tests/Unit

# Run feature tests
php artisan test tests/Feature

# Run integration tests
php artisan test tests/Integration

# Run specific test file
php artisan test tests/Unit/UserTest.php

# Run specific test method
php artisan test --filter testUserCanLogin
```

### Running All Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run with minimum coverage threshold
php artisan test --coverage --min=80
```

### Using the Test Runner Script

```bash
# Make script executable
chmod +x run-tests.sh

# Run comprehensive test suite
./run-tests.sh
```

## Test Data

### Factories
The test suite uses Laravel factories to generate test data:

- **UserFactory**: Creates users with different roles
- **BuildingFactory**: Creates buildings with owners
- **FlatFactory**: Creates flats with building relationships
- **TenantFactory**: Creates tenants with building assignments
- **BillCategoryFactory**: Creates bill categories per building
- **BillFactory**: Creates bills with proper relationships

### Sample Test Data
Each test creates its own isolated data using factories, ensuring:
- No test interference
- Consistent test data
- Easy maintenance
- Realistic scenarios

## Test Scenarios

### Multi-Tenant Isolation Tests
1. **Data Isolation**: House owners can only access their building's data
2. **Admin Access**: Admins can access all data across all buildings
3. **Scope Testing**: Custom scopes filter data correctly
4. **Cascade Deletion**: Deleting a building removes all related data
5. **Middleware Enforcement**: Middleware properly isolates requests

### Authentication Tests
1. **Login/Logout**: Users can authenticate and logout
2. **Registration**: Users can register with proper role assignment
3. **Password Security**: Passwords are properly hashed
4. **Remember Me**: Remember functionality works correctly
5. **Role Validation**: Only valid roles can be assigned

### Business Logic Tests
1. **Bill Management**: Bills can be created, updated, and marked as paid
2. **Tenant Assignment**: Tenants can be assigned to flats within their building
3. **Email Notifications**: Emails are sent on bill creation and payment
4. **Data Validation**: All forms validate input correctly
5. **Status Management**: Bill status changes work correctly

## Test Assertions

### Database Assertions
```php
// Check data exists
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);

// Check data doesn't exist
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);

// Check count
$this->assertDatabaseCount('buildings', 3);
```

### Response Assertions
```php
// Check status code
$response->assertStatus(200);

// Check redirect
$response->assertRedirect('/dashboard');

// Check view
$response->assertViewIs('dashboard.admin');

// Check view data
$response->assertViewHas('buildings');
```

### Authentication Assertions
```php
// Check user is authenticated
$this->assertAuthenticated();

// Check specific user is authenticated
$this->assertAuthenticatedAs($user);

// Check user is not authenticated
$this->assertGuest();
```

## Continuous Integration

### GitHub Actions (Example)
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install dependencies
      run: composer install --no-progress --prefer-dist --optimize-autoloader
      
    - name: Run tests
      run: php artisan test --coverage --min=80
```

## Test Maintenance

### Adding New Tests
1. Create test file in appropriate directory (`Unit`, `Feature`, or `Integration`)
2. Use descriptive test method names starting with `test_` or `it_`
3. Follow AAA pattern: Arrange, Act, Assert
4. Use factories for test data generation
5. Add proper assertions and cleanup

### Test Data Management
- Use `RefreshDatabase` trait for database isolation
- Create specific test data for each test
- Avoid sharing test data between tests
- Use factories for consistent data generation

### Performance Considerations
- Use `RefreshDatabase` sparingly (only when needed)
- Consider using `DatabaseTransactions` for faster tests
- Mock external services (email, APIs)
- Use `Mail::fake()` for email testing

## Troubleshooting

### Common Issues
1. **Database Connection**: Ensure test database is configured
2. **Factory Issues**: Check factory definitions and relationships
3. **Authentication**: Verify user creation and login logic
4. **Middleware**: Check middleware registration and logic
5. **Email Testing**: Use `Mail::fake()` for email assertions

### Debugging Tests
```bash
# Run with verbose output
php artisan test --verbose

# Run specific test with debug
php artisan test --filter testUserCanLogin --verbose

# Check test database
php artisan tinker
>>> DB::connection()->getDatabaseName()
```

## Best Practices

1. **Test Isolation**: Each test should be independent
2. **Descriptive Names**: Use clear, descriptive test names
3. **Single Responsibility**: Test one thing per test method
4. **Arrange-Act-Assert**: Follow the AAA pattern
5. **Mock External Dependencies**: Use mocks for external services
6. **Cover Edge Cases**: Test both happy path and error scenarios
7. **Maintain Test Data**: Keep test data realistic and up-to-date

## Coverage Goals

- **Unit Tests**: 90%+ coverage for models and business logic
- **Feature Tests**: 80%+ coverage for user-facing functionality
- **Integration Tests**: 70%+ coverage for system integration
- **Overall Coverage**: 80%+ minimum threshold

This comprehensive test suite ensures the Multi-Tenant Flat & Bill Management System is robust, secure, and maintainable.



