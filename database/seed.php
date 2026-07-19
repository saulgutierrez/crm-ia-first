<?php

declare(strict_types=1);

/**
 * Seed Runner
 * 
 * Usage: php database/seed.php
 * 
 * Inserts default data with properly generated Argon2id password hashes.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/../config/database.php';

echo "=== CRM Profesional - Seed Runner ===\n\n";

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "✓ Connected to database '{$config['database']}'\n\n";

    // Check if users already exist
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count > 0) {
        echo "⚠ Users already exist ({$count} found). Skipping seed.\n";
        echo "  To re-seed, truncate the users table first.\n";
        exit(0);
    }

    // Generate passwords with Argon2id
    echo "Generating Argon2id password hashes...\n";

    $adminHash = password_hash('admin123', PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3,
    ]);
    echo "  ✓ Admin password hash generated\n";

    $agentHash = password_hash('agent123', PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3,
    ]);
    echo "  ✓ Agent password hash generated\n";

    // Insert admin user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, is_active) 
        VALUES (:name, :email, :password_hash, :role, :is_active)
    ");

    $stmt->execute([
        'name' => 'Administrador',
        'email' => 'admin@crm.com',
        'password_hash' => $adminHash,
        'role' => 'admin',
        'is_active' => 1,
    ]);
    $adminId = $pdo->lastInsertId();
    echo "  ✓ Admin user created (ID: {$adminId})\n";

    $stmt->execute([
        'name' => 'Agente de Ventas',
        'email' => 'agent@crm.com',
        'password_hash' => $agentHash,
        'role' => 'agent',
        'is_active' => 1,
    ]);
    $agentId = $pdo->lastInsertId();
    echo "  ✓ Agent user created (ID: {$agentId})\n";

    // Insert sample client
    $stmt = $pdo->prepare("
        INSERT INTO clients (company_name, email, phone, industry, owner_id) 
        VALUES (:company_name, :email, :phone, :industry, :owner_id)
    ");

    $stmt->execute([
        'company_name' => 'Corporación TechSolutions',
        'email' => 'contacto@techsolutions.com',
        'phone' => '+52 55 1234 5678',
        'industry' => 'Tecnología',
        'owner_id' => $adminId,
    ]);
    echo "  ✓ Sample client created\n";

    $stmt->execute([
        'company_name' => 'Grupo Financiero Horizonte',
        'email' => 'info@horizonte.com',
        'phone' => '+52 81 9876 5432',
        'industry' => 'Finanzas',
        'owner_id' => $agentId,
    ]);
    echo "  ✓ Sample client created\n";

    // Insert sample leads
    $stmt = $pdo->prepare("
        INSERT INTO leads (client_id, assigned_to, title, status, estimated_value, source)
        VALUES (:client_id, :assigned_to, :title, :status, :estimated_value, :source)
    ");

    $stmt->execute([
        'client_id' => 1,
        'assigned_to' => $adminId,
        'title' => 'Implementación ERP',
        'status' => 'qualified',
        'estimated_value' => 150000.00,
        'source' => 'Web',
    ]);
    echo "  ✓ Sample lead created\n";

    $stmt->execute([
        'client_id' => 2,
        'assigned_to' => $agentId,
        'title' => 'Consultoría Financiera',
        'status' => 'proposal',
        'estimated_value' => 85000.00,
        'source' => 'Referido',
    ]);
    echo "  ✓ Sample lead created\n";

    // Insert sample interactions
    $stmt = $pdo->prepare("
        INSERT INTO interactions (client_id, type, subject, description, created_by)
        VALUES (:client_id, :type, :subject, :description, :created_by)
    ");

    $stmt->execute([
        'client_id' => 1,
        'type' => 'call',
        'subject' => 'Primer contacto - ERP',
        'description' => 'Llamada inicial para conocer requisitos del sistema ERP.',
        'created_by' => $adminId,
    ]);
    echo "  ✓ Sample interaction created\n";

    $stmt->execute([
        'client_id' => 2,
        'type' => 'meeting',
        'subject' => 'Reunión de propuesta',
        'description' => 'Presentación de propuesta de consultoría financiera.',
        'created_by' => $agentId,
    ]);
    echo "  ✓ Sample interaction created\n";

    // Insert sample ticket
    $stmt = $pdo->prepare("
        INSERT INTO tickets (client_id, assigned_to, subject, description, priority, status, created_by)
        VALUES (:client_id, :assigned_to, :subject, :description, :priority, :status, :created_by)
    ");

    $stmt->execute([
        'client_id' => 1,
        'assigned_to' => $agentId,
        'subject' => 'Problema con módulo de facturación',
        'description' => 'El módulo de facturación no genera PDFs correctamente.',
        'priority' => 'high',
        'status' => 'open',
        'created_by' => $adminId,
    ]);
    echo "  ✓ Sample ticket created\n";

    $stmt->execute([
        'client_id' => 2,
        'assigned_to' => null,
        'subject' => 'Solicitud de nueva funcionalidad',
        'description' => 'Solicitan integración con API de pagos.',
        'priority' => 'medium',
        'status' => 'open',
        'created_by' => $agentId,
    ]);
    echo "  ✓ Sample ticket created\n";

    echo "\n========================================\n";
    echo "  ✅ Seed completed successfully!\n";
    echo "========================================\n";
    echo "  Admin login: admin@crm.com / admin123\n";
    echo "  Agent login: agent@crm.com / agent123\n";
    echo "========================================\n";

} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
