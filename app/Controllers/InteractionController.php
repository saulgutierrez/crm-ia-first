<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use App\Helpers\Pagination;
use App\Helpers\Session;

class InteractionController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List interactions with pagination.
     * Agents only see their own interactions; admins see all.
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $type = $_GET['type'] ?? '';

        $where = '1=1';
        $params = [];

        // Agents only see their own interactions
        if (!Session::isRole('admin')) {
            $where .= ' AND i.created_by = :user_id';
            $params['user_id'] = Session::userId();
        }

        if ($type && in_array($type, ['call', 'email', 'meeting', 'note'])) {
            $where .= ' AND i.type = :type';
            $params['type'] = $type;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) AS total FROM interactions i WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()->total;

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT i.*, c.company_name, u.name AS user_name
            FROM interactions i
            JOIN clients c ON c.id = i.client_id
            LEFT JOIN users u ON u.id = i.created_by
            WHERE {$where}
            ORDER BY i.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }
        $stmt->execute();
        $interactions = $stmt->fetchAll();

        $this->render('interactions/index', [
            'title' => 'Interacciones',
            'interactions' => $interactions,
            'pagination' => Pagination::metadata($total, $page, $perPage),
            'currentType' => $type,
        ]);
    }

    /**
     * Show create interaction form.
     */
    public function create(): void
    {
        $clients = $this->db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name ASC")->fetchAll();

        $this->render('interactions/form', [
            'title' => 'Nueva Interacción',
            'clients' => $clients,
        ]);
    }

    /**
     * Store a new interaction.
     */
    public function store(): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'type' => $_POST['type'] ?? 'note',
            'subject' => $_POST['subject'] ?? '',
            'description' => $_POST['description'] ?? '',
            'created_by' => Session::userId(),
        ];

        $stmt = $this->db->prepare("
            INSERT INTO interactions (client_id, type, subject, description, created_by)
            VALUES (:client_id, :type, :subject, :description, :created_by)
        ");
        $stmt->execute($data);

        Session::flash('success', 'Interacción registrada exitosamente.');
        $this->redirect('/interactions');
    }

    /**
     * Show edit interaction form.
     */
    public function edit(array $vars): void
    {
        $stmt = $this->db->prepare("SELECT * FROM interactions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int) $vars['id']]);
        $interaction = $stmt->fetch();

        if (!$interaction) {
            $this->redirect('/interactions');
        }

        $clients = $this->db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name ASC")->fetchAll();

        $this->render('interactions/form', [
            'title' => 'Editar Interacción',
            'interaction' => $interaction,
            'clients' => $clients,
        ]);
    }

    /**
     * Update an interaction.
     */
    public function update(array $vars): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'type' => $_POST['type'] ?? 'note',
            'subject' => $_POST['subject'] ?? '',
            'description' => $_POST['description'] ?? '',
            'id' => (int) $vars['id'],
        ];

        $stmt = $this->db->prepare("
            UPDATE interactions SET client_id = :client_id, type = :type,
            subject = :subject, description = :description
            WHERE id = :id
        ");
        $stmt->execute($data);

        Session::flash('success', 'Interacción actualizada exitosamente.');
        $this->redirect('/interactions');
    }

    /**
     * Show a single interaction with full details.
     */
    public function show(array $vars): void
    {
        $stmt = $this->db->prepare("
            SELECT i.*, c.company_name, c.email AS client_email, c.phone AS client_phone,
                   u.name AS user_name
            FROM interactions i
            JOIN clients c ON c.id = i.client_id
            LEFT JOIN users u ON u.id = i.created_by
            WHERE i.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => (int) $vars['id']]);
        $interaction = $stmt->fetch();

        if (!$interaction) {
            $this->redirect('/interactions');
        }

        $this->render('interactions/show', [
            'title' => $interaction->subject,
            'interaction' => $interaction,
        ]);
    }

    /**
     * Delete an interaction.
     */
    public function destroy(array $vars): void
    {
        $this->validateCsrf();
        $stmt = $this->db->prepare("DELETE FROM interactions WHERE id = :id");
        $stmt->execute(['id' => (int) $vars['id']]);

        Session::flash('success', 'Interacción eliminada.');
        $this->redirectBack();
    }
}
