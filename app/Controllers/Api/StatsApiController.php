<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Repositories\Database;

class StatsApiController extends BaseController
{
    /**
     * GET /api/v1/stats/dashboard
     */
    public function dashboard(): void
    {
        $db = Database::getInstance();

        $totalClients = (int) $db->query("SELECT COUNT(*) AS total FROM clients WHERE deleted_at IS NULL")->fetch()->total;

        $activeLeads = (int) $db->query("
            SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost')
        ")->fetch()->total;

        $openTickets = (int) $db->query("
            SELECT COUNT(*) AS total FROM tickets WHERE status != 'closed'
        ")->fetch()->total;

        $pipelineValue = (float) $db->query("
            SELECT COALESCE(SUM(estimated_value), 0) AS total FROM leads 
            WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost')
        ")->fetch()->total;

        $wonLeads = (int) $db->query("
            SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL AND status = 'won'
        ")->fetch()->total;

        $leadsByStatus = $db->query("
            SELECT status, COUNT(*) AS total FROM leads 
            WHERE deleted_at IS NULL 
            GROUP BY status ORDER BY FIELD(status, 'new', 'contacted', 'qualified', 'proposal', 'won', 'lost')
        ")->fetchAll();

        $ticketsByStatus = $db->query("
            SELECT status, COUNT(*) AS total FROM tickets 
            GROUP BY status ORDER BY FIELD(status, 'open', 'in_progress', 'resolved', 'closed')
        ")->fetchAll();

        $recentClients = $db->query("
            SELECT id, company_name, email, created_at 
            FROM clients WHERE deleted_at IS NULL 
            ORDER BY created_at DESC LIMIT 5
        ")->fetchAll();

        $this->json([
            'summary' => [
                'total_clients' => $totalClients,
                'active_leads' => $activeLeads,
                'open_tickets' => $openTickets,
                'pipeline_value' => $pipelineValue,
                'won_leads' => $wonLeads,
            ],
            'leads_by_status' => $leadsByStatus,
            'tickets_by_status' => $ticketsByStatus,
            'recent_clients' => $recentClients,
        ]);
    }
}
