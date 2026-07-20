<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use App\Helpers\Pagination;
use App\Helpers\Session;

class TicketController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * List tickets with pagination and status filter.
     * Agents only see tickets assigned to them; admins see all.
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $status = $_GET['status'] ?? '';

        $where = '1=1';
        $params = [];

        // Agents only see tickets assigned to them
        if (!Session::isRole('admin')) {
            $where .= ' AND t.assigned_to = :user_id';
            $params['user_id'] = Session::userId();
        }

        if ($status && in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            $where .= ' AND t.status = :status';
            $params['status'] = $status;
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) AS total FROM tickets t WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()->total;

        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("
            SELECT t.*, c.company_name, u.name AS assigned_name
            FROM tickets t
            JOIN clients c ON c.id = t.client_id
            LEFT JOIN users u ON u.id = t.assigned_to
            WHERE {$where}
            ORDER BY 
                CASE t.priority 
                    WHEN 'urgent' THEN 0 
                    WHEN 'high' THEN 1 
                    WHEN 'medium' THEN 2 
                    WHEN 'low' THEN 3 
                END ASC,
                t.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }
        $stmt->execute();
        $tickets = $stmt->fetchAll();

        $this->render('tickets/index', [
            'title' => 'Tickets de Soporte',
            'tickets' => $tickets,
            'pagination' => Pagination::metadata($total, $page, $perPage),
            'currentStatus' => $status,
        ]);
    }

    /**
     * Show create ticket form.
     */
    public function create(): void
    {
        $clients = $this->db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name ASC")->fetchAll();
        
        // Agents can only assign to themselves
        if (Session::isRole('admin')) {
            $agents = $this->db->query("SELECT id, name FROM users WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        } else {
            $agents = $this->db->query("SELECT id, name FROM users WHERE id = " . (int) Session::userId() . " ORDER BY name ASC")->fetchAll();
        }

        $this->render('tickets/form', [
            'title' => 'Nuevo Ticket',
            'ticket' => null,
            'clients' => $clients,
            'agents' => $agents,
        ]);
    }

    /**
     * Store a new ticket.
     */
    public function store(): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
            'subject' => $_POST['subject'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? 'medium',
            'status' => 'open',
            'created_by' => Session::userId(),
        ];

        $stmt = $this->db->prepare("
            INSERT INTO tickets (client_id, assigned_to, subject, description, priority, status, created_by)
            VALUES (:client_id, :assigned_to, :subject, :description, :priority, :status, :created_by)
        ");
        $stmt->execute($data);

        Session::flash('success', 'Ticket creado exitosamente.');
        $this->redirect('/tickets');
    }

    /**
     * Show a single ticket.
     */
    public function show(array $vars): void
    {
        $stmt = $this->db->prepare("
            SELECT t.*, c.company_name, c.email AS client_email, 
                   u.name AS assigned_name, cr.name AS creator_name
            FROM tickets t
            JOIN clients c ON c.id = t.client_id
            LEFT JOIN users u ON u.id = t.assigned_to
            LEFT JOIN users cr ON cr.id = t.created_by
            WHERE t.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => (int) $vars['id']]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            $this->redirect('/tickets');
        }

        $this->render('tickets/show', [
            'title' => $ticket->subject,
            'ticket' => $ticket,
        ]);
    }

    /**
     * Show edit ticket form.
     */
    public function edit(array $vars): void
    {
        $stmt = $this->db->prepare("SELECT * FROM tickets WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int) $vars['id']]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            $this->redirect('/tickets');
        }

        $clients = $this->db->query("SELECT id, company_name FROM clients WHERE deleted_at IS NULL ORDER BY company_name ASC")->fetchAll();
        
        if (Session::isRole('admin')) {
            $agents = $this->db->query("SELECT id, name FROM users WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
        } else {
            $agents = $this->db->query("SELECT id, name FROM users WHERE id = " . (int) Session::userId() . " ORDER BY name ASC")->fetchAll();
        }

        $this->render('tickets/form', [
            'title' => 'Editar Ticket',
            'ticket' => $ticket,
            'clients' => $clients,
            'agents' => $agents,
        ]);
    }

    /**
     * Update a ticket.
     */
    public function update(array $vars): void
    {
        $this->validateCsrf();

        $data = [
            'client_id' => (int) ($_POST['client_id'] ?? 0),
            'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
            'subject' => $_POST['subject'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? 'medium',
            'status' => $_POST['status'] ?? 'open',
            'id' => (int) $vars['id'],
        ];

        $stmt = $this->db->prepare("
            UPDATE tickets SET client_id = :client_id, assigned_to = :assigned_to,
            subject = :subject, description = :description,
            priority = :priority, status = :status
            WHERE id = :id
        ");
        $stmt->execute($data);

        Session::flash('success', 'Ticket actualizado exitosamente.');
        $this->redirect('/tickets');
    }

    /**
     * Delete a ticket.
     */
    public function destroy(array $vars): void
    {
        $this->validateCsrf();
        $stmt = $this->db->prepare("DELETE FROM tickets WHERE id = :id");
        $stmt->execute(['id' => (int) $vars['id']]);

        Session::flash('success', 'Ticket eliminado.');
        $this->redirect('/tickets');
    }
}
