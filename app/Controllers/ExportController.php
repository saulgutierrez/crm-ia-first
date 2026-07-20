<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Database;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends BaseController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Export clients list as PDF.
     */
    public function clientsPdf(): void
    {
        $search = $_GET['search'] ?? '';

        if ($search) {
            $like = '%' . $search . '%';
            $stmt = $this->db->prepare("
                SELECT c.*, u.name AS owner_name
                FROM clients c LEFT JOIN users u ON u.id = c.owner_id
                WHERE c.deleted_at IS NULL
                AND (c.company_name LIKE :term1 OR c.email LIKE :term2 OR c.industry LIKE :term3)
                ORDER BY c.company_name ASC
            ");
            $stmt->bindValue('term1', $like, \PDO::PARAM_STR);
            $stmt->bindValue('term2', $like, \PDO::PARAM_STR);
            $stmt->bindValue('term3', $like, \PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $this->db->query("
                SELECT c.*, u.name AS owner_name
                FROM clients c LEFT JOIN users u ON u.id = c.owner_id
                WHERE c.deleted_at IS NULL
                ORDER BY c.company_name ASC
            ");
        }
        $clients = $stmt->fetchAll();

        $html = $this->buildClientPdfHtml($clients);
        $this->renderPdf($html, 'clientes.pdf');
    }

    /**
     * Export leads list as PDF.
     */
    public function leadsPdf(): void
    {
        $status = $_GET['status'] ?? '';
        $where = 'l.deleted_at IS NULL';
        $params = [];

        if ($status && in_array($status, ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'])) {
            $where .= ' AND l.status = :status';
            $params['status'] = $status;
        }

        $stmt = $this->db->prepare("
            SELECT l.*, c.company_name, u.name AS assigned_name
            FROM leads l
            JOIN clients c ON c.id = l.client_id
            LEFT JOIN users u ON u.id = l.assigned_to
            WHERE {$where}
            ORDER BY l.created_at DESC
        ");
        $stmt->execute($params);
        $leads = $stmt->fetchAll();

        // Calculate totals
        $totalValue = array_sum(array_map(fn($l) => (float) $l->estimated_value, $leads));

        $html = $this->buildLeadPdfHtml($leads, $totalValue);
        $this->renderPdf($html, 'oportunidades.pdf');
    }

    /**
     * Export dashboard report as PDF.
     */
    public function dashboardPdf(): void
    {
        // Summary stats
        $totalClients = (int) $this->db->query("SELECT COUNT(*) AS total FROM clients WHERE deleted_at IS NULL")->fetch()->total;
        $activeLeads = (int) $this->db->query("SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL AND status NOT IN ('won', 'lost')")->fetch()->total;
        $openTickets = (int) $this->db->query("SELECT COUNT(*) AS total FROM tickets WHERE status != 'closed'")->fetch()->total;
        $wonLeads = (int) $this->db->query("SELECT COUNT(*) AS total FROM leads WHERE deleted_at IS NULL AND status = 'won'")->fetch()->total;
        $pipelineValue = (float) $this->db->query("SELECT COALESCE(SUM(estimated_value), 0) AS total FROM leads WHERE deleted_at IS NULL")->fetch()->total;

        $leadsByStatus = $this->db->query("
            SELECT status, COUNT(*) AS total FROM leads 
            WHERE deleted_at IS NULL GROUP BY status
        ")->fetchAll();

        $html = $this->buildDashboardPdfHtml($totalClients, $activeLeads, $openTickets, $wonLeads, $pipelineValue, $leadsByStatus);
        $this->renderPdf($html, 'reporte-dashboard.pdf');
    }

    /**
     * Generate and stream a PDF.
     */
    private function renderPdf(string $html, string $filename): void
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo $dompdf->output();
        exit;
    }

    /**
     * Build HTML for clients PDF.
     */
    private function buildClientPdfHtml(array $clients): string
    {
        $rows = '';
        $i = 1;
        foreach ($clients as $c) {
            $rows .= "<tr>
                <td>{$i}</td>
                <td>" . htmlspecialchars($c->company_name) . "</td>
                <td>" . htmlspecialchars($c->email ?? '-') . "</td>
                <td>" . htmlspecialchars($c->phone ?? '-') . "</td>
                <td>" . htmlspecialchars($c->industry ?? '-') . "</td>
            </tr>";
            $i++;
        }

        return '
        <html>
        <head><meta charset="utf-8">
        <style>
            body { font-family: Helvetica, sans-serif; font-size: 10pt; color: #333; }
            h1 { color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; }
            .meta { color: #666; font-size: 9pt; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #4f46e5; color: white; padding: 8px 6px; text-align: left; font-size: 9pt; }
            td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9pt; }
            tr:nth-child(even) { background: #f9fafb; }
            .footer { margin-top: 20px; font-size: 8pt; color: #999; text-align: center; }
        </style>
        </head>
        <body>
            <h1>Reporte de Clientes</h1>
            <p class="meta">Generado: ' . date('d/m/Y H:i') . ' | Total: ' . count($clients) . ' clientes</p>
            <table>
                <tr><th>#</th><th>Empresa</th><th>Email</th><th>Teléfono</th><th>Industria</th></tr>
                ' . $rows . '
            </table>
            <p class="footer">CRM Profesional - Reporte generado el ' . date('d/m/Y H:i') . '</p>
        </body>
        </html>';
    }

    /**
     * Build HTML for leads PDF.
     */
    private function buildLeadPdfHtml(array $leads, float $totalValue): string
    {
        $statusLabels = [
            'new' => 'Nuevo', 'contacted' => 'Contactado', 'qualified' => 'Calificado',
            'proposal' => 'Propuesta', 'won' => 'Ganado', 'lost' => 'Perdido'
        ];

        $rows = '';
        $i = 1;
        foreach ($leads as $l) {
            $rows .= "<tr>
                <td>{$i}</td>
                <td>" . htmlspecialchars($l->title) . "</td>
                <td>" . htmlspecialchars($l->company_name) . "</td>
                <td>" . ($statusLabels[$l->status] ?? $l->status) . "</td>
                <td>$" . number_format((float) $l->estimated_value, 2) . "</td>
                <td>" . htmlspecialchars($l->assigned_name ?? '-') . "</td>
            </tr>";
            $i++;
        }

        return '
        <html>
        <head><meta charset="utf-8">
        <style>
            body { font-family: Helvetica, sans-serif; font-size: 10pt; color: #333; }
            h1 { color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; }
            .meta { color: #666; font-size: 9pt; margin-bottom: 20px; }
            .summary { background: #f3f4f6; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
            .summary span { font-weight: bold; color: #4f46e5; }
            table { width: 100%; border-collapse: collapse; }
            th { background: #4f46e5; color: white; padding: 8px 6px; text-align: left; font-size: 9pt; }
            td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9pt; }
            tr:nth-child(even) { background: #f9fafb; }
        </style>
        </head>
        <body>
            <h1>Reporte de Oportunidades</h1>
            <p class="meta">Generado: ' . date('d/m/Y H:i') . ' | Total: ' . count($leads) . ' oportunidades</p>
            <div class="summary">Valor total del pipeline: <span>$' . number_format($totalValue, 2) . '</span></div>
            <table>
                <tr><th>#</th><th>Título</th><th>Cliente</th><th>Estado</th><th>Valor</th><th>Responsable</th></tr>
                ' . $rows . '
            </table>
        </body>
        </html>';
    }

    /**
     * Build HTML for dashboard PDF report.
     */
    private function buildDashboardPdfHtml(int $totalClients, int $activeLeads, int $openTickets, int $wonLeads, float $pipelineValue, array $leadsByStatus): string
    {
        $statusLabels = [
            'new' => 'Nuevo', 'contacted' => 'Contactado', 'qualified' => 'Calificado',
            'proposal' => 'Propuesta', 'won' => 'Ganado', 'lost' => 'Perdido'
        ];

        $statusRows = '';
        foreach ($leadsByStatus as $ls) {
            $statusRows .= "<tr>
                <td>" . ($statusLabels[$ls->status] ?? $ls->status) . "</td>
                <td>{$ls->total}</td>
            </tr>";
        }

        return '
        <html>
        <head><meta charset="utf-8">
        <style>
            body { font-family: Helvetica, sans-serif; font-size: 10pt; color: #333; }
            h1 { color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 5px; }
            h2 { color: #374151; font-size: 12pt; margin-top: 20px; }
            .meta { color: #666; font-size: 9pt; margin-bottom: 20px; }
            .kpi-grid { display: flex; gap: 10px; margin-bottom: 20px; }
            .kpi-box { flex: 1; background: #f3f4f6; padding: 10px; border-radius: 5px; text-align: center; }
            .kpi-value { font-size: 18pt; font-weight: bold; color: #4f46e5; }
            .kpi-label { font-size: 8pt; color: #666; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background: #4f46e5; color: white; padding: 8px 6px; text-align: left; font-size: 9pt; }
            td { padding: 6px; border-bottom: 1px solid #ddd; font-size: 9pt; }
            tr:nth-child(even) { background: #f9fafb; }
            .footer { margin-top: 30px; font-size: 8pt; color: #999; text-align: center; }
        </style>
        </head>
        <body>
            <h1>Reporte del Dashboard</h1>
            <p class="meta">Generado: ' . date('d/m/Y H:i') . '</p>

            <h2>Indicadores Clave</h2>
            <table>
                <tr><td>Total Clientes</td><td><strong>' . $totalClients . '</strong></td></tr>
                <tr><td>Oportunidades Activas</td><td><strong>' . $activeLeads . '</strong></td></tr>
                <tr><td>Tickets Abiertos</td><td><strong>' . $openTickets . '</strong></td></tr>
                <tr><td>Oportunidades Ganadas</td><td><strong>' . $wonLeads . '</strong></td></tr>
                <tr><td>Valor Total Pipeline</td><td><strong>$' . number_format($pipelineValue, 2) . '</strong></td></tr>
            </table>

            <h2>Oportunidades por Estado</h2>
            <table>
                <tr><th>Estado</th><th>Cantidad</th></tr>
                ' . $statusRows . '
            </table>

            <p class="footer">CRM Profesional - Reporte generado el ' . date('d/m/Y H:i') . '</p>
        </body>
        </html>';
    }
}
