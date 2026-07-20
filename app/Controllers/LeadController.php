<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use App\Helpers\Pagination;
use App\Helpers\Session;

class LeadController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List leads with pagination and filters.
     * Agents only see their assigned leads; admins see all.
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $status = $_GET['status'] ?? '';

        $where = 'l.deleted_at IS NULL';
        $params = [];

        // Agents only see their assigned leads
        if (!Session::isRole('admin')) {
            $where .= ' AND l.assigned_to = :user_id';
            $params['user_id'] = Session::userId();
        }

        if ($status && in_array($status, ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'])) {
            $where .= ' AND l.status = :status';
            $params['status'] = $status;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) AS total FROM leads l WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()->total;

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT l.*, c.company_name, u.name AS assigned_name
            FROM leads l
            JOIN clients c ON c.id = l.client_id
            LEFT JOIN users u ON u.id = l.assigned_to
            WHERE {$where}
            ORDER BY l.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }
        $stmt->execute();
        $leads = $stmt->fetchAll();

        $this->render('leads/index', [
            'title' => 'Oportunidades',
            'leads' => $leads,
            'pagination' => Pagination::metadata($total, $page, $perPage),
            'currentStatus' => $status,
        ]);
    }

    /**
     * Show create lead form.
     * Agents can only assign leads to themselves.
     */
    public function create(): void
    {
        $clients = $this->db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name ASC")->fetchAll();
        
        // Agents can only assign to themselves; admins can assign to anyone
        if (Session::isRole('admin')) {
            $agents = $this->db->query("SELECT id, name FROM users WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        } else {
            $agents = $this->db->query("SELECT id, name FROM users WHERE id = " . (int) Session::userId() . " ORDER BY name ASC")->fetchAll();
        }

        $this->render('leads/form', [
            'title' => 'Nueva Oportunidad',
            'lead' => null,
            'clients' => $clients,
            'agents' => $agents,
        ]);
    }

    /**
     * Store a new lead.
     */
    public function store(): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
            'title' => $_POST['title'] ?? '',
            'status' => $_POST['status'] ?? 'new',
            'estimated_value' => (float) ($_POST['estimated_value'] ?? 0),
            'source' => $_POST['source'] ?? null,
            'expected_close_date' => $_POST['expected_close_date'] ?: null,
        ];

        $stmt = $this->db->prepare("
            INSERT INTO leads (client_id, assigned_to, title, status, estimated_value, source, expected_close_date)
            VALUES (:client_id, :assigned_to, :title, :status, :estimated_value, :source, :expected_close_date)
        ");
        $stmt->execute($data);

        Session::flash('success', 'Oportunidad creada exitosamente.');
        $this->redirect('/leads');
    }

    /**
     * Show a single lead.
     */
    public function show(array $vars): void
    {
        $stmt = $this->db->prepare("
            SELECT l.*, c.company_name, c.email AS client_email, c.phone AS client_phone,
                   u.name AS assigned_name
            FROM leads l
            JOIN clients c ON c.id = l.client_id
            LEFT JOIN users u ON u.id = l.assigned_to
            WHERE l.id = :id AND l.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['id' => (int) $vars['id']]);
        $lead = $stmt->fetch();

        if (!$lead) {
            $this->redirect('/leads');
        }

        $this->render('leads/show', [
            'title' => $lead->title,
            'lead' => $lead,
        ]);
    }

    /**
     * Show edit lead form.
     */
    public function edit(array $vars): void
    {
        $stmt = $this->db->prepare("SELECT * FROM leads WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id' => (int) $vars['id']]);
        $lead = $stmt->fetch();

        if (!$lead) {
            $this->redirect('/leads');
        }            $clients = $this->db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name ASC")->fetchAll();
        
        // Agents can only assign to themselves
        if (Session::isRole('admin')) {
            $agents = $this->db->query("SELECT id, name FROM users WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        } else {
            $agents = $this->db->query("SELECT id, name FROM users WHERE id = " . (int) Session::userId() . " ORDER BY name ASC")->fetchAll();
        }

        $this->render('leads/form', [
            'title' => 'Editar Oportunidad',
            'lead' => $lead,
            'clients' => $clients,
            'agents' => $agents,
        ]);
    }

    /**
     * Update a lead.
     */
    public function update(array $vars): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
            'title' => $_POST['title'] ?? '',
            'status' => $_POST['status'] ?? 'new',
            'estimated_value' => (float) ($_POST['estimated_value'] ?? 0),
            'source' => $_POST['source'] ?? null,
            'expected_close_date' => $_POST['expected_close_date'] ?: null,
            'id' => (int) $vars['id'],
        ];

        $stmt = $this->db->prepare("
            UPDATE leads SET client_id = :client_id, assigned_to = :assigned_to,
            title = :title, status = :status, estimated_value = :estimated_value,
            source = :source, expected_close_date = :expected_close_date
            WHERE id = :id
        ");
        $stmt->execute($data);

        Session::flash('success', 'Oportunidad actualizada exitosamente.');
        $this->redirect('/leads');
    }

    /**
     * Soft-delete a lead.
     */
    public function destroy(array $vars): void
    {
        $this->validateCsrf();
        $stmt = $this->db->prepare("UPDATE leads SET deleted_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => (int) $vars['id']]);

        Session::flash('success', 'Oportunidad eliminada.');
        $this->redirect('/leads');
    }
}
