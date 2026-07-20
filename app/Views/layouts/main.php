<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CRM Profesional') ?> - CRM Profesional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-indigo-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-4 border-b border-indigo-800">
                <h1 class="text-xl font-bold tracking-tight">CRM Profesional</h1>
                <p class="text-indigo-300 text-sm mt-1">Panel de Control</p>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-800 transition-colors <?= ($_SERVER['REQUEST_URI'] ?? '/') === '/' ? 'bg-indigo-800' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="/clients" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-800 transition-colors <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/clients') ? 'bg-indigo-800' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Clientes
                </a>
                <a href="/leads" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-800 transition-colors <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/leads') ? 'bg-indigo-800' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Oportunidades
                </a>
                <a href="/interactions" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-800 transition-colors <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/interactions') ? 'bg-indigo-800' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Interacciones
                </a>
                <a href="/tickets" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-800 transition-colors <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/tickets') ? 'bg-indigo-800' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    Tickets
                </a>
                <?php if (($session['user_role'] ?? '') === 'admin'): ?>
                <hr class="border-indigo-800 my-2">
                <a href="/users" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-800 transition-colors <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/users') ? 'bg-indigo-800' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    Usuarios
                    <span class="ml-auto px-2 py-0.5 text-xs bg-purple-500 text-white rounded-full">Admin</span>
                </a>
                <?php endif; ?>
            </nav>
            <div class="p-4 border-t border-indigo-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-sm font-medium">
                        <?= strtoupper(substr($session['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= htmlspecialchars($session['user_name'] ?? 'Usuario') ?></p>
                        <p class="text-xs text-indigo-300 capitalize"><?= htmlspecialchars($session['user_role'] ?? '') ?></p>
                    </div>
                    <a href="/logout" class="text-indigo-300 hover:text-white transition-colors" title="Cerrar Sesión">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-indigo-900 text-white z-50 p-4 flex items-center justify-between">
            <h1 class="text-lg font-bold">CRM Profesional</h1>
            <button id="mobile-menu-btn" class="p-2" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden fixed inset-0 bg-indigo-900 text-white z-40 pt-16 hidden">
            <nav class="p-4 space-y-1">
                <a href="/" class="block px-3 py-2.5 rounded-lg hover:bg-indigo-800">Dashboard</a>
                <a href="/clients" class="block px-3 py-2.5 rounded-lg hover:bg-indigo-800">Clientes</a>
                <a href="/leads" class="block px-3 py-2.5 rounded-lg hover:bg-indigo-800">Oportunidades</a>
                <a href="/interactions" class="block px-3 py-2.5 rounded-lg hover:bg-indigo-800">Interacciones</a>
                <a href="/tickets" class="block px-3 py-2.5 rounded-lg hover:bg-indigo-800">Tickets</a>
                <hr class="border-indigo-800 my-4">
                <a href="/logout" class="block px-3 py-2.5 rounded-lg hover:bg-indigo-800">Cerrar Sesión</a>
            </nav>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto pt-16 md:pt-0">
            <div class="p-6 max-w-7xl mx-auto">
                <!-- Flash Messages -->
                <?php $flash = \App\Helpers\Session::getFlash('success'); if ($flash): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= htmlspecialchars($flash) ?>
                </div>
                <?php endif; ?>
                <?php $flash = \App\Helpers\Session::getFlash('error'); if ($flash): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= htmlspecialchars($flash) ?>
                </div>
                <?php endif; ?>

                <?= $content ?>
            </div>
        </main>
    </div>
</body>
</html>
