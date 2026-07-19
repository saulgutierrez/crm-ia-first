-- Migration: 005_create_interactions_table
-- Description: Creates the interactions table for client communication logging.

CREATE TABLE IF NOT EXISTS `interactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('call', 'email', 'meeting', 'note') NOT NULL DEFAULT 'note',
    `subject` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_interactions_client` (`client_id`),
    INDEX `idx_interactions_type` (`type`),
    INDEX `idx_interactions_created` (`created_by`),
    CONSTRAINT `fk_interactions_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_interactions_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
