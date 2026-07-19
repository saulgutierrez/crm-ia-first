-- Migration: 002_create_clients_table
-- Description: Creates the clients table for company/client management.

CREATE TABLE IF NOT EXISTS `clients` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(120) NULL,
    `phone` VARCHAR(30) NULL,
    `industry` VARCHAR(80) NULL,
    `owner_id` BIGINT UNSIGNED NULL,
    `deleted_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_clients_company` (`company_name`),
    INDEX `idx_clients_industry` (`industry`),
    INDEX `idx_clients_deleted` (`deleted_at`),
    CONSTRAINT `fk_clients_owner` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
