<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="/interactions" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Nueva Interacción</h2>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="/interactions" class="space-y-5">
        <?= $csrf_field ?>

        <div>
            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
            <select id="client_id" name="client_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Seleccionar cliente...</option>
                <?php foreach ($clients as $client): ?>
                <option value="<?= $client->id ?>"><?= htmlspecialchars($client->company_name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Interacción</label>
            <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="call">Llamada</option>
                <option value="email">Correo Electrónico</option>
                <option value="meeting">Reunión</option>
                <option value="note">Nota</option>
            </select>
        </div>

        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Asunto *</label>
            <input type="text" id="subject" name="subject" required
                   placeholder="Breve descripción de la interacción"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Detalles de la interacción..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                Guardar
            </button>
            <a href="/interactions" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
        </div>
    </form>
</div>
