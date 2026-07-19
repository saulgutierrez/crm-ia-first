<?php

declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Controllers\Api\AuthApiController;
use App\Controllers\Api\ClientApiController;
use App\Controllers\Api\LeadApiController;
use App\Controllers\Api\TicketApiController;
use App\Controllers\Api\StatsApiController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;

/*
 * API Routes
 * ----------
 * All under /api/v1 prefix. Route format:
 * ['GET|POST|PUT|DELETE', '/api/v1/...', [Controller::class, 'method', ['Middleware']]]
 */

// Auth
$r->addRoute('POST', '/api/v1/auth/login', [AuthApiController::class, 'login']);
$r->addRoute('POST', '/api/v1/auth/logout', [AuthApiController::class, 'logout']);
$r->addRoute('GET', '/api/v1/auth/me', [AuthApiController::class, 'me', [AuthMiddleware::class]]);

// Clients
$r->addRoute('GET', '/api/v1/clients', [ClientApiController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('POST', '/api/v1/clients', [ClientApiController::class, 'store', [AuthMiddleware::class, CsrfMiddleware::class]]);
$r->addRoute('GET', '/api/v1/clients/{id:\d+}', [ClientApiController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('PUT', '/api/v1/clients/{id:\d+}', [ClientApiController::class, 'update', [AuthMiddleware::class, CsrfMiddleware::class]]);
$r->addRoute('DELETE', '/api/v1/clients/{id:\d+}', [ClientApiController::class, 'destroy', [AuthMiddleware::class, CsrfMiddleware::class]]);

// Leads
$r->addRoute('GET', '/api/v1/leads', [LeadApiController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('POST', '/api/v1/leads', [LeadApiController::class, 'store', [AuthMiddleware::class, CsrfMiddleware::class]]);
$r->addRoute('GET', '/api/v1/leads/{id:\d+}', [LeadApiController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('PUT', '/api/v1/leads/{id:\d+}', [LeadApiController::class, 'update', [AuthMiddleware::class, CsrfMiddleware::class]]);
$r->addRoute('DELETE', '/api/v1/leads/{id:\d+}', [LeadApiController::class, 'destroy', [AuthMiddleware::class, CsrfMiddleware::class]]);

// Tickets
$r->addRoute('GET', '/api/v1/tickets', [TicketApiController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('POST', '/api/v1/tickets', [TicketApiController::class, 'store', [AuthMiddleware::class, CsrfMiddleware::class]]);
$r->addRoute('GET', '/api/v1/tickets/{id:\d+}', [TicketApiController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('PUT', '/api/v1/tickets/{id:\d+}', [TicketApiController::class, 'update', [AuthMiddleware::class, CsrfMiddleware::class]]);
$r->addRoute('DELETE', '/api/v1/tickets/{id:\d+}', [TicketApiController::class, 'destroy', [AuthMiddleware::class, CsrfMiddleware::class]]);

// Stats / Dashboard
$r->addRoute('GET', '/api/v1/stats/dashboard', [StatsApiController::class, 'dashboard', [AuthMiddleware::class]]);
