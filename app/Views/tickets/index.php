<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Tickets de Soporte</h2>
    <a href="/tickets/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
        + Nuevo Ticket
    </a>
</div>

<!-- Status Filter -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="/tickets" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= !$currentStatus ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Todos</a>
    <a href="/tickets?status=open" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentStatus === 'open' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Abiertos</a>
    <a href="/tickets?status=in_progress" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentStatus === 'in_progress' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">En Progreso</a>
    <a href="/tickets?status=resolved" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentStatus === 'resolved' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Resueltos</a>
    <a href="/tickets?status=closed" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentStatus === 'closed' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Cerrados</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                <th class="px-5 py-3">Prioridad</th>
                <th class="px-5 py-3">Asunto</th>
                <th class="px-5 py-3">Cliente</th>
                <th class="px-5 py-3">Estado</th>
                <th class="px-5 py-3">Asignado</th>
                <th class="px-5 py-3">Creado</th>
                <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($tickets)): ?>
            <tr>
                <td colspan="7" class="px-5 py-8 text-center text-gray-500">No se encontraron tickets.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        <?= $ticket->priority === 'urgent' ? 'bg-red-100 text-red-700' : '' ?>
                        <?= $ticket->priority === 'high' ? 'bg-orange-100 text-orange-700' : '' ?>
                        <?= $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                        <?= $ticket->priority === 'low' ? 'bg-green-100 text-green-700' : '' ?>">
                        <?= $ticket->priority === 'urgent' ? 'Urgente' : ($ticket->priority === 'high' ? 'Alta' : ($ticket->priority === 'medium' ? 'Media' : 'Baja')) ?>
                    </span>
                </td>
                <td class="px-5 py-3">
                    <a href="/tickets/<?= $ticket->id ?>" class="font-medium text-indigo-600 hover:text-indigo-800">
                        <?= htmlspecialchars($ticket->subject) ?>
                    </a>
                </td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($ticket->company_name) ?></td>
                <td class="px-5 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        <?= $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : '' ?>
                        <?= $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                        <?= $ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : '' ?>
                        <?= $ticket->status === 'closed' ? 'bg-gray-100 text-gray-700' : '' ?>">
                        <?= $ticket->status === 'open' ? 'Abierto' : ($ticket->status === 'in_progress' ? 'En Progreso' : ($ticket->status === 'resolved' ? 'Resuelto' : 'Cerrado')) ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($ticket->assigned_name ?? '-') ?></td>
                <td class="px-5 py-3 text-sm text-gray-500"><?= date('d/m/Y', strtotime($ticket->created_at)) ?></td>
                <td class="px-5 py-3 text-right">
                    <a href="/tickets/<?= $ticket->id ?>/edit" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Editar</a>
                    <form method="POST" action="/tickets/<?= $ticket->id ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar este ticket?')">
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
