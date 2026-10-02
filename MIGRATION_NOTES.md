# Database Baseline & Migration Guide

## Context & State of the Database
The live database (`gym`) was originally created manually outside of standard Laravel migrations and was further updated after `shaltout.sql` was dumped.
Prior to this baseline setup:
- The `migrations` table in `gym` contained only 4 default Laravel migrations from Laravel's initial setup.
- The 3 Spatie `activity_log` migrations in `database/migrations` were not recorded in the `migrations` table and would fail if executed against an existing database (`table already exists`).
- The live schema contains custom adjustments:
  - Custom `activity_log` schema (integer ID, IP, user agent, URL columns, custom properties JSON, no `updated_at`, no `batch_uuid`).
  - Table naming conventions (`user` and `message` instead of plural Laravel defaults).
  - Specific integer types, defaults, and 7 active foreign key constraints.
  - Legacy abandoned e-commerce tables (`address_*`, `bin`, `site`) that had broken foreign keys referencing non-existent tables (`store`, `currency`).

---

## Baseline Migration Details

- **Baseline Migration File:** `database/migrations/2026_09_29_000000_create_baseline_schema.php`
- **Old Migrations Archived:** Archived safely in `database/migrations_old/`.
- **Target Engine & Collation:** MariaDB 11.4 / MySQL 8.x using `utf8mb4` with `utf8mb4_unicode_ci` across all tables.
- **Included Tables (21 tables):**
  1. `permission_groups`
  2. `user` (`name`: VARCHAR(100) NOT NULL, `email`: VARCHAR(96) NULL with UNIQUE index `user_email_unique`)
  3. `trainees`
  4. `workout_plans`
  5. `exercises`
  6. `workout_logs`
  7. `nutrition_plans`
  8. `posts`
  9. `post_items`
  10. `join_us_submissions`
  11. `message`
  12. `settings` (no primary key, matching live `gym`)
  13. `permissions`
  14. `auth_session` (`id`: signed BIGINT auto-increment, `ip`: VARCHAR(45) NOT NULL, `access_token`: VARCHAR(100))
  15. `activity_log`
  16. `activate_section` (`id`: INT NOT NULL without primary key, `value`: ENUM('active','inactive'))
  17. `language`
  18. `social_links`
  19. `password_resets`
  20. `failed_jobs`
  21. `personal_access_tokens`

- **Active Foreign Keys Configured (7 total):**
  - `trainees.user_id` -> `user.id` (`fk_trainees_user`, `ON DELETE RESTRICT`)
  - `workout_plans.trainee_id` -> `trainees.id` (`fk_workout_plans_trainee`, `ON DELETE CASCADE`)
  - `workout_logs.trainee_id` -> `trainees.id` (`fk_workout_logs_trainee`, `ON DELETE CASCADE`)
  - `exercises.workout_plan_id` -> `workout_plans.id` (`fk_exercise_plan`, `ON DELETE CASCADE`)
  - `workout_logs.exercise_id` -> `exercises.id` (`fk_log_exercise`, `ON DELETE CASCADE`)
  - `nutrition_plans.trainee_id` -> `trainees.id` (`fk_nutrition_trainee_id`, `ON DELETE CASCADE`)
  - `post_items.post_id` -> `posts.id` (`fk_post_items_post_id`, `ON DELETE CASCADE`)

- **Excluded Unused Tables:**
  - `address_*` (8 mapping tables: `address_area_mapping`, `address_area_translation`, `address_city_mapping`, `address_city_translation`, `address_country_mapping`, `address_country_translation`, `address_region_mapping`, `address_zone_mapping`, etc.): Unused legacy tables.
  - `bin`: Unused legacy table.
  - `site`: Unused legacy table.
  - `users` (plural): Orphaned default Laravel table. The application model uses `user` (singular).

---

## Testing Against an Isolated Database (`gym_baseline_test`)

Testing is performed using the standard `mysql` connection with the `DB_DATABASE` environment variable overridden directly on the CLI, without needing a separate config entry:

```cmd
:: Windows CMD
set DB_DATABASE=gym_baseline_test&& php artisan migrate --force
set DB_DATABASE=gym_baseline_test&& set SEED_ADMIN_EMAIL=admin_test@gym.local&& set SEED_ADMIN_PASSWORD=TestSecret#2026!&& php artisan db:seed --force
```

```powershell
# Windows PowerShell
$env:DB_DATABASE="gym_baseline_test"; php artisan migrate --force
$env:SEED_ADMIN_EMAIL="admin_test@gym.local"; $env:SEED_ADMIN_PASSWORD="TestSecret#2026!"; php artisan db:seed --force
```

---

## Marking Baseline Migration as Already Run on Existing Database (`gym`)

> **WARNING:** 
> Do **NOT** run `php artisan migrate` on the existing/live database (`gym`) before recording the baseline migration.
> Because all 21 tables already exist in `gym`, running the migration directly would fail on table creation.

To mark the baseline migration as already executed on the existing database without modifying existing data or tables, execute the following SQL statement in your database management tool (phpMyAdmin, HeidiSQL, DBeaver, or MariaDB CLI):

```sql
INSERT INTO `migrations` (`migration`, `batch`)
VALUES ('2026_09_29_000000_create_baseline_schema', 1);
```

### Checking Migration Status
After executing the `INSERT` query above on the live database, verify the status by running:
```bash
php artisan migrate:status
```
`2026_09_29_000000_create_baseline_schema` will show `Ran? [Yes]`. Any new migrations created in the future will now run smoothly without conflicts.

---

## Seeders Overview

The application includes clean baseline seeders that insert no personal trainee data:
- `PermissionSeeder`: Creates standard roles (Super Admin [ID 119], Captain/Coach [ID 126], Trainee [ID 125]) and registers all dashboard route permissions from `routes/system.php`.
- `SettingSeeder`: Creates basic system configuration entries (site title, contact email, social links, logos).
- `LanguageSeeder`: Creates default supported languages (`en-gb` English and `ar` Arabic).
- `AdminUserSeeder`: Creates an initial admin user safely. `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` are strictly required in the environment (an exception is thrown if either is missing).

---

## Notes on Hardcoded Identifiers

- **`permission_group_id` = 125:**
  The Trainee group ID `125` is currently hardcoded in two locations:
  1. `app/Services/TraineeService.php` (line 177): Automatically assigns group `125` when creating new trainees.
  2. `app/Repositories/PermissionGroup/PermissionGroupRepository.php` (line 26): Excludes group `125` from general administrative permission group listings.
  
  The `PermissionSeeder` explicitly preserves this group ID (`id = 125`) for seamless compatibility with `TraineeService`.
