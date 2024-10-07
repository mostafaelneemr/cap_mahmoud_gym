CREATE TABLE IF NOT EXISTS `auth_session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `guard_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) NOT NULL,
  `ip` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `access_token_2` (`access_token`),
  KEY `guard_name` (`guard_name`),
  KEY `access_token` (`access_token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




----------------------- make columns nullable ------------
ALTER TABLE `user` CHANGE `mobile` `mobile` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `customer` CHANGE `lastname` `lastname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `customer` CHANGE `fax` `fax` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `ip` `ip` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `token` `token` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `code` `code` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `customer` CHANGE `custom_field` `custom_field` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `address` CHANGE `lastname` `lastname` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `company` `company` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `address_2` `address_2` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `city` `city` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `postcode` `postcode` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `latitude` `latitude` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `longitude` `longitude` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `address` CHANGE `custom_field` `custom_field` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `address` CHANGE `confirmed` `confirmed` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `customer_group` CHANGE `sort_order` `sort_order` INT(3) NULL;


-------------------------------------- activity_log -------------------------------

ALTER TABLE `activity_log` ADD `event` VARCHAR(255) NULL AFTER `properties`;
ALTER TABLE `activity_log` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `activity_log` DROP `method`;
ALTER TABLE `activity_log` DROP `updated_at`;


------------------------------------------permission_groups----------------------
ALTER TABLE `permission_groups` ADD `system` VARCHAR(50) NOT NULL DEFAULT 'po' AFTER `default_route`;

----------------- new 04/06/2023----------------------------

ALTER TABLE `customer_group_description` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

------------------------------------------user----------------------
ALTER TABLE `user` ADD `two_fa_secret` VARCHAR(255) NULL AFTER `inventory`;
------------------------------------------user 07/06/2023----------------------
ALTER TABLE `user` ADD `new_admin_permission_group_id` INT NULL AFTER `permission_group_id`;
ALTER TABLE `user` ADD `new_admin_permission_group_id` INT NULL AFTER `permission_group_id`;





