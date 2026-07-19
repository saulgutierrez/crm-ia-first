<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="/clients/<?= $clientId ?>" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Contactos</h2>
    </div>
    <a href="/clients/<?= $clientId ?>/contacts/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
        + Nuevo Contacto
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                <th class="px-5 py-3">Nombre</th>
                <th class="px-5 py-3">Cargo</th>
                <th class="px-5 py-3">Email</th>
                <th class="px-5 py-3">Teléfono</th>
                <th class="px-5 py-3">Principal</th>
                <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (empty($contacts)): ?>
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-500">No hay contactos registrados para este cliente.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($contacts as $contact): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3 font-medium"><?= htmlspecialchars($contact->full_name) ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($contact->position ?? '-') ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($contact->email ?? '-') ?></td>
                <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($contact->phone ?? '-') ?></td>
                <td class="px-5 py-3">
                    <?php if ($contact->is_primary): ?>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">Principal</span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="/contacts/<?= $contact->id ?>/edit" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Editar</a>
                    <form method="POST" action="/contacts/<?= $contact->id ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar este contacto?')">
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
