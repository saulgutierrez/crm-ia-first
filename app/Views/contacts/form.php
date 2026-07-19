<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="/clients/<?= $clientId ?>/contacts" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= $contact ? 'Editar Contacto' : 'Nuevo Contacto' ?></h2>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="<?= $contact ? '/contacts/' . $contact->id : '/clients/' . $clientId . '/contacts' ?>" class="space-y-5">
        <?= $csrf_field ?>

        <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
            <input type="text" id="full_name" name="full_name" required
                   value="<?= htmlspecialchars($contact->full_name ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
            <input type="text" id="position" name="position"
                   value="<?= htmlspecialchars($contact->position ?? '') ?>"
                   placeholder="Ej: CEO, Gerente de Ventas..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($contact->email ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" id="phone" name="phone"
                       value="<?= htmlspecialchars($contact->phone ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_primary" name="is_primary" value="1"
                   <?= isset($contact->is_primary) && $contact->is_primary ? 'checked' : '' ?>
                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="is_primary" class="text-sm text-gray-700">Contacto principal</label>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <?= $contact ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="/clients/<?= $clientId ?>/contacts" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
        </div>
    </form>
</div>
