<?php

declare(strict_types=1);

use FastRoute\RouteCollector;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ClientController;
use App\Controllers\ContactController;
use App\Controllers\LeadController;
use App\Controllers\InteractionController;
use App\Controllers\TicketController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Controllers\UserController;

/*
 * Web Routes
 * ----------
 * Route format: ['GET|POST|PUT|DELETE', '/path', [Controller::class, 'method', ['Middleware1', 'Middleware2']]]
 */

$r->addRoute('GET', '/login', [AuthController::class, 'showLoginForm']);
$r->addRoute('POST', '/login', [AuthController::class, 'login']);
$r->addRoute('GET', '/logout', [AuthController::class, 'logout']);

// Admin-only: User management
$r->addRoute('GET', '/users', [UserController::class, 'index', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('GET', '/users/create', [UserController::class, 'create', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('POST', '/users', [UserController::class, 'store', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('GET', '/users/{id:\d+}/edit', [UserController::class, 'edit', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('POST', '/users/{id:\d+}', [UserController::class, 'update', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('POST', '/users/{id:\d+}/toggle-status', [UserController::class, 'toggleStatus', [AuthMiddleware::class, AdminMiddleware::class]]);

// Export routes (admin-only)
$r->addRoute('GET', '/export/clients', [App\Controllers\ExportController::class, 'clientsPdf', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('GET', '/export/leads', [App\Controllers\ExportController::class, 'leadsPdf', [AuthMiddleware::class, AdminMiddleware::class]]);
$r->addRoute('GET', '/export/dashboard', [App\Controllers\ExportController::class, 'dashboardPdf', [AuthMiddleware::class, AdminMiddleware::class]]);

// Protected routes
$r->addRoute('GET', '/', [DashboardController::class, 'index', [AuthMiddleware::class]]);

// Clients
$r->addRoute('GET', '/clients', [ClientController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('GET', '/clients/create', [ClientController::class, 'create', [AuthMiddleware::class]]);
$r->addRoute('POST', '/clients', [ClientController::class, 'store', [AuthMiddleware::class]]);
$r->addRoute('GET', '/clients/{id:\d+}', [ClientController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('GET', '/clients/{id:\d+}/edit', [ClientController::class, 'edit', [AuthMiddleware::class]]);
$r->addRoute('POST', '/clients/{id:\d+}', [ClientController::class, 'update', [AuthMiddleware::class]]);
$r->addRoute('POST', '/clients/{id:\d+}/delete', [ClientController::class, 'destroy', [AuthMiddleware::class]]);

// Contacts
$r->addRoute('GET', '/clients/{clientId:\d+}/contacts', [ContactController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('GET', '/clients/{clientId:\d+}/contacts/create', [ContactController::class, 'create', [AuthMiddleware::class]]);
$r->addRoute('POST', '/clients/{clientId:\d+}/contacts', [ContactController::class, 'store', [AuthMiddleware::class]]);
$r->addRoute('GET', '/contacts/{id:\d+}/edit', [ContactController::class, 'edit', [AuthMiddleware::class]]);
$r->addRoute('POST', '/contacts/{id:\d+}', [ContactController::class, 'update', [AuthMiddleware::class]]);
$r->addRoute('POST', '/contacts/{id:\d+}/delete', [ContactController::class, 'destroy', [AuthMiddleware::class]]);

// Leads
$r->addRoute('GET', '/leads', [LeadController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('GET', '/leads/create', [LeadController::class, 'create', [AuthMiddleware::class]]);
$r->addRoute('POST', '/leads', [LeadController::class, 'store', [AuthMiddleware::class]]);
$r->addRoute('GET', '/leads/{id:\d+}', [LeadController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('GET', '/leads/{id:\d+}/edit', [LeadController::class, 'edit', [AuthMiddleware::class]]);
$r->addRoute('POST', '/leads/{id:\d+}', [LeadController::class, 'update', [AuthMiddleware::class]]);
$r->addRoute('POST', '/leads/{id:\d+}/delete', [LeadController::class, 'destroy', [AuthMiddleware::class]]);

// Interactions
$r->addRoute('GET', '/interactions', [InteractionController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('GET', '/interactions/create', [InteractionController::class, 'create', [AuthMiddleware::class]]);
$r->addRoute('POST', '/interactions', [InteractionController::class, 'store', [AuthMiddleware::class]]);
$r->addRoute('GET', '/interactions/{id:\d+}', [InteractionController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('POST', '/interactions/{id:\d+}/delete', [InteractionController::class, 'destroy', [AuthMiddleware::class]]);

// Tickets
$r->addRoute('GET', '/tickets', [TicketController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('GET', '/tickets/create', [TicketController::class, 'create', [AuthMiddleware::class]]);
$r->addRoute('POST', '/tickets', [TicketController::class, 'store', [AuthMiddleware::class]]);
$r->addRoute('GET', '/tickets/{id:\d+}', [TicketController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('GET', '/tickets/{id:\d+}/edit', [TicketController::class, 'edit', [AuthMiddleware::class]]);
$r->addRoute('POST', '/tickets/{id:\d+}', [TicketController::class, 'update', [AuthMiddleware::class]]);
$r->addRoute('POST', '/tickets/{id:\d+}/delete', [TicketController::class, 'destroy', [AuthMiddleware::class]]);

