<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Helpers\Session;
use App\Exceptions\HttpException;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): void
    {
        if (Session::isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'title' => 'Iniciar Sesión',
        ], 'layouts/guest');
    }

    /**
     * Handle login form submission.
     */
    public function login(): void
    {
        $this->validateCsrf();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $this->authService->login($email, $password);
            $this->redirect('/');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/login');
        }
    }

    /**
     * Handle logout.
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/login');
    }
}
