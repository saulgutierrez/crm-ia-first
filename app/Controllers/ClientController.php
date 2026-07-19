<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ClientService;
use App\Helpers\Pagination;
use App\Exceptions\HttpException;

class ClientController extends BaseController
{
    private ClientService $clientService;

    public function __construct()
    {
        $this->clientService = new ClientService();
    }

    /**
     * List clients with pagination and search.
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage(15);
        $search = $_GET['search'] ?? '';

        $result = $this->clientService->list($page, $perPage, $search);

        $this->render('clients/index', [
            'title' => 'Clientes',
            'clients' => $result['items'],
            'pagination' => $result['pagination'],
            'search' => $search,
        ]);
    }

    /**
     * Show create client form.
     */
    public function create(): void
    {
        $this->render('clients/form', [
            'title' => 'Nuevo Cliente',
            'client' => null,
        ]);
    }

    /**
     * Store a new client.
     */
    public function store(): void
    {
        $this->validateCsrf();

        try {
            $data = [
                'company_name' => $_POST['company_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'industry' => $_POST['industry'] ?? '',
            ];

            $this->clientService->create($data);
            Session::flash('success', 'Cliente creado exitosamente.');
            $this->redirect('/clients');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirectBack();
        }
    }

    /**
     * Show a single client with details.
     */
    public function show(array $vars): void
    {
        $client = $this->clientService->find((int) $vars['id']);

        if (!$client) {
            $this->redirect('/clients');
        }

        $this->render('clients/show', [
            'title' => $client->company_name,
            'client' => $client,
        ]);
    }

    /**
     * Show edit client form.
     */
    public function edit(array $vars): void
    {
        $client = $this->clientService->find((int) $vars['id']);

        if (!$client) {
            $this->redirect('/clients');
        }

        $this->render('clients/form', [
            'title' => 'Editar Cliente',
            'client' => $client,
        ]);
    }

    /**
     * Update a client.
     */
    public function update(array $vars): void
    {
        $this->validateCsrf();

        try {
            $data = [
                'company_name' => $_POST['company_name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'industry' => $_POST['industry'] ?? '',
            ];

            $this->clientService->update((int) $vars['id'], $data);
            Session::flash('success', 'Cliente actualizado exitosamente.');
            $this->redirect('/clients');
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirectBack();
        }
    }

    /**
     * Soft-delete a client.
     */
    public function destroy(array $vars): void
    {
        $this->validateCsrf();
        $this->clientService->delete((int) $vars['id']);
        Session::flash('success', 'Cliente eliminado exitosamente.');
        $this->redirect('/clients');
    }
}
