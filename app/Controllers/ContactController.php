<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use App\Helpers\Pagination;
use App\Helpers\Session;
use App\Exceptions\HttpException;

class ContactController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List contacts for a client.
     */
    public function index(array $vars): void
    {
        $clientId = (int) $vars['clientId'];
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();

        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM contacts WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $clientId]);
        $total = (int) $stmt->fetch()->total;

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT * FROM contacts 
            WHERE client_id = :client_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('client_id', $clientId, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $contacts = $stmt->fetchAll();

        $this->render('contacts/index', [
            'title' => 'Contactos',
            'clientId' => $clientId,
            'contacts' => $contacts,
            'pagination' => \App\Helpers\Pagination::metadata($total, $page, $perPage),
        ]);
    }

    /**
     * Show create contact form.
     */
    public function create(array $vars): void
    {
        $this->render('contacts/form', [
            'title' => 'Nuevo Contacto',
            'clientId' => (int) $vars['clientId'],
            'contact' => null,
        ]);
    }

    /**
     * Store a new contact.
     */
    public function store(array $vars): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) $vars['clientId'],
            'full_name' => $_POST['full_name'] ?? '',
            'position' => $_POST['position'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'is_primary' => isset($_POST['is_primary']) ? 1 : 0,
        ];

        $stmt = $this->db->prepare("
            INSERT INTO contacts (client_id, full_name, position, email, phone, is_primary)
            VALUES (:client_id, :full_name, :position, :email, :phone, :is_primary)
        ");
        $stmt->execute($data);

        Session::flash('success', 'Contacto creado exitosamente.');
        $this->redirect("/clients/{$data['client_id']}/contacts");
    }

    /**
     * Show edit contact form.
     */
    public function edit(array $vars): void
    {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int) $vars['id']]);
        $contact = $stmt->fetch();

        if (!$contact) {
            $this->redirect('/clients');
        }

        $this->render('contacts/form', [
            'title' => 'Editar Contacto',
            'clientId' => $contact->client_id,
            'contact' => $contact,
        ]);
    }

    /**
     * Update a contact.
     */
    public function update(array $vars): void
    {
        $this->validateCsrf();

        $data = [
            'full_name' => $_POST['full_name'] ?? '',
            'position' => $_POST['position'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'is_primary' => isset($_POST['is_primary']) ? 1 : 0,
        ];

        $stmt = $this->db->prepare("
            UPDATE contacts SET full_name = :full_name, position = :position, 
            email = :email, phone = :phone, is_primary = :is_primary
            WHERE id = :id
        ");
        $data['id'] = (int) $vars['id'];
        $stmt->execute($data);

        Session::flash('success', 'Contacto actualizado exitosamente.');
        $this->redirectBack();
    }

    /**
     * Delete a contact.
     */
    public function destroy(array $vars): void
    {
        $this->validateCsrf();

        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = :id");
        $stmt->execute(['id' => (int) $vars['id']]);

        Session::flash('success', 'Contacto eliminado.');
        $this->redirectBack();
    }
}
