<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="/users" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= $user ? 'Editar Usuario' : 'Nuevo Usuario' ?></h2>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="<?= $user ? '/users/' . $user->id : '/users' ?>" class="space-y-5">
        <?= $csrf_field ?>

        <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-lg border border-indigo-100 mb-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold text-white bg-indigo-500">
                <?= $user ? strtoupper(substr($user->name, 0, 1)) : '?' ?>
            </div>
            <div>
                <p class="text-sm font-medium text-indigo-900">
                    <?= $user ? 'Editando: ' . htmlspecialchars($user->name) : 'Creando nuevo usuario' ?>
                </p>
                <p class="text-xs text-indigo-600">
                    <?= $user ? 'Rol actual: ' . ($user->role === 'admin' ? 'Administrador' : 'Agente') : 'Complete los campos para crear un usuario' ?>
                </p>
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
            <input type="text" id="name" name="name" required
                   value="<?= htmlspecialchars($user->name ?? '') ?>"
                   placeholder="Ej: Juan Pérez"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($user->email ?? '') ?>"
                   placeholder="Ej: usuario@ejemplo.com"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Contraseña <?= $user ? '(dejar vacío para mantener la actual)' : '*' ?>
            </label>
            <input type="password" id="password" name="password"
                   <?= $user ? '' : 'required' ?>
                   placeholder="<?= $user ? 'Nueva contraseña (opcional)' : 'Contraseña segura' ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres. Se usará hash Argon2id.</p>
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="agent" <?= ($user->role ?? 'agent') === 'agent' ? 'selected' : '' ?>>Agente</option>
                <option value="admin" <?= ($user->role ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Los administradores tienen acceso completo a todas las funcionalidades del sistema.</p>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <?= $user ? 'Actualizar Usuario' : 'Crear Usuario' ?>
            </button>
            <a href="/users" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
        </div>
    </form>
</div>
