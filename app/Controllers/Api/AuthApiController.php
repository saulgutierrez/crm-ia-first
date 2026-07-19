<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Exceptions\HttpException;

class AuthApiController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(): void
    {
        $body = $this->getJsonBody();

        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->error('Email y contraseña son requeridos.', 422);
        }

        try {
            $user = $this->authService->login($email, $password);
            $this->json([
                'user' => [
                    'id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ], 200, 'Inicio de sesión exitoso.');
        } catch (HttpException $e) {
            $this->error($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->json(null, 200, 'Sesión cerrada.');
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(): void
    {
        $user = $this->authService->currentUser();

        if (!$user) {
            $this->error('No autenticado.', 401);
        }

        $this->json([
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
}
