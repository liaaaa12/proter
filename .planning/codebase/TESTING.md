# Testing

## Frameworks

- **PHPUnit**: Used for both Unit and Feature testing in Laravel.

## Structure

- `tests/Unit`: Low-level tests for isolated logic (e.g., helpers, model methods).
- `tests/Feature`: Integration tests for endpoints, controllers, and database interactions.

## Configuration

- `phpunit.xml`: Configured to use an in-memory SQLite database (`:memory:`) for fast test execution.
- Environment variables for testing are defined in the `<php>` block of `phpunit.xml`.

## Execution

Run tests using:

```bash
php artisan test
```

or

```bash
vendor/bin/phpunit
```
