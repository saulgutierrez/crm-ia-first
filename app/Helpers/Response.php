<?php

declare(strict_types=1);

namespace App\Helpers;

class Response
{
    /**
     * Send a successful JSON response.
     */
    public static function success(mixed $data = null, int $statusCode = 200, string $message = ''): void
    {
        self::send([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Send an error JSON response.
     */
    public static function error(string $message, int $statusCode = 400, mixed $errors = null): void
    {
        $payload = [
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $message,
            ],
        ];

        if ($errors !== null) {
            $payload['error']['details'] = $errors;
        }

        self::send($payload, $statusCode);
    }

    /**
     * Send a paginated JSON response.
     */
    public static function paginated(array $items, int $total, int $page, int $perPage): void
    {
        self::send([
            'success' => true,
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int) ceil($total / max($perPage, 1)),
            ],
        ]);
    }

    /**
     * Send a redirect response (for web routes).
     */
    public static function redirect(string $url, int $statusCode = 302): never
    {
        header("Location: $url", true, $statusCode);
        exit;
    }

    /**
     * Render an HTML view (for web routes).
     */
    public static function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data);

        ob_start();
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = __DIR__ . "/../Views/{$layout}.php";
        if ($layout && file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Send raw JSON and terminate.
     */
    private static function send(array $payload, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
