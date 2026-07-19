-- Migration: 003_create_contacts_table
-- Description: Creates the contacts table linked to clients.

CREATE TABLE IF NOT EXISTS `contacts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `position` VARCHAR(80) NULL,
    `email` VARCHAR(120) NULL,
    `phone` VARCHAR(30) NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_contacts_client` (`client_id`),
    INDEX `idx_contacts_primary` (`is_primary`),
    CONSTRAINT `fk_contacts_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
