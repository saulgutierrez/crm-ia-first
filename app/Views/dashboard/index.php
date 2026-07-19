<!-- Page Header -->
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    <div class="flex items-center gap-3">
        <p class="text-sm text-gray-500">Bienvenido, <?= htmlspecialchars($session['user_name'] ?? '') ?></p>
        <a href="/export/dashboard" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Reporte PDF
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Clientes</p>
                <p class="text-3xl font-bold text-gray-800 mt-1"><?= number_format($totalClients) ?></p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Oportunidades Activas</p>
                <p class="text-3xl font-bold text-gray-800 mt-1"><?= number_format($activeLeads) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Tickets Abiertos</p>
                <p class="text-3xl font-bold text-gray-800 mt-1"><?= number_format($openTickets) ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pipeline Total</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">$<?= number_format($pipelineValue, 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Pipeline Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Pipeline de Ventas</h3>
        <canvas id="leadsChart" height="200"></canvas>
    </div>
    
    <!-- Tickets Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Tickets por Estado</h3>
        <canvas id="ticketsChart" height="200"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Interactions Trend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 lg:col-span-2">
        <h3 class="font-semibold text-gray-800 mb-4">Tendencia de Interacciones</h3>
        <canvas id="interactionsChart" height="180"></canvas>
    </div>
    
    <!-- Quick Stats -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Resumen Rápido</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Valor Ganado</span>
                <span class="font-semibold text-green-600">$<?= number_format($wonValue, 0) ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Pipeline Activo</span>
                <span class="font-semibold text-indigo-600">$<?= number_format($pipelineValue, 0) ?></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <?php $totalPipeline = $wonValue + $pipelineValue; ?>
                <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: <?= $totalPipeline > 0 ? ($wonValue / $totalPipeline * 100) : 0 ?>%"></div>
            </div>
            <p class="text-xs text-gray-400">
                <?= $totalPipeline > 0 ? number_format($wonValue / $totalPipeline * 100, 1) : 0 ?>% de oportunidades ganadas
            </p>
            <hr class="border-gray-100">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Clientes Nuevos</span>
                <span class="font-semibold"><?= count($recentClients) ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Interacciones (6 meses)</span>
                <span class="font-semibold"><?= array_sum(array_column($monthlyInteractions, 'total')) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Interacciones Recientes</h3>
            <a href="/interactions" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todas</a>
        </div>
        <div class="p-5">
            <?php if (empty($recentInteractions)): ?>
            <p class="text-gray-500 text-center py-4">No hay interacciones registradas aún.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($recentInteractions as $interaction): ?>
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium <?= $interaction->type === 'call' ? 'bg-green-100 text-green-700' : ($interaction->type === 'email' ? 'bg-blue-100 text-blue-700' : ($interaction->type === 'meeting' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700')) ?>">
                        <?= strtoupper(substr($interaction->type ?? 'N', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate"><?= htmlspecialchars($interaction->subject ?? '') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($interaction->company_name ?? '') ?></p>
                    </div>
                    <span class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($interaction->created_at)) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Clientes Recientes</h3>
            <a href="/clients" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todos</a>
        </div>
        <div class="p-5">
            <?php if (empty($recentClients)): ?>
            <p class="text-gray-500 text-center py-4">No hay clientes registrados aún.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($recentClients as $client): ?>
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="/clients/<?= $client->id ?>" class="text-sm font-medium text-gray-800 hover:text-indigo-600 truncate block">
                            <?= htmlspecialchars($client->company_name) ?>
                        </a>
                    </div>
                    <span class="text-xs text-gray-400"><?= date('d/m/Y', strtotime($client->created_at)) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart Scripts -->
<?php
// Label mappings for charts (PHP arrays)
$leadLabelsMap = ['new' => 'Nuevo', 'contacted' => 'Contactado', 'qualified' => 'Calificado', 'proposal' => 'Propuesta', 'won' => 'Ganado', 'lost' => 'Perdido'];
$leadColorsMap = ['new' => '#3b82f6', 'contacted' => '#f97316', 'qualified' => '#4f46e5', 'proposal' => '#8b5cf6', 'won' => '#10b981', 'lost' => '#ef4444'];
$fallbackColor = '#6b7280';
$ticketLabelsMap = ['open' => 'Abierto', 'in_progress' => 'En Progreso', 'resolved' => 'Resuelto', 'closed' => 'Cerrado'];
$ticketColorsMap = ['open' => '#eab308', 'in_progress' => '#3b82f6', 'resolved' => '#10b981', 'closed' => '#6b7280'];
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($leadsByStatus) || !empty($ticketsByStatus) || !empty($monthlyInteractions)): ?>
    const colors = { indigo: '#4f46e5', green: '#10b981', yellow: '#eab308', blue: '#3b82f6', purple: '#8b5cf6', red: '#ef4444', orange: '#f97316', gray: '#6b7280' };
    <?php endif; ?>

    // 1. Leads Pipeline Chart (Doughnut)
    <?php if (!empty($leadsByStatus)): ?>
    (function() {
        const ctx = document.getElementById('leadsChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_map(fn($ls) => $leadLabelsMap[$ls->status] ?? $ls->status, $leadsByStatus)) ?>,
                datasets: [{
                    data: <?= json_encode(array_map(fn($ls) => (int) $ls->total, $leadsByStatus)) ?>,
                    backgroundColor: <?= json_encode(array_map(fn($ls) => $leadColorsMap[$ls->status] ?? $fallbackColor, $leadsByStatus)) ?>,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, font: { size: 11 } } }
                }
            }
        });
    })();
    <?php endif; ?>

    // 2. Tickets Chart (Bar)
    <?php if (!empty($ticketsByStatus)): ?>
    (function() {
        const ctx = document.getElementById('ticketsChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(fn($ts) => $ticketLabelsMap[$ts->status] ?? $ts->status, $ticketsByStatus)) ?>,
                datasets: [{
                    label: 'Tickets',
                    data: <?= json_encode(array_map(fn($ts) => (int) $ts->total, $ticketsByStatus)) ?>,
                    backgroundColor: <?= json_encode(array_map(fn($ts) => $ticketColorsMap[$ts->status] ?? $fallbackColor, $ticketsByStatus)) ?>,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
                    x: { ticks: { font: { size: 11 } } }
                }
            }
        });
    })();
    <?php endif; ?>

    // 3. Interactions Trend (Line)
    <?php if (!empty($monthlyInteractions)): ?>
    (function() {
        const ctx = document.getElementById('interactionsChart');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_map(fn($mi) => $mi->month, $monthlyInteractions)) ?>,
                datasets: [{
                    label: 'Interacciones',
                    data: <?= json_encode(array_map(fn($mi) => (int) $mi->total, $monthlyInteractions)) ?>,
                    borderColor: colors.indigo,
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: colors.indigo,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
                    x: { ticks: { font: { size: 10 } } }
                }
            }
        });
    })();
    <?php endif; ?>
});
</script>
