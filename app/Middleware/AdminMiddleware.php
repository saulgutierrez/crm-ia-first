<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Session;

class AdminMiddleware
{
    /**
     * Verify the authenticated user has admin role.
     */
    public function handle(): void
    {
        if (Session::userRole() !== 'admin') {
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 403,
                        'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                    ],
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            http_response_code(403);
            echo '<h1>403 - Acceso Denegado</h1><p>Se requieren permisos de administrador.</p>';
            exit;
        }
    }
}
