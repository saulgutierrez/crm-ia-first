<div class="bg-white rounded-2xl shadow-xl p-8">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">CRM Profesional</h1>
        <p class="text-gray-500 mt-2">Inicia sesión para continuar</p>
    </div>

    <?php $error = \App\Helpers\Session::getFlash('error'); if ($error): ?>
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="/login" class="space-y-5">
        <?= $csrf_field ?>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
            <input type="email" id="email" name="email" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                   placeholder="tu@correo.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" id="password" name="password" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                   placeholder="••••••••">
        </div>

        <?php if (!empty($_ENV['RECAPTCHA_SITE_KEY'])): ?>
        <input type="hidden" name="g-recaptcha-response" id="recaptchaResponse">
        <?php endif; ?>

        <button type="submit"
                class="w-full bg-indigo-600 text-white py-2.5 rounded-lg font-medium hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all">
            Iniciar Sesión
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-400">
        Sistema CRM Profesional v1.0
    </div>
</div>
