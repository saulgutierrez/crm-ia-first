<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/interactions" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($interaction->subject) ?></h2>
        </div>
        <form method="POST" action="/interactions/<?= $interaction->id ?>/delete" class="inline" onsubmit="return confirm('¿Eliminar esta interacción?')">
            <?= $csrf_field ?>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">Eliminar</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full 
                    <?= $interaction->type === 'call' ? 'bg-green-100 text-green-700' : '' ?>
                    <?= $interaction->type === 'email' ? 'bg-blue-100 text-blue-700' : '' ?>
                    <?= $interaction->type === 'meeting' ? 'bg-purple-100 text-purple-700' : '' ?>
                    <?= $interaction->type === 'note' ? 'bg-gray-100 text-gray-700' : '' ?>">
                    <?= $interaction->type === 'call' ? '📞 Llamada' : ($interaction->type === 'email' ? '✉️ Correo' : ($interaction->type === 'meeting' ? '🤝 Reunión' : '📝 Nota')) ?>
                </span>
                <span class="text-sm text-gray-400">•</span>
                <span class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($interaction->created_at)) ?></span>
            </div>

            <h3 class="font-semibold text-gray-800 mb-3">Descripción</h3>
            <?php if ($interaction->description): ?>
                <div class="bg-gray-50 rounded-lg p-4 text-gray-700 whitespace-pre-wrap text-sm leading-relaxed">
                    <?= nl2br(htmlspecialchars($interaction->description)) ?>
                </div>
            <?php else: ?>
                <p class="text-gray-400 italic">Sin descripción</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Detalles</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-500">Cliente</dt>
                    <dd class="font-medium">
                        <a href="/clients/<?= $interaction->client_id ?>" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                            <?= htmlspecialchars($interaction->company_name) ?>
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Tipo</dt>
                    <dd class="mt-1">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            <?= $interaction->type === 'call' ? 'bg-green-100 text-green-700' : '' ?>
                            <?= $interaction->type === 'email' ? 'bg-blue-100 text-blue-700' : '' ?>
                            <?= $interaction->type === 'meeting' ? 'bg-purple-100 text-purple-700' : '' ?>
                            <?= $interaction->type === 'note' ? 'bg-gray-100 text-gray-700' : '' ?>">
                            <?= $interaction->type === 'call' ? 'Llamada' : ($interaction->type === 'email' ? 'Correo' : ($interaction->type === 'meeting' ? 'Reunión' : 'Nota')) ?>
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Registrado por</dt>
                    <dd class="font-medium"><?= htmlspecialchars($interaction->user_name ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Creado</dt>
                    <dd class="font-medium"><?= date('d/m/Y H:i', strtotime($interaction->created_at)) ?></dd>
                </div>
                <?php if ($interaction->created_at !== $interaction->updated_at): ?>
                <div>
                    <dt class="text-sm text-gray-500">Actualizado</dt>
                    <dd class="font-medium"><?= date('d/m/Y H:i', strtotime($interaction->updated_at)) ?></dd>
                </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</div>
