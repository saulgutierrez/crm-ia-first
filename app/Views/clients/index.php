<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Clientes</h2>
    <div class="flex gap-2">
        <a href="/export/clients<?= $search ? '?search=' . urlencode($search) : '' ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Exportar PDF
        </a>
        <a href="/clients/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
            + Nuevo Cliente
        </a>
    </div>
</div>

<!-- Search -->
<form method="GET" action="/clients" class="mb-6">
    <div class="flex gap-2">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nombre, email o industria..."
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Buscar</button>
        <?php if ($search): ?>
        <a href="/clients" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<!-- Clients Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                    <th class="px-5 py-3">Empresa</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Teléfono</th>
                    <th class="px-5 py-3">Industria</th>
                    <th class="px-5 py-3">Responsable</th>
                    <th class="px-5 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">No se encontraron clientes.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($clients as $client): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <a href="/clients/<?= $client->id ?>" class="font-medium text-indigo-600 hover:text-indigo-800">
                            <?= htmlspecialchars($client->company_name) ?>
                        </a>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($client->email ?? '-') ?></td>
                    <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($client->phone ?? '-') ?></td>
                    <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($client->industry ?? '-') ?></td>
                    <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($client->owner_name ?? '-') ?></td>
                    <td class="px-5 py-3 text-right">
                        <a href="/clients/<?= $client->id ?>/edit" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Editar</a>
                        <form method="POST" action="/clients/<?= $client->id ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar este cliente?')">
                            <?= $csrf_field ?>
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">Mostrando página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?> (<?= $pagination['total'] ?> registros)</p>
        <div class="flex gap-1">
            <?php if ($pagination['has_prev']): ?>
            <a href="?page=<?= $pagination['prev_page'] ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Anterior</a>
            <?php endif; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $pagination['next_page'] ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Siguiente</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
