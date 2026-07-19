-- Migration: 007_create_login_attempts_table
-- Description: Tracks failed login attempts for brute force protection.

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(120) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `success` TINYINT(1) NOT NULL DEFAULT 0,
    `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_login_attempts_ip` (`ip_address`),
    INDEX `idx_login_attempts_email` (`email`),
    INDEX `idx_login_attempts_time` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
