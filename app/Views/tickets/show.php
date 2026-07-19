<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/tickets" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($ticket->subject) ?></h2>
        </div>
        <a href="/tickets/<?= $ticket->id ?>/edit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">Editar</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Descripción</h3>
            <p class="text-gray-700 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($ticket->description ?? 'Sin descripción')) ?></p>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Detalles</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-500">Cliente</dt>
                    <dd class="font-medium"><?= htmlspecialchars($ticket->company_name) ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Prioridad</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $ticket->priority === 'urgent' ? 'bg-red-100 text-red-700' : '' ?>
                            <?= $ticket->priority === 'high' ? 'bg-orange-100 text-orange-700' : '' ?>
                            <?= $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                            <?= $ticket->priority === 'low' ? 'bg-green-100 text-green-700' : '' ?>">
                            <?= $ticket->priority === 'urgent' ? 'Urgente' : ($ticket->priority === 'high' ? 'Alta' : ($ticket->priority === 'medium' ? 'Media' : 'Baja')) ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Estado</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $ticket->status === 'open' ? 'bg-blue-100 text-blue-700' : '' ?>
                            <?= $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                            <?= $ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : '' ?>
                            <?= $ticket->status === 'closed' ? 'bg-gray-100 text-gray-700' : '' ?>">
                            <?= $ticket->status === 'open' ? 'Abierto' : ($ticket->status === 'in_progress' ? 'En Progreso' : ($ticket->status === 'resolved' ? 'Resuelto' : 'Cerrado')) ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Asignado a</dt>
                    <dd class="font-medium"><?= htmlspecialchars($ticket->assigned_name ?? 'Sin asignar') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Creado por</dt>
                    <dd class="font-medium"><?= htmlspecialchars($ticket->creator_name ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Creado</dt>
                    <dd class="font-medium"><?= date('d/m/Y H:i', strtotime($ticket->created_at)) ?></dd>
                </div>
            </dl>
        </div>
    </div>
</div>
