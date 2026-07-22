<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Interacciones</h2>
    <a href="/interactions/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
        + Nueva Interacción
    </a>
</div>

<!-- Type Filter -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="/interactions" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= !$currentType ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Todas</a>
    <a href="/interactions?type=call" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentType === 'call' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Llamadas</a>
    <a href="/interactions?type=email" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentType === 'email' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Correos</a>
    <a href="/interactions?type=meeting" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentType === 'meeting' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Reuniones</a>
    <a href="/interactions?type=note" class="px-3 py-1.5 text-sm rounded-lg border transition-colors <?= $currentType === 'note' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' ?>">Notas</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                <th class="px-5 py-3">Tipo</th>
                <th class="px-5 py-3">Asunto</th>
                <th class="px-5 py-3">Cliente</th>
                <th class="px-5 py-3">Usuario</th>
                <th class="px-5 py-3">Fecha</th>
                <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($interactions)): ?>
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-500">No hay interacciones registradas.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($interactions as $interaction): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        <?= $interaction->type === 'call' ? 'bg-green-100 text-green-700' : '' ?>
                        <?= $interaction->type === 'email' ? 'bg-blue-100 text-blue-700' : '' ?>
                        <?= $interaction->type === 'meeting' ? 'bg-purple-100 text-purple-700' : '' ?>
                        <?= $interaction->type === 'note' ? 'bg-gray-100 text-gray-700' : '' ?>">
                        <?= $interaction->type === 'call' ? 'Llamada' : ($interaction->type === 'email' ? 'Correo' : ($interaction->type === 'meeting' ? 'Reunión' : 'Nota')) ?>
                    </span>
                </td>
                <td class="px-5 py-3">
                    <a href="/interactions/<?= $interaction->id ?>" class="font-medium text-indigo-600 hover:text-indigo-800">
                        <?= htmlspecialchars($interaction->subject) ?>
                    </a>
                </td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($interaction->company_name) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($interaction->user_name ?? '-') ?></td>
                <td class="px-5 py-3 text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($interaction->created_at)) ?></td>
                <td class="px-5 py-3 text-right">
                    <a href="/interactions/<?= $interaction->id ?>/edit" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Editar</a>
                    <form method="POST" action="/interactions/<?= $interaction->id ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar esta interacción?')">
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
            <a href="?page=<?= $pagination['prev_page'] ?>&type=<?= $currentType ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Anterior</a>
            <?php endif; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $pagination['next_page'] ?>&type=<?= $currentType ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Siguiente</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
