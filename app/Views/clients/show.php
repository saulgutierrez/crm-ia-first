<div class="mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/clients" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($client->company_name) ?></h2>
        </div>
        <div class="flex gap-2">
            <a href="/clients/<?= $client->id ?>/edit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">Editar</a>
            <a href="/clients/<?= $client->id ?>/contacts" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">Contactos</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <!-- Client Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Información General</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm text-gray-500">Email</dt>
                    <dd class="font-medium"><?= htmlspecialchars($client->email ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Teléfono</dt>
                    <dd class="font-medium"><?= htmlspecialchars($client->phone ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Industria</dt>
                    <dd class="font-medium"><?= htmlspecialchars($client->industry ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Responsable</dt>
                    <dd class="font-medium"><?= htmlspecialchars($client->owner_name ?? '-') ?></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Creado</dt>
                    <dd class="font-medium"><?= date('d/m/Y', strtotime($client->created_at)) ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
        <h3 class="font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
        <div class="space-y-2">
            <a href="/clients/<?= $client->id ?>/contacts/create" class="block text-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors text-sm">Añadir Contacto</a>
            <a href="/interactions/create" class="block text-center px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm">Registrar Interacción</a>
            <a href="/leads/create" class="block text-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm">Crear Oportunidad</a>
        </div>
    </div>
</div>
