<?php

declare(strict_types=1);

// -------------------------------------------------------
// 1. Autoload
// -------------------------------------------------------
require_once __DIR__ . '/../vendor/autoload.php';

// -------------------------------------------------------
// 2. Environment
// -------------------------------------------------------
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$appConfig = require __DIR__ . '/../config/app.php';

// -------------------------------------------------------
// 3. Error Handling
// -------------------------------------------------------
if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

set_exception_handler(function (\Throwable $e) use ($appConfig) {
    $statusCode = 500;
    $message = $appConfig['debug'] ? $e->getMessage() : 'Internal Server Error';

    if ($e instanceof \App\Exceptions\HttpException) {
        $statusCode = $e->getStatusCode();
        $message = $e->getMessage();
    }

    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        'success' => false,
        'error' => [
            'code' => $statusCode,
            'message' => $message,
        ],
    ], JSON_UNESCAPED_UNICODE);
});

// -------------------------------------------------------
// 4. Session
// -------------------------------------------------------
$sessionConfig = $appConfig['session'];
session_name($sessionConfig['name']);
session_set_cookie_params([
    'lifetime' => $sessionConfig['lifetime'] * 60,
    'path' => '/',
    'domain' => '',
    'secure' => $sessionConfig['secure'],
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// -------------------------------------------------------
// 5. Database Singleton
// -------------------------------------------------------
\App\Repositories\Database::connect(require __DIR__ . '/../config/database.php');

// -------------------------------------------------------
// 6. Routing
// -------------------------------------------------------
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    require __DIR__ . '/../routes/web.php';
    require __DIR__ . '/../routes/api.php';
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove trailing slash (except root)
$uri = ($uri !== '/') ? rtrim($uri, '/') : $uri;

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 404,
                'message' => 'Not Found',
            ],
        ], JSON_UNESCAPED_UNICODE);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => 405,
                'message' => 'Method Not Allowed',
            ],
        ], JSON_UNESCAPED_UNICODE);
        break;

    case FastRoute\Dispatcher::FOUND:
        [$class, $method, $middleware] = $routeInfo[1];
        $vars = $routeInfo[2];

        // Run middleware pipeline
        if (!empty($middleware)) {
            foreach ((array) $middleware as $mwClass) {
                $mw = new $mwClass();
                $mw->handle();
            }
        }

        $controller = new $class();
        $controller->$method($vars);
        break;
}
