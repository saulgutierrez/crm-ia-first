<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Repositories\Database;
use App\Helpers\Pagination;

class LeadApiController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * GET /api/v1/leads
     */
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $status = $_GET['status'] ?? '';

        $where = 'l.deleted_at IS NULL';
        $params = [];

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

        \App\Helpers\Response::paginated($leads, $total, $page, $perPage);
    }

    /**
     * POST /api/v1/leads
     */
    public function store(): void
    {
        $body = $this->getJsonBody();

        $data = [
            'client_id' => (int) ($body['client_id'] ?? 0),
            'assigned_to' => !empty($body['assigned_to']) ? (int) $body['assigned_to'] : null,
            'title' => $body['title'] ?? '',
            'status' => $body['status'] ?? 'new',
            'estimated_value' => (float) ($body['estimated_value'] ?? 0),
            'source' => $body['source'] ?? null,
            'expected_close_date' => $body['expected_close_date'] ?? null,
        ];

        if (empty($data['title'])) {
            $this->error('El título es obligatorio.', 422);
        }

        $stmt = $this->db->prepare("
            INSERT INTO leads (client_id, assigned_to, title, status, estimated_value, source, expected_close_date)
            VALUES (:client_id, :assigned_to, :title, :status, :estimated_value, :source, :expected_close_date)
        ");
        $stmt->execute($data);

        $leadId = (int) $this->db->lastInsertId();
        $lead = $this->findLead($leadId);
        $this->json($lead, 201, 'Oportunidad creada exitosamente.');
    }

    /**
     * GET /api/v1/leads/{id}
     */
    public function show(array $vars): void
    {
        $lead = $this->findLead((int) $vars['id']);

        if (!$lead) {
            $this->error('Oportunidad no encontrada.', 404);
        }

        $this->json($lead);
    }

    /**
     * PUT /api/v1/leads/{id}
     */
    public function update(array $vars): void
    {
        $body = $this->getJsonBody();
        $id = (int) $vars['id'];

        $data = [
            'client_id' => (int) ($body['client_id'] ?? 0),
            'assigned_to' => !empty($body['assigned_to']) ? (int) $body['assigned_to'] : null,
            'title' => $body['title'] ?? '',
            'status' => $body['status'] ?? 'new',
            'estimated_value' => (float) ($body['estimated_value'] ?? 0),
            'source' => $body['source'] ?? null,
            'expected_close_date' => $body['expected_close_date'] ?? null,
        ];

        $stmt = $this->db->prepare("
            UPDATE leads SET client_id = :client_id, assigned_to = :assigned_to,
            title = :title, status = :status, estimated_value = :estimated_value,
            source = :source, expected_close_date = :expected_close_date
            WHERE id = :id
        ");
        $data['id'] = $id;
        $stmt->execute($data);

        $lead = $this->findLead($id);
        $this->json($lead, 200, 'Oportunidad actualizada exitosamente.');
    }

    /**
     * DELETE /api/v1/leads/{id}
     */
    public function destroy(array $vars): void
    {
        $stmt = $this->db->prepare("UPDATE leads SET deleted_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => (int) $vars['id']]);

        $this->json(null, 200, 'Oportunidad eliminada.');
    }

    /**
     * Find a lead by ID with related data.
     */
    private function findLead(int $id): ?\stdClass
    {
        $stmt = $this->db->prepare("
            SELECT l.*, c.company_name, u.name AS assigned_name
            FROM leads l
            JOIN clients c ON c.id = l.client_id
            LEFT JOIN users u ON u.id = l.assigned_to
            WHERE l.id = :id AND l.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
