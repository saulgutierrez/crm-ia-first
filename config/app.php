<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'CRM Profesional',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',

    'session' => [
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 120),
        'name' => $_ENV['SESSION_NAME'] ?? 'crm_session',
        'secure' => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
    ],

    'csrf' => [
        'token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? 'crm_csrf_token',
    ],

    'pagination' => [
        'per_page' => 15,
    ],
];
