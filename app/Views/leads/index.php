<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Oportunidades</h2>
    <div class="flex gap-2">
        <a href="/export/leads<?= $currentStatus ? '?status=' . $currentStatus : '' ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exportar PDF
        </a>
        <a href="/leads/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
            + Nueva Oportunidad
        </a>
    </div>
</div>

<!-- Status Filter -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="/leads" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= !$currentStatus ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Todas</a>
    <?php 
    $statuses = ['new' => 'Nuevo', 'contacted' => 'Contactado', 'qualified' => 'Calificado', 'proposal' => 'Propuesta', 'won' => 'Ganado', 'lost' => 'Perdido'];
    foreach ($statuses as $key => $label): ?>
    <a href="/leads?status=<?= $key ?>" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentStatus === $key ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                <th class="px-5 py-3">Título</th>
                <th class="px-5 py-3">Cliente</th>
                <th class="px-5 py-3">Estado</th>
                <th class="px-5 py-3">Valor Estimado</th>
                <th class="px-5 py-3">Responsable</th>
                <th class="px-5 py-3">Cierre Esperado</th>
                <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($leads)): ?>
            <tr>
                <td colspan="7" class="px-5 py-8 text-center text-gray-500">No se encontraron oportunidades.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($leads as $lead): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                    <a href="/leads/<?= $lead->id ?>" class="font-medium text-indigo-600 hover:text-indigo-800">
                        <?= htmlspecialchars($lead->title) ?>
                    </a>
                </td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($lead->company_name) ?></td>
                <td class="px-5 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        <?= $lead->status === 'new' ? 'bg-blue-100 text-blue-700' : '' ?>
                        <?= $lead->status === 'contacted' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                        <?= $lead->status === 'qualified' ? 'bg-indigo-100 text-indigo-700' : '' ?>
                        <?= $lead->status === 'proposal' ? 'bg-purple-100 text-purple-700' : '' ?>
                        <?= $lead->status === 'won' ? 'bg-green-100 text-green-700' : '' ?>
                        <?= $lead->status === 'lost' ? 'bg-red-100 text-red-700' : '' ?>">
                        <?= $statuses[$lead->status] ?? $lead->status ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-sm font-medium">$<?= number_format((float) $lead->estimated_value, 2) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($lead->assigned_name ?? '-') ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= $lead->expected_close_date ? date('d/m/Y', strtotime($lead->expected_close_date)) : '-' ?></td>
                <td class="px-5 py-3 text-right">
                    <a href="/leads/<?= $lead->id ?>/edit" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Editar</a>
                    <form method="POST" action="/leads/<?= $lead->id ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar esta oportunidad?')">
                        <?= $csrf_field ?>
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?></p>
        <div class="flex gap-1">
            <?php if ($pagination['has_prev']): ?>
            <a href="?page=<?= $pagination['prev_page'] ?>&status=<?= $currentStatus ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Anterior</a>
            <?php endif; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $pagination['next_page'] ?>&status=<?= $currentStatus ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Siguiente</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
