<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="/clients" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= $client ? 'Editar Cliente' : 'Nuevo Cliente' ?></h2>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="<?= $client ? '/clients/' . $client->id : '/clients' ?>" class="space-y-5">
        <?= $csrf_field ?>

        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa *</label>
            <input type="text" id="company_name" name="company_name" required
                   value="<?= htmlspecialchars($client->company_name ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($client->email ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" id="phone" name="phone"
                       value="<?= htmlspecialchars($client->phone ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div>
            <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industria</label>
            <input type="text" id="industry" name="industry"
                   value="<?= htmlspecialchars($client->industry ?? '') ?>"
                   placeholder="Ej: Tecnología, Salud, Finanzas..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <?= $client ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="/clients" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
        </div>
    </form>
</div>
