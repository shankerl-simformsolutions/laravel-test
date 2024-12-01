# laravel-test
Set up the laravel test with Unit, Functional, Integration with code coverage analysis

## Install dependency using the composer command to set up the required dependency
1. Run git clone https://github.com/jeremykenedy/laravel-auth.git laravel-auth
2. Create a MySQL database for the project
3. Run php artisan migrate & php artisan db:seed
4. From the projects root run cp .env.example .env
5. Configure your .env file
6. Install composer, php-mysql, php-ext and php-dom (dependent on your distribution, For Debian run apt install composer php-mysql php-ext php-dom)
7. Run composer update from the project root folder

# Test Automation and Code Coverage Guide

## Test Structure
Our testing suite is organized into three main categories:

1. **Unit Tests** (`tests/Unit/`)
   - Focus on testing individual components in isolation
   - Fast execution and high coverage of core logic
   - Example: `UserTest.php` for user-related functionality

2. **Feature Tests** (`tests/Feature/`)
   - Test complete features and API endpoints
   - Example: `ApiUserTest.php` for testing user-related API endpoints

3. **Integration Tests** (`tests/Integration/`)
   - Test interaction between multiple components
   - Example: `UserServiceTest.php` for testing user service workflows

## Code Coverage Requirements

### Coverage Targets
- Overall code coverage target: 80%
- Critical components (user management, authentication): 90%
- Unit test coverage: 70%
- Feature test coverage: 20%
- Integration test coverage: 10%

### Running Tests with Coverage

To run tests with coverage analysis:

```bash
# Run all tests with coverage report
php artisan test --coverage

# Run specific test suite with coverage
php artisan test --coverage --testsuite=Unit
php artisan test --coverage --testsuite=Feature
php artisan test --coverage --testsuite=Integration

# Generate HTML coverage report
./vendor/bin/pest --coverage --min=0 --coverage-html=./coverage
```

## Test Automation Strategy

### Continuous Integration
1. All tests must pass before merging PRs
2. Coverage reports generated on each CI run
3. Coverage thresholds enforced in CI pipeline

### Best Practices
1. Write tests for new features before implementation (TDD)
2. Maintain test isolation and independence
3. Use descriptive test names following the pattern:
   - Unit: `it_does_something_specific()`
   - Feature: `users_can_perform_action()`
   - Integration: `it_completes_workflow_successfully()`

### Key Test Areas
1. User Management
   - Registration
   - Profile updates
   - Avatar uploads
   - Role management
   - Subscription handling
   - Account deletion

2. API Endpoints
   - Authentication
   - Profile operations
   - Data validation
   - Error handling

3. Service Integration
   - Event handling
   - Database operations
   - External service interaction

## Setting Up Local Test Environment

1. Configure phpunit.xml:
   - Use SQLite for testing
   - Enable memory database
   - Configure test environment variables

2. Install dependencies:
```bash
composer require --dev phpunit/phpunit
composer require --dev phpunit/php-code-coverage
```

3. Configure IDE for test running and debugging

## Maintaining Tests

1. Regular test maintenance schedule
2. Update tests when requirements change
3. Review and refactor test code
4. Document test scenarios and edge cases

## Screenshot of Test Coverage Dashboard

![image](https://github.com/user-attachments/assets/010639d6-6162-4bca-99a4-c17bff1793e9)

![image](https://github.com/user-attachments/assets/8647fbf9-7fd8-49af-a2ba-d739a3302005)

![image](https://github.com/user-attachments/assets/8613441a-c1c5-4509-949b-4e541b272df5)
