<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="/leads" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= $lead ? 'Editar Oportunidad' : 'Nueva Oportunidad' ?></h2>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="<?= $lead ? '/leads/' . $lead->id : '/leads' ?>" class="space-y-5">
        <?= $csrf_field ?>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
            <input type="text" id="title" name="title" required
                   value="<?= htmlspecialchars($lead->title ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select id="client_id" name="client_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccionar cliente...</option>
                    <?php foreach ($clients as $client): ?>
                    <option value="<?= $client->id ?>" <?= ($lead->client_id ?? '') == $client->id ? 'selected' : '' ?>><?= htmlspecialchars($client->company_name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Asignar a</label>
                <select id="assigned_to" name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sin asignar</option>
                    <?php foreach ($agents as $agent): ?>
                    <option value="<?= $agent->id ?>" <?= ($lead->assigned_to ?? '') == $agent->id ? 'selected' : '' ?>><?= htmlspecialchars($agent->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="new" <?= ($lead->status ?? '') === 'new' ? 'selected' : '' ?>>Nuevo</option>
                    <option value="contacted" <?= ($lead->status ?? '') === 'contacted' ? 'selected' : '' ?>>Contactado</option>
                    <option value="qualified" <?= ($lead->status ?? '') === 'qualified' ? 'selected' : '' ?>>Calificado</option>
                    <option value="proposal" <?= ($lead->status ?? '') === 'proposal' ? 'selected' : '' ?>>Propuesta</option>
                    <option value="won" <?= ($lead->status ?? '') === 'won' ? 'selected' : '' ?>>Ganado</option>
                    <option value="lost" <?= ($lead->status ?? '') === 'lost' ? 'selected' : '' ?>>Perdido</option>
                </select>
            </div>
            <div>
                <label for="estimated_value" class="block text-sm font-medium text-gray-700 mb-1">Valor Estimado</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                    <input type="number" step="0.01" id="estimated_value" name="estimated_value"
                           value="<?= htmlspecialchars((string) ($lead->estimated_value ?? '0')) ?>"
                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Origen</label>
                <input type="text" id="source" name="source"
                       value="<?= htmlspecialchars($lead->source ?? '') ?>"
                       placeholder="Ej: Web, Referido, Redes Sociales..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Cierre Esperada</label>
                <input type="date" id="expected_close_date" name="expected_close_date"
                       value="<?= htmlspecialchars($lead->expected_close_date ?? '') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <?= $lead ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="/leads" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
        </div>
    </form>
</div>
