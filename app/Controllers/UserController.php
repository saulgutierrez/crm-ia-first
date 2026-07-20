<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;
use App\Helpers\Pagination;
use App\Helpers\Session;
use App\Exceptions\HttpException;

class UserController extends BaseController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * List all users with pagination.
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage(15);

        $result = $this->userService->list($page, $perPage);
        $agents = $this->userService->getAllAgents();

        $this->render('users/index', [
            'title' => 'Usuarios',
            'users' => $result['items'],
            'pagination' => $result['pagination'],
            'agentsCount' => count($agents),
        ]);
    }

    /**
     * Show create user form.
     */
    public function create(): void
    {
        $this->render('users/form', [
            'title' => 'Nuevo Usuario',
            'user' => null,
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(): void
    {
        $this->validateCsrf();

        try {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'agent',
            ];

            $this->userService->create($data);
            Session::flash('success', 'Usuario creado exitosamente.');
            $this->redirect('/users');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirectBack();
        }
    }

    /**
     * Show edit user form.
     */
    public function edit(array $vars): void
    {
        $user = $this->userService->find((int) $vars['id']);

        if (!$user) {
            Session::flash('error', 'Usuario no encontrado.');
            $this->redirect('/users');
        }

        $this->render('users/form', [
            'title' => 'Editar Usuario',
            'user' => $user,
        ]);
    }

    /**
     * Update a user.
     */
    public function update(array $vars): void
    {
        $this->validateCsrf();

        try {
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'agent',
            ];

            $this->userService->update((int) $vars['id'], $data);
            Session::flash('success', 'Usuario actualizado exitosamente.');
            $this->redirect('/users');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirectBack();
        }
    }

    /**
     * Toggle user active/inactive status.
     */
    public function toggleStatus(array $vars): void
    {
        $this->validateCsrf();

        try {
            $this->userService->toggleStatus((int) $vars['id']);
            Session::flash('success', 'Estado del usuario actualizado.');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
        }

        $this->redirect('/users');
    }
}
