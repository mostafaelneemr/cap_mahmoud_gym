-- ==============================================================================
-- CENTRAL EXERCISE LIBRARY — SAFE PHASE 1 MIGRATION SCRIPT (MANUAL REVIEW ONLY)
-- ==============================================================================
-- IMPORTANT NOTICE:
-- DO NOT EXECUTE THIS SCRIPT BLINDLY ON PRODUCTION.
-- This script has been generated for manual review and pre-deployment inspection.
-- Production environment was NOT accessed or modified during this task.
-- Before applying to production, verify column types, storage engine, charset,
-- collation, and existing indexes against your live production database.
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- PREFLIGHT AUDIT CHECKS (Execute before applying schema changes)
-- ------------------------------------------------------------------------------

-- 1. Verify existence and collation of the `exercises` table
SELECT TABLE_NAME, ENGINE, TABLE_COLLATION 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'exercises';

-- 2. Verify column type of exercises.id (Must be BIGINT UNSIGNED)
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'exercises' AND COLUMN_NAME = 'id';

-- 3. Verify that `exercise_libraries` does NOT already exist
SELECT COUNT(*) AS library_table_exists 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'exercise_libraries';

-- 4. Audit production exercises data for conflicts before any backfill
-- (Identifies names with multiple conflicting video URLs)
SELECT 
    name, 
    COUNT(*) AS total_rows, 
    COUNT(DISTINCT NULLIF(TRIM(link), '')) AS distinct_urls_count 
FROM exercises 
GROUP BY name 
HAVING distinct_urls_count > 1;

-- ==============================================================================
-- STEP 1: CREATE `exercise_libraries` TABLE
-- ==============================================================================

CREATE TABLE IF NOT EXISTS `exercise_libraries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `video_url` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_exercise_libraries_name` (`name`),
    KEY `idx_exercise_libraries_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- STEP 2: ADD NULLABLE `exercise_library_id` TO `exercises` TABLE
-- ==============================================================================

-- Safe conditional addition:
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'exercises' 
      AND COLUMN_NAME = 'exercise_library_id'
);

SET @sql_add_col = IF(
    @col_exists = 0,
    'ALTER TABLE `exercises` ADD COLUMN `exercise_library_id` BIGINT UNSIGNED NULL AFTER `workout_plan_id`',
    'SELECT "Column exercise_library_id already exists" AS notice'
);
PREPARE stmt_col FROM @sql_add_col;
EXECUTE stmt_col;
DEALLOCATE PREPARE stmt_col;

-- Add index if not already present
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'exercises' 
      AND INDEX_NAME = 'idx_exercise_library_id'
);

SET @sql_add_idx = IF(
    @idx_exists = 0,
    'ALTER TABLE `exercises` ADD KEY `idx_exercise_library_id` (`exercise_library_id`)',
    'SELECT "Index idx_exercise_library_id already exists" AS notice'
);
PREPARE stmt_idx FROM @sql_add_idx;
EXECUTE stmt_idx;
DEALLOCATE PREPARE stmt_idx;

-- Add foreign key constraint with ON DELETE SET NULL to preserve exercises if library item is removed
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'exercises' 
      AND CONSTRAINT_NAME = 'fk_exercise_library'
);

SET @sql_add_fk = IF(
    @fk_exists = 0,
    'ALTER TABLE `exercises` ADD CONSTRAINT `fk_exercise_library` FOREIGN KEY (`exercise_library_id`) REFERENCES `exercise_libraries` (`id`) ON DELETE SET NULL',
    'SELECT "Foreign key fk_exercise_library already exists" AS notice'
);
PREPARE stmt_fk FROM @sql_add_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;

-- ==============================================================================
-- STEP 3: OPTIONAL SAFE DATA BACKFILL (UNAMBIGUOUS EXERCISES ONLY)
-- ==============================================================================
-- NOTE:
-- - This step ONLY maps exercises where all rows sharing the name have EXACTLY ONE distinct non-empty URL.
-- - Conflicting URLs (>1 distinct URL) and exercises with NO URL are strictly excluded.
-- - Existing `exercises.link` values are NEVER erased or overwritten.
-- ------------------------------------------------------------------------------

-- A. Insert unambiguous library records
INSERT INTO `exercise_libraries` (`name`, `video_url`, `description`, `status`, `created_at`, `updated_at`)
SELECT 
    sub.name, 
    sub.video_url, 
    'Migrated from legacy workout exercise records' AS description, 
    'active' AS status,
    NOW() AS created_at,
    NOW() AS updated_at
FROM (
    SELECT 
        TRIM(name) AS name, 
        MAX(TRIM(link)) AS video_url
    FROM `exercises`
    WHERE `link` IS NOT NULL AND TRIM(`link`) != ''
    GROUP BY TRIM(name)
    HAVING COUNT(DISTINCT TRIM(link)) = 1
) sub
LEFT JOIN `exercise_libraries` el ON el.name = sub.name
WHERE el.id IS NULL;

-- B. Link unambiguous exercise records
UPDATE `exercises` e
JOIN `exercise_libraries` el ON TRIM(e.name) = el.name
SET e.exercise_library_id = el.id
WHERE e.exercise_library_id IS NULL
  AND e.link IS NOT NULL 
  AND TRIM(e.link) != ''
  AND TRIM(e.name) IN (
      SELECT name_to_link FROM (
          SELECT TRIM(name) AS name_to_link
          FROM `exercises`
          WHERE `link` IS NOT NULL AND TRIM(`link`) != ''
          GROUP BY TRIM(name)
          HAVING COUNT(DISTINCT TRIM(link)) = 1
      ) t
  );

-- ==============================================================================
-- POST-MIGRATION VERIFICATION
-- ==============================================================================

SELECT COUNT(*) AS total_library_entries FROM `exercise_libraries`;
SELECT COUNT(*) AS linked_exercises_count FROM `exercises` WHERE `exercise_library_id` IS NOT NULL;
SELECT COUNT(*) AS unlinked_exercises_count FROM `exercises` WHERE `exercise_library_id` IS NULL;

-- ==============================================================================
-- REVERSIBLE ROLLBACK PROCEDURE (If rollback is needed)
-- ==============================================================================
-- To safely reverse this migration without losing original workout history:
--
-- 1. Unlink exercises:
--    UPDATE `exercises` SET `exercise_library_id` = NULL WHERE `exercise_library_id` IS NOT NULL;
--
-- 2. Drop foreign key and column:
--    ALTER TABLE `exercises` DROP FOREIGN KEY `fk_exercise_library`;
--    ALTER TABLE `exercises` DROP KEY `idx_exercise_library_id`;
--    ALTER TABLE `exercises` DROP COLUMN `exercise_library_id`;
--
-- 3. Drop `exercise_libraries` table:
--    DROP TABLE IF EXISTS `exercise_libraries`;
--
-- Original exercises and their legacy link values remain 100% intact.
-- ==============================================================================
