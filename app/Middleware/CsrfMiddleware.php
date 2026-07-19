<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Csrf;
use App\Helpers\Session;

class CsrfMiddleware
{
    /**
     * Verify CSRF token for state-changing requests.
     */
    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Only check for mutable methods
        if (!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return;
        }

        $token = $_POST['_csrf_token']
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_SERVER['HTTP_X_XSRF_TOKEN']
            ?? '';

        // For JSON API requests, try to get from body
        if (empty($token) && str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            $body = json_decode(file_get_contents('php://input'), true);
            $token = $body['_csrf_token'] ?? '';
        }

        if (!Csrf::validate($token)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error' => [
                    'code' => 403,
                    'message' => 'Token CSRF inválido. Por favor recargue la página e intente de nuevo.',
                ],
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
