<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use App\Helpers\Session;

class DashboardController extends BaseController
{
    /**
     * Show the main dashboard with key metrics.
     * Agents see data filtered to their own scope; admins see global data.
     */
    public function index(): void
    {
        $db = Database::getInstance();
        $userId = Session::userId();
        $isAdmin = Session::isRole('admin');

        // Build owner filter clause for agents
        // Note: column names without table prefix because queries below don't use aliases
        $ownerFilter = $isAdmin ? '' : ' AND owner_id = ' . (int) $userId;
        $leadFilter = $isAdmin ? '' : ' AND assigned_to = ' . (int) $userId;
        $ticketFilter = $isAdmin ? '' : ' AND assigned_to = ' . (int) $userId;
        $interactionFilter = $isAdmin ? '' : ' AND created_by = ' . (int) $userId;

        // Total clients
        $totalClients = (int) $db->query("SELECT COUNT(*) AS total FROM clients WHERE deleted_at IS NULL{$ownerFilter}")->fetch()->total;

        // Active leads
        $activeLeads = (int) $db->query("SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost'){$leadFilter}")->fetch()->total;

        // Open tickets
        $openTickets = (int) $db->query("SELECT COUNT(*) AS total FROM tickets WHERE status != 'closed'{$ticketFilter}")->fetch()->total;

        // Total pipeline value
        $pipelineValue = (float) $db->query("SELECT COALESCE(SUM(estimated_value), 0) AS total FROM leads WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost'){$leadFilter}")->fetch()->total;

        // Won leads value
        $wonValue = (float) $db->query("SELECT COALESCE(SUM(estimated_value), 0) AS total FROM leads WHERE deleted_at IS NULL AND status = 'won'{$leadFilter}")->fetch()->total;

        // Leads by status for chart
        $leadsByStatus = $db->query("
            SELECT status, COUNT(*) AS total FROM leads 
            WHERE deleted_at IS NULL{$leadFilter}
            GROUP BY status 
            ORDER BY FIELD(status, 'new', 'contacted', 'qualified', 'proposal', 'won', 'lost')
        ")->fetchAll();

        // Tickets by status for chart
        $ticketsByStatus = $db->query("
            SELECT status, COUNT(*) AS total FROM tickets 
            WHERE 1=1{$ticketFilter}
            GROUP BY status 
            ORDER BY FIELD(status, 'open', 'in_progress', 'resolved', 'closed')
        ")->fetchAll();

        // Monthly interactions for trend chart (last 6 months)
        $monthlyInteractions = $db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total 
            FROM interactions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH){$interactionFilter}
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll();

        // Recent interactions
        $recentInteractions = $db->query("
            SELECT i.*, c.company_name 
            FROM interactions i 
            JOIN clients c ON c.id = i.client_id 
            WHERE 1=1{$interactionFilter}
            ORDER BY i.created_at DESC 
            LIMIT 5
        ")->fetchAll();

        // Recent clients
        $recentClients = $db->query("
            SELECT id, company_name, created_at 
            FROM clients WHERE deleted_at IS NULL{$ownerFilter}
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
