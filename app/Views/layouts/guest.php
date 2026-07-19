<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'CRM Profesional') ?> - CRM Profesional</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-indigo-700 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <?= $content ?>
    </div>
</body>
</html>
