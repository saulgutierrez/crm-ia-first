<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ClientRepository;
use App\Helpers\Pagination;
use App\Helpers\Session;
use App\Exceptions\HttpException;

class ClientService extends BaseService
{
    private ClientRepository $clientRepo;

    public function __construct()
    {
        $this->clientRepo = new ClientRepository();
    }

    /**
     * Get the owner filter for the current user.
     * Agents can only see their own clients; admins see all.
     */
    private function getOwnerFilter(): ?int
    {
        return Session::isRole('admin') ? null : Session::userId();
    }

    /**
     * List clients with pagination and optional search.
     */
    public function list(int $page = 1, int $perPage = 15, string $search = ''): array
    {
        $ownerId = $this->getOwnerFilter();

        $total = $search
            ? $this->clientRepo->countSearch($search, $ownerId)
            : $this->clientRepo->countActive($ownerId);

        $items = $search
            ? $this->clientRepo->search($search, $page, $perPage, $ownerId)
            : $this->clientRepo->findAllActive($page, $perPage, $ownerId);

        return [
            'items' => $items,
            'pagination' => Pagination::metadata($total, $page, $perPage),
        ];
    }

    /**
     * Find a client by ID.
     */
    public function find(int $id): ?\stdClass
    {
        return $this->clientRepo->findWithOwner($id);
    }

    /**
     * Create a new client.
     */
    public function create(array $data): int
    {
        $this->requireField($data, 'company_name', 'nombre de la empresa');

        // Check uniqueness
        if ($this->clientRepo->exists('company_name', $data['company_name'])) {
            throw HttpException::conflict('Ya existe un cliente con ese nombre.');
        }

        return (int) $this->clientRepo->create([
            'company_name' => $data['company_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'industry' => $data['industry'] ?? null,
            'owner_id' => \App\Helpers\Session::userId(),
        ]);
    }

    /**
     * Update a client.
     */
    public function update(int $id, array $data): bool
    {
        $this->requireField($data, 'company_name', 'nombre de la empresa');

        if ($this->clientRepo->exists('company_name', $data['company_name'], $id)) {
            throw HttpException::conflict('Ya existe otro cliente con ese nombre.');
        }

        return $this->clientRepo->update($id, [
            'company_name' => $data['company_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'industry' => $data['industry'] ?? null,
        ]);
    }

    /**
     * Soft-delete a client.
     */
    public function delete(int $id): bool
    {
        return $this->clientRepo->softDelete($id);
    }
}
