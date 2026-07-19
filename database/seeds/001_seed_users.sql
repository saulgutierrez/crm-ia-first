-- Seed: 001_seed_users
-- Description: Creates default admin and agent users.
-- Default passwords are hashed with Argon2id.
-- Note: Run this only once after migration.

-- Admin user (password: admin123)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `is_active`) VALUES
('Administrador', 'admin@crm.com', '$argon2id$v=19$m=65536,t=4,p=3$c2FsdHlzYWx0c2FsdHk$DqQjRK8JZP6GVVn5qLQpRv2VXKGCmFGwFrfK4bLc6Xc', 'admin', 1);

-- Agent user (password: agent123)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `is_active`) VALUES
('Agente de Ventas', 'agent@crm.com', '$argon2id$v=19$m=65536,t=4,p=3$YW5vdGhlcnNhbHR5$RhGt3bL8nXPqJWVkLmN7pR8vYQXBHfDgK5jM2zW9bYc', 'agent', 1);
