<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Usuarios del Sistema</h2>
    <a href="/users/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
        + Nuevo Usuario
    </a>
</div>

<!-- Summary cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm text-gray-500">Total Usuarios</p>
        <p class="text-2xl font-bold text-gray-800"><?= $pagination['total'] ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm text-gray-500">Agentes</p>
        <p class="text-2xl font-bold text-indigo-600"><?= $agentsCount ?></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-sm text-gray-500">Administradores</p>
        <p class="text-2xl font-bold text-purple-600"><?= $pagination['total'] - $agentsCount ?></p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left text-sm font-medium text-gray-500">
                    <th class="px-5 py-3">Nombre</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Rol</th>
                    <th class="px-5 py-3">Estado</th>
                    <th class="px-5 py-3">Creado</th>
                    <th class="px-5 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">No se encontraron usuarios.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium text-white
                                <?= $user->role === 'admin' ? 'bg-purple-500' : 'bg-indigo-500' ?>">
                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                            </div>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($user->name) ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600"><?= htmlspecialchars($user->email) ?></td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' ?>">
                            <?= $user->role === 'admin' ? 'Administrador' : 'Agente' ?>
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $user->is_active ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500"><?= date('d/m/Y', strtotime($user->created_at)) ?></td>
                    <td class="px-5 py-3 text-right">
                        <a href="/users/<?= $user->id ?>/edit" class="text-sm text-indigo-600 hover:text-indigo-800 mr-3">Editar</a>
                        <form method="POST" action="/users/<?= $user->id ?>/toggle-status" class="inline">
                            <?= $csrf_field ?>
                            <button type="submit" class="text-sm <?= $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' ?>"
                                onclick="return confirm('¿<?= $user->is_active ? 'Desactivar' : 'Activar' ?> este usuario?')">
                                <?= $user->is_active ? 'Desactivar' : 'Activar' ?>
                            </button>
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
        <p class="text-sm text-gray-500">Página <?= $pagination['current_page'] ?> de <?= $pagination['total_pages'] ?> (<?= $pagination['total'] ?> registros)</p>
        <div class="flex gap-1">
            <?php if ($pagination['has_prev']): ?>
            <a href="?page=<?= $pagination['prev_page'] ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Anterior</a>
            <?php endif; ?>
            <?php if ($pagination['has_next']): ?>
            <a href="?page=<?= $pagination['next_page'] ?>" class="px-3 py-1 text-sm border rounded hover:bg-gray-50">Siguiente</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
