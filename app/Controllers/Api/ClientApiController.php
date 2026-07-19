<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\ClientService;
use App\Exceptions\HttpException;
use App\Helpers\Pagination;
use App\Helpers\Response;

class ClientApiController extends BaseController
{
    private ClientService $clientService;

    public function __construct()
    {
        $this->clientService = new ClientService();
    }

    /**
     * GET /api/v1/clients
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $search = $_GET['search'] ?? '';

        $result = $this->clientService->list($page, $perPage, $search);
        Response::paginated($result['items'], $result['pagination']['total'], $page, $perPage);
    }

    /**
     * POST /api/v1/clients
     */
    public function store(): void
    {
        try {
            $data = $this->getJsonBody();
            $id = $this->clientService->create($data);
            $client = $this->clientService->find($id);
            $this->json($client, 201, 'Cliente creado exitosamente.');
        } catch (HttpException $e) {
            $this->error($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * GET /api/v1/clients/{id}
     */
    public function show(array $vars): void
    {
        $client = $this->clientService->find((int) $vars['id']);

        if (!$client) {
            $this->error('Cliente no encontrado.', 404);
        }

        $this->json($client);
    }

    /**
     * PUT /api/v1/clients/{id}
     */
    public function update(array $vars): void
    {
        try {
            $data = $this->getJsonBody();
            $this->clientService->update((int) $vars['id'], $data);
            $client = $this->clientService->find((int) $vars['id']);
            $this->json($client, 200, 'Cliente actualizado exitosamente.');
        } catch (HttpException $e) {
            $this->error($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * DELETE /api/v1/clients/{id}
     */
    public function destroy(array $vars): void
    {
        $this->clientService->delete((int) $vars['id']);
        $this->json(null, 200, 'Cliente eliminado exitosamente.');
    }
}
