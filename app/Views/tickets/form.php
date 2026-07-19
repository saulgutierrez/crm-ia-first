<div class="mb-6">
    <div class="flex items-center gap-3">
        <a href="/tickets" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-800"><?= $ticket ? 'Editar Ticket' : 'Nuevo Ticket' ?></h2>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form method="POST" action="<?= $ticket ? '/tickets/' . $ticket->id : '/tickets' ?>" class="space-y-5">
        <?= $csrf_field ?>

        <div>
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Asunto *</label>
            <input type="text" id="subject" name="subject" required
                   value="<?= htmlspecialchars($ticket->subject ?? '') ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($ticket->description ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                <select id="client_id" name="client_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccionar cliente...</option>
                    <?php foreach ($clients as $client): ?>
                    <option value="<?= $client->id ?>" <?= ($ticket->client_id ?? '') == $client->id ? 'selected' : '' ?>><?= htmlspecialchars($client->company_name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                <select id="priority" name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="low" <?= ($ticket->priority ?? '') === 'low' ? 'selected' : '' ?>>Baja</option>
                    <option value="medium" <?= ($ticket->priority ?? '') === 'medium' || empty($ticket) ? 'selected' : '' ?>>Media</option>
                    <option value="high" <?= ($ticket->priority ?? '') === 'high' ? 'selected' : '' ?>>Alta</option>
                    <option value="urgent" <?= ($ticket->priority ?? '') === 'urgent' ? 'selected' : '' ?>>Urgente</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Asignar a</label>
                <select id="assigned_to" name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sin asignar</option>
                    <?php foreach ($agents as $agent): ?>
                    <option value="<?= $agent->id ?>" <?= ($ticket->assigned_to ?? '') == $agent->id ? 'selected' : '' ?>><?= htmlspecialchars($agent->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($ticket): ?>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="open" <?= ($ticket->status ?? '') === 'open' ? 'selected' : '' ?>>Abierto</option>
                    <option value="in_progress" <?= ($ticket->status ?? '') === 'in_progress' ? 'selected' : '' ?>>En Progreso</option>
                    <option value="resolved" <?= ($ticket->status ?? '') === 'resolved' ? 'selected' : '' ?>>Resuelto</option>
                    <option value="closed" <?= ($ticket->status ?? '') === 'closed' ? 'selected' : '' ?>>Cerrado</option>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                <?= $ticket ? 'Actualizar' : 'Guardar' ?>
            </button>
            <a href="/tickets" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</a>
        </div>
    </form>
</div>
