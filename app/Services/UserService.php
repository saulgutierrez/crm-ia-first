<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Helpers\Pagination;
use App\Exceptions\HttpException;

class UserService extends BaseService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * List all active users with pagination.
     */
    public function list(int $page = 1, int $perPage = 15): array
    {
        $total = $this->userRepo->count('is_active = 1');

        $items = $this->userRepo->findAll($page, $perPage, 'name', 'ASC');

        return [
            'items' => $items,
            'pagination' => Pagination::metadata($total, $page, $perPage),
        ];
    }

    /**
     * Find a user by ID.
     */
    public function find(int $id): ?\stdClass
    {
        return $this->userRepo->find($id);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): int
    {
        $this->requireField($data, 'name', 'nombre');
        $this->requireField($data, 'email', 'email');
        $this->requireField($data, 'password', 'contraseña');

        // Validate email uniqueness
        if ($this->userRepo->exists('email', $data['email'])) {
            throw HttpException::conflict('Ya existe un usuario con ese email.');
        }

        // Validate role
        $role = $data['role'] ?? 'agent';
        if (!in_array($role, ['admin', 'agent'], true)) {
            $role = 'agent';
        }

        return (int) $this->userRepo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => AuthService::hashPassword($data['password']),
            'role' => $role,
            'is_active' => 1,
        ]);
    }

    /**
     * Update an existing user.
     */
    public function update(int $id, array $data): bool
    {
        $this->requireField($data, 'name', 'nombre');
        $this->requireField($data, 'email', 'email');

        // Check email uniqueness (excluding current user)
        if ($this->userRepo->exists('email', $data['email'], $id)) {
            throw HttpException::conflict('Ya existe otro usuario con ese email.');
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Only update role if provided and valid
        if (!empty($data['role']) && in_array($data['role'], ['admin', 'agent'], true)) {
            $updateData['role'] = $data['role'];
        }

        // Only update password if provided
        if (!empty($data['password'])) {
            $updateData['password_hash'] = AuthService::hashPassword($data['password']);
        }

        return $this->userRepo->update($id, $updateData);
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(int $id): bool
    {
        $user = $this->userRepo->find($id);
        if (!$user) {
            throw HttpException::notFound('Usuario no encontrado.');
        }

        // Prevent deactivating yourself
        if (\App\Helpers\Session::userId() === $id) {
            throw HttpException::validationError('No puedes desactivar tu propio usuario.');
        }

        $newStatus = $user->is_active ? 0 : 1;
        return $this->userRepo->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Get all agents for assignment dropdowns.
     */
    public function getAllAgents(): array
    {
        return $this->userRepo->findAllAgents();
    }

    /**
     * Get all active users for dropdowns.
     */
    public function getAllActive(): array
    {
        return $this->userRepo->findAllActive();
    }
}
