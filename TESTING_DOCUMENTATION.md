# Laravel Testing Documentation

This document provides an overview of the different types of tests implemented in this project.

## Types of Tests

### 1. Unit Tests (`tests/Unit/`)
Unit tests focus on testing individual components in isolation. Examples include:
- Testing model methods
- Testing helper functions
- Testing individual service methods
- Validation rules testing

### 2. Feature Tests (`tests/Feature/`)
Feature tests focus on testing full features from HTTP request to response. Examples include:
- Testing registration process
- Testing login functionality
- Testing profile management
- Testing authentication flows

### 3. Integration Tests (`tests/Integration/`)
Integration tests focus on testing multiple components working together. Examples include:
- Testing service classes that use multiple models
- Testing event/listener combinations
- Testing notification systems
- Testing file upload with storage

## Test Structure

Each test follows the Arrange-Act-Assert (AAA) pattern:
1. **Arrange**: Set up the test data and conditions
2. **Act**: Perform the action being tested
3. **Assert**: Verify the results

## Code Coverage Analysis

To generate code coverage reports:

```bash
php artisan test --coverage
```

Or for HTML reports:

```bash
php artisan test --coverage-html reports/
```

## Best Practices

1. Use descriptive test names that explain the scenario being tested
2. Use the RefreshDatabase trait when testing database operations
3. Fake events and notifications when not directly testing them
4. Use factories for generating test data
5. Test both successful and failure scenarios
6. Test edge cases and boundary conditions

## Testing Traits Used

- RefreshDatabase: Resets the database after each test
- WithFaker: Provides fake data generation
- WithoutMiddleware: Bypasses middleware when needed
- DatabaseMigrations: Runs migrations for each test

## Assertions Used

- assertEquals: Verify exact matches
- assertInstanceOf: Verify object types
- assertDatabaseHas: Verify database records exist
- assertStatus: Verify HTTP status codes
- assertTrue/assertFalse: Verify boolean conditions
- assertAuthenticated: Verify authentication status

## Model Factory Example

```php
User::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password123')
]);
```

## Testing Environment

The testing environment is configured in `phpunit.xml`:
- Uses SQLite in-memory database
- Disables certain services (Telescope, Pulse)
- Sets environment variables for testing

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Unit/UserTest.php

# Run specific test method
php artisan test --filter unit=  it_registers_new_user_and_triggers_events

# Run with coverage report
php artisan test --coverage
```