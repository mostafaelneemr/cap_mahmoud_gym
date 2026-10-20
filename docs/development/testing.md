# Test Suite & Database Isolation Strategy

**Project:** Captain Mahmoud Fitness Management System  
**Last Updated:** October 2026 (Task 1)

---

## 1. Overview and Purpose

This document outlines the testing architecture, environment isolation rules, and execution guidelines for the Captain Mahmoud Fitness Management System.

To protect live trainee records, workout programs, and database integrity, all automated tests must execute in an isolated environment that completely prevents accidental reads or writes to the local development MySQL database.

---

## 2. Test Execution & How to Run Tests

### Running the Entire Test Suite
Execute the test runner from the project root:

```bash
php vendor/phpunit/phpunit/phpunit
```
Or via artisan:
```bash
php artisan test
```

### Running Specific Test Suites
```bash
# Run unit tests only
php vendor/phpunit/phpunit/phpunit --testsuite=Unit

# Run feature / smoke tests only
php vendor/phpunit/phpunit/phpunit --testsuite=Feature

# Run a specific test class
php vendor/phpunit/phpunit/phpunit tests/Feature/ExampleTest.php
```

---

## 3. Database Isolation Architecture

### Active Test Connection
Automated tests are configured to use an **in-memory SQLite database**:
* **Driver:** `sqlite`
* **Database Target:** `:memory:`
* **Default Connection:** `sqlite` (overridden strictly for testing)

### Isolation Enforcement in `phpunit.xml`
In `phpunit.xml`, testing variables are enforced using the `force="true"` attribute:

```xml
<php>
    <env name="APP_ENV" value="testing" force="true"/>
    <env name="BCRYPT_ROUNDS" value="4" force="true"/>
    <env name="CACHE_DRIVER" value="array" force="true"/>
    <env name="DB_CONNECTION" value="sqlite" force="true"/>
    <env name="DB_DATABASE" value=":memory:" force="true"/>
    <env name="MAIL_MAILER" value="array" force="true"/>
    <env name="QUEUE_CONNECTION" value="sync" force="true"/>
    <env name="SESSION_DRIVER" value="array" force="true"/>
    <env name="TELESCOPE_ENABLED" value="false" force="true"/>
</php>
```

This guarantees that:
1. `DB_CONNECTION` is overridden to `sqlite`, preventing any fallback to the `mysql` connection defined in `.env`.
2. `DB_DATABASE` is overridden to `:memory:`, ensuring no file or persistent storage is touched.
3. Sessions, queues, and caches use non-persistent memory drivers (`array`, `sync`).

---

## 4. Verification and Smoke Testing

A dedicated smoke test in `tests/Feature/ExampleTest.php` asserts:
1. **Application Boot:** Verifies the Laravel framework boots correctly in the `testing` environment.
2. **Connection Type:** Verifies `config('database.default')` is `'sqlite'` and that `DB::connection()` is an instance of `Illuminate\Database\SQLiteConnection`.
3. **Database Name:** Verifies that `DB::connection()->getDatabaseName()` and configuration target are strictly `':memory:'`.
4. **Execution Safety:** Verifies raw query execution via SQLite (`SELECT sqlite_version()`) and asserts that no development application tables exist in the clean in-memory database.

---

## 5. Strict Prohibitions for Developers

To maintain data safety, developers must **NEVER** run the following commands on the application database:

* `php artisan migrate:fresh` (wipes all tables and data)
* `php artisan db:wipe` (drops all database objects)
* Any manual `DROP TABLE` or `TRUNCATE` queries against the MySQL development database.
* Running test suites without verifying that `DB_CONNECTION=sqlite` is active.

---

## 6. Current Limitations & Safe Path for Future Feature Tests

1. **Schema Prerequisites:**
   * The baseline migration (`2026_09_29_000000_create_baseline_schema.php`) is currently pending in MySQL, and two production tables (`trainee_progress_checkins`, `trainee_progress_photos`) are missing from migrations.
   * Do not use database migration traits (`RefreshDatabase`, `DatabaseMigrations`) in feature tests until migrations are synchronized and verified to be compatible with SQLite.
2. **Future Path:**
   * In subsequent tasks, when migrations are fully harmonized, feature tests testing specific business logic (e.g. workout planning, trainee creation) should either use SQLite schema builders or mock repository/service layers for fast, isolated verification.
