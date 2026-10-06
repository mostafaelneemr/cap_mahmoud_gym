USE gym;

-- 1) IPv6 ممكن يوصل 45 حرف
ALTER TABLE auth_session MODIFY ip VARCHAR(45) NOT NULL;

-- 2) منع تكرار الإيميل في user
ALTER TABLE `user` ADD UNIQUE KEY user_email_unique (email);

-- 3) توسيع الاسم من 32 لـ 100
ALTER TABLE `user` MODIFY name VARCHAR(100) NOT NULL;

-- 4) ربط الجداول (Foreign Keys)
ALTER TABLE trainees
    ADD CONSTRAINT fk_trainees_user
        FOREIGN KEY (user_id) REFERENCES `user`(id) ON DELETE RESTRICT;

ALTER TABLE workout_plans
    ADD CONSTRAINT fk_workout_plans_trainee
        FOREIGN KEY (trainee_id) REFERENCES trainees(id) ON DELETE CASCADE;

ALTER TABLE workout_logs
    ADD CONSTRAINT fk_workout_logs_trainee
        FOREIGN KEY (trainee_id) REFERENCES trainees(id) ON DELETE CASCADE;

SHOW COLUMNS FROM auth_session LIKE 'ip';
SHOW COLUMNS FROM `user` LIKE 'name';
SHOW INDEX FROM `user` WHERE Key_name = 'user_email_unique';

SELECT table_name, constraint_name, referenced_table_name, delete_rule
FROM information_schema.referential_constraints
WHERE constraint_schema = DATABASE()
  AND table_name IN ('trainees','workout_plans','workout_logs','exercises','nutrition_plans','post_items');


SELECT table_name, table_collation
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'
ORDER BY table_collation, table_name;

SELECT CONCAT('ALTER TABLE `', table_name, '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;') AS cmd
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_type = 'BASE TABLE'
  AND table_name NOT LIKE 'address\_%'
  AND table_name NOT IN ('bin', 'site', 'users')
  AND table_collation <> 'utf8mb4_unicode_ci';

SELECT table_name, table_collation
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'
  AND table_collation <> 'utf8mb4_unicode_ci'
ORDER BY table_name;


ALTER TABLE `activate_section` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `exercises` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `join_us_submissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `language` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `message` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `trainees` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `workout_logs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `workout_plans` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SELECT table_name, table_collation
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_type = 'BASE TABLE'
  AND table_collation <> 'utf8mb4_unicode_ci'
ORDER BY table_name;


SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS address_city_mapping, address_country_mapping, address_region_mapping, address_zone_mapping,
    address_city, address_country, address_region, address_zone, bin, site, users;
SET FOREIGN_KEY_CHECKS = 1;

    -- ALTER TABLE `workout_plans`
-- ADD COLUMN `day_name_ar` VARCHAR(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `day_name`,
-- ADD COLUMN `warmup_ar` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `warmup`,
-- ADD COLUMN `post_workout_ar` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `post_workout`;


-- ALTER TABLE `exercises`
-- ADD COLUMN `name_ar` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `name`,
-- ADD COLUMN `notes_en` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `tempo`;
-- ADD COLUMN `notes_ar` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `tempo`;
