-- Migration: 004_create_leads_table
-- Description: Creates the leads/opportunities table for sales pipeline.

CREATE TABLE IF NOT EXISTS `leads` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `assigned_to` BIGINT UNSIGNED NULL,
    `title` VARCHAR(150) NOT NULL,
    `status` ENUM('new', 'contacted', 'qualified', 'proposal', 'won', 'lost') NOT NULL DEFAULT 'new',
    `estimated_value` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `source` VARCHAR(60) NULL,
    `expected_close_date` DATE NULL,
    `deleted_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_leads_client` (`client_id`),
    INDEX `idx_leads_assigned` (`assigned_to`),
    INDEX `idx_leads_status` (`status`),
    INDEX `idx_leads_deleted` (`deleted_at`),
    CONSTRAINT `fk_leads_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_leads_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
