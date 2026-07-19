<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/leads" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($lead->title) ?></h2>
        </div>
        <a href="/leads/<?= $lead->id ?>/edit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">Editar</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
            <dt class="text-sm text-gray-500">Cliente</dt>
            <dd class="font-medium mt-1"><?= htmlspecialchars($lead->company_name) ?></dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Estado</dt>
            <dd class="mt-1">
                <span class="px-2 py-1 text-xs font-medium rounded-full 
                    <?= $lead->status === 'new' ? 'bg-blue-100 text-blue-700' : '' ?>
                    <?= $lead->status === 'contacted' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                    <?= $lead->status === 'qualified' ? 'bg-indigo-100 text-indigo-700' : '' ?>
                    <?= $lead->status === 'proposal' ? 'bg-purple-100 text-purple-700' : '' ?>
                    <?= $lead->status === 'won' ? 'bg-green-100 text-green-700' : '' ?>
                    <?= $lead->status === 'lost' ? 'bg-red-100 text-red-700' : '' ?>">
                    <?= $lead->status === 'new' ? 'Nuevo' : ($lead->status === 'contacted' ? 'Contactado' : ($lead->status === 'qualified' ? 'Calificado' : ($lead->status === 'proposal' ? 'Propuesta' : ($lead->status === 'won' ? 'Ganado' : 'Perdido')))) ?>
                </span>
            </dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Valor Estimado</dt>
            <dd class="font-medium mt-1 text-lg">$<?= number_format((float) $lead->estimated_value, 2) ?></dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Responsable</dt>
            <dd class="font-medium mt-1"><?= htmlspecialchars($lead->assigned_name ?? 'Sin asignar') ?></dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Origen</dt>
            <dd class="font-medium mt-1"><?= htmlspecialchars($lead->source ?? '-') ?></dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Cierre Esperado</dt>
            <dd class="font-medium mt-1"><?= $lead->expected_close_date ? date('d/m/Y', strtotime($lead->expected_close_date)) : '-' ?></dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">Creado</dt>
            <dd class="font-medium mt-1"><?= date('d/m/Y H:i', strtotime($lead->created_at)) ?></dd>
        </div>
    </div>
</div>
