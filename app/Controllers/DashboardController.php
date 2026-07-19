<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;

class DashboardController extends BaseController
{
    /**
     * Show the main dashboard with key metrics.
     */
    public function index(): void
    {
        $db = Database::getInstance();

        // Total clients
        $totalClients = (int) $db->query("SELECT COUNT(*) AS total FROM clients WHERE deleted_at IS NULL")->fetch()->total;

        // Active leads
        $activeLeads = (int) $db->query("SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost')")->fetch()->total;

        // Open tickets
        $openTickets = (int) $db->query("SELECT COUNT(*) AS total FROM tickets WHERE status != 'closed'")->fetch()->total;

        // Total pipeline value
        $pipelineValue = (float) $db->query("SELECT COALESCE(SUM(estimated_value), 0) AS total FROM leads WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost')")->fetch()->total;

        // Won leads value
        $wonValue = (float) $db->query("SELECT COALESCE(SUM(estimated_value), 0) AS total FROM leads WHERE deleted_at IS NULL AND status = 'won'")->fetch()->total;

        // Leads by status for chart
        $leadsByStatus = $db->query("
            SELECT status, COUNT(*) AS total FROM leads 
            WHERE deleted_at IS NULL 
            GROUP BY status 
            ORDER BY FIELD(status, 'new', 'contacted', 'qualified', 'proposal', 'won', 'lost')
        ")->fetchAll();

        // Tickets by status for chart
        $ticketsByStatus = $db->query("
            SELECT status, COUNT(*) AS total FROM tickets 
            GROUP BY status 
            ORDER BY FIELD(status, 'open', 'in_progress', 'resolved', 'closed')
        ")->fetchAll();

        // Monthly interactions for trend chart (last 6 months)
        $monthlyInteractions = $db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total 
            FROM interactions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll();

        // Recent interactions
        $recentInteractions = $db->query("
            SELECT i.*, c.company_name 
            FROM interactions i 
            JOIN clients c ON c.id = i.client_id 
            ORDER BY i.created_at DESC 
            LIMIT 5
        ")->fetchAll();

        // Recent clients
        $recentClients = $db->query("
            SELECT id, company_name, created_at 
            FROM clients WHERE deleted_at IS NULL 
            ORDER BY created_at DESC LIMIT 5
        ")->fetchAll();

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'totalClients' => $totalClients,
            'activeLeads' => $activeLeads,
            'openTickets' => $openTickets,
            'pipelineValue' => $pipelineValue,
            'wonValue' => $wonValue,
            'leadsByStatus' => $leadsByStatus,
            'ticketsByStatus' => $ticketsByStatus,
            'monthlyInteractions' => $monthlyInteractions,
            'recentInteractions' => $recentInteractions,
            'recentClients' => $recentClients,
        ]);
    }
}
