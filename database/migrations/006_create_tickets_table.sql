-- Migration: 006_create_tickets_table
-- Description: Creates the support tickets table.

CREATE TABLE IF NOT EXISTS `tickets` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `assigned_to` BIGINT UNSIGNED NULL,
    `subject` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `status` ENUM('open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'open',
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_tickets_client` (`client_id`),
    INDEX `idx_tickets_assigned` (`assigned_to`),
    INDEX `idx_tickets_priority` (`priority`),
    INDEX `idx_tickets_status` (`status`),
    CONSTRAINT `fk_tickets_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tickets_assignee` FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tickets_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
