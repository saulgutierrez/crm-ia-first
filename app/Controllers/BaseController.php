<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Session;
use App\Helpers\Csrf;

abstract class BaseController
{
    /**
     * Render a full HTML page with layout.
     */
    protected function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $data['session'] = [
            'user_id' => Session::userId(),
            'user_name' => Session::get('user_name'),
            'user_role' => Session::userRole(),
        ];
        $data['csrf_field'] = Csrf::field();

        Response::view($view, $data, $layout);
    }

    /**
     * Return a JSON success response.
     */
    protected function json(mixed $data = null, int $statusCode = 200, string $message = ''): void
    {
        Response::success($data, $statusCode, $message);
    }

    /**
     * Return a JSON error response.
     */
    protected function error(string $message, int $statusCode = 400, mixed $errors = null): void
    {
        Response::error($message, $statusCode, $errors);
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url, int $statusCode = 302): never
    {
        Response::redirect($url, $statusCode);
    }

    /**
     * Redirect back to the previous page.
     */
    protected function redirectBack(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        Response::redirect($referer);
    }

    /**
     * Get JSON body from request (for API/JS requests).
     */
    protected function getJsonBody(): array
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Validate CSRF token from request.
     */
    protected function validateCsrf(): void
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Csrf::validate($token)) {
            $this->error('Token CSRF inválido.', 403);
        }
    }
}
