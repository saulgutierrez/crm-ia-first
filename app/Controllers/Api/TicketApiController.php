<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Repositories\Database;
use App\Helpers\Pagination;
use App\Helpers\Response;

class TicketApiController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * GET /api/v1/tickets
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $status = $_GET['status'] ?? '';

        $where = '1=1';
        $params = [];

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

        Response::paginated($tickets, $total, $page, $perPage);
    }

    /**
     * POST /api/v1/tickets
     */
    public function store(): void
    {
        $body = $this->getJsonBody();

        $data = [
            'client_id' => (int) ($body['client_id'] ?? 0),
            'assigned_to' => !empty($body['assigned_to']) ? (int) $body['assigned_to'] : null,
            'subject' => $body['subject'] ?? '',
            'description' => $body['description'] ?? '',
            'priority' => $body['priority'] ?? 'medium',
            'status' => 'open',
            'created_by' => \App\Helpers\Session::userId(),
        ];

        if (empty($data['subject'])) {
            $this->error('El asunto es obligatorio.', 422);
        }

        $stmt = $this->db->prepare("
            INSERT INTO tickets (client_id, assigned_to, subject, description, priority, status, created_by)
            VALUES (:client_id, :assigned_to, :subject, :description, :priority, :status, :created_by)
        ");
        $stmt->execute($data);

        $ticketId = (int) $this->db->lastInsertId();
        $ticket = $this->findTicket($ticketId);
        $this->json($ticket, 201, 'Ticket creado exitosamente.');
    }

    /**
     * GET /api/v1/tickets/{id}
     */
    public function show(array $vars): void
    {
        $ticket = $this->findTicket((int) $vars['id']);

        if (!$ticket) {
            $this->error('Ticket no encontrado.', 404);
        }

        $this->json($ticket);
    }

    /**
     * PUT /api/v1/tickets/{id}
     */
    public function update(array $vars): void
    {
        $body = $this->getJsonBody();
        $id = (int) $vars['id'];

        $data = [
            'client_id' => (int) ($body['client_id'] ?? 0),
            'assigned_to' => !empty($body['assigned_to']) ? (int) $body['assigned_to'] : null,
            'subject' => $body['subject'] ?? '',
            'description' => $body['description'] ?? '',
            'priority' => $body['priority'] ?? 'medium',
            'status' => $body['status'] ?? 'open',
        ];

        $stmt = $this->db->prepare("
            UPDATE tickets SET client_id = :client_id, assigned_to = :assigned_to,
            subject = :subject, description = :description,
            priority = :priority, status = :status
            WHERE id = :id
        ");
        $data['id'] = $id;
        $stmt->execute($data);

        $ticket = $this->findTicket($id);
        $this->json($ticket, 200, 'Ticket actualizado exitosamente.');
    }

    /**
     * DELETE /api/v1/tickets/{id}
     */
    public function destroy(array $vars): void
    {
        $stmt = $this->db->prepare("DELETE FROM tickets WHERE id = :id");
        $stmt->execute(['id' => (int) $vars['id']]);

        $this->json(null, 200, 'Ticket eliminado.');
    }

    /**
     * Find a ticket by ID with related data.
     */
    private function findTicket(int $id): ?\stdClass
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
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
