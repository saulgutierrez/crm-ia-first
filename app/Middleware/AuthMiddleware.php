<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Session;
use App\Exceptions\HttpException;

class AuthMiddleware
{
    /**
     * Verify the user is authenticated.
     */
    public function handle(): void
    {
        if (!Session::isAuthenticated()) {
            // Check if it's an API request
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error' => [
                        'code' => 401,
                        'message' => 'No autenticado. Por favor inicie sesión.',
                    ],
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Web request - redirect to login
            Session::flash('error', 'Por favor inicie sesión para continuar.');
            header('Location: /login');
            exit;
        }
    }
}
