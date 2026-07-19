# Guías Prácticas - CRM Profesional

## 📋 Índice

1. [Cómo agregar un nuevo módulo (ej: Productos)](#1-cómo-agregar-un-nuevo-módulo)
2. [Cómo ejecutar migraciones y seeds](#2-cómo-ejecutar-migraciones-y-seeds)
3. [Cómo agregar un nuevo endpoint de API](#3-cómo-agregar-un-nuevo-endpoint-de-api)
4. [Cómo probar el sistema manualmente](#4-cómo-probar-el-sistema-manualmente)
5. [Cómo depurar errores](#5-cómo-depurar-errores)
6. [Cómo extender el dashboard con nuevos gráficos](#6-cómo-extender-el-dashboard)
7. [Cómo implementar reCAPTCHA correctamente](#7-cómo-implementar-recaptcha)
8. [Cómo configurar el entorno de producción](#8-cómo-configurar-el-entorno-de-producción)

---

## 1. Cómo Agregar un Nuevo Módulo (Ej: Productos)

Agregar un módulo completo requiere crear 7+ archivos. Sigue estos pasos:

### Paso 1: Migración SQL

`database/migrations/008_create_products_table.sql`:
```sql
CREATE TABLE IF NOT EXISTS `products` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_products_client` (`client_id`),
    CONSTRAINT `fk_products_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Ejecutar: `php database/migrate.php`

### Paso 2: Repository

`app/Repositories/ProductRepository.php`:
```php
<?php
declare(strict_types=1);

namespace App\Repositories;

class ProductRepository extends BaseRepository
{
    protected string $table = 'products';

    public function findAllByClient(int $clientId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE client_id = :client_id AND is_active = 1 ORDER BY name ASC"
        );
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }
}
```

### Paso 3: Service

`app/Services/ProductService.php`:
```php
<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Exceptions\HttpException;

class ProductService extends BaseService
{
    private ProductRepository $productRepo;

    public function __construct()
    {
        $this->productRepo = new ProductRepository();
    }

    public function listByClient(int $clientId): array
    {
        return $this->productRepo->findAllByClient($clientId);
    }

    public function create(array $data): int
    {
        $this->requireField($data, 'name', 'nombre del producto');
        return (int) $this->productRepo->create($data);
    }

    public function delete(int $id): bool
    {
        return $this->productRepo->delete($id);
    }
}
```

### Paso 4: Controller

`app/Controllers/ProductController.php`:
```php
<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductService;
use App\Helpers\Session;
use App\Exceptions\HttpException;

class ProductController extends BaseController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function index(array $vars): void
    {
        $products = $this->productService->listByClient((int) $vars['clientId']);
        $this->render('products/index', [
            'title' => 'Productos',
            'clientId' => (int) $vars['clientId'],
            'products' => $products,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $this->productService->create($_POST);
            Session::flash('success', 'Producto creado.');
            $this->redirectBack();
        } catch (HttpException $e) {
            Session::flash('error', $e->getMessage());
            $this->redirectBack();
        }
    }
}
```

### Paso 5: Rutas

En `routes/web.php`:
```php
$r->addRoute('GET', '/clients/{clientId:\d+}/products', [ProductController::class, 'index', [AuthMiddleware::class]]);
$r->addRoute('POST', '/products', [ProductController::class, 'store', [AuthMiddleware::class]]);
$r->addRoute('POST', '/products/{id:\d+}/delete', [ProductController::class, 'destroy', [AuthMiddleware::class]]);
```

### Paso 6: Vista

`app/Views/products/index.php` con la misma estructura de tabla que las vistas existentes.

### Paso 7: Actualizar DashboardController

Agregar consulta de productos recientes y pasarlo a la vista.

---

## 2. Cómo Ejecutar Migraciones y Seeds

### Migraciones

```bash
# Ejecutar todas las migraciones
php database/migrate.php

# Salida esperada:
# === CRM Profesional - Migration Runner ===
# ✓ Database 'crm_profesional' ready
# Running migration: 001_create_users_table.sql... ✓ done
# Running migration: 002_create_clients_table.sql... ✓ done
# ...
# ✓ All migrations executed successfully
# Tables created: users, clients, contacts, leads, interactions, tickets, login_attempts
```

### Seeds

```bash
# Poblar con datos de prueba (genera hashes Argon2id reales)
php database/seed.php

# Salida esperada:
# === CRM Profesional - Seed Runner ===
# ✓ Connected to database 'crm_profesional'
# Generating Argon2id password hashes...
#   ✓ Admin password hash generated
#   ✓ Agent password hash generated
#   ✓ Admin user created (ID: 1)
#   ✓ Agent user created (ID: 2)
#   ✓ Sample client created (x2)
#   ✓ Sample lead created (x2)
#   ✓ Sample interaction created (x2)
#   ✓ Sample ticket created (x2)
# ========================================
#   ✅ Seed completed successfully!
# ========================================
#   Admin login: admin@crm.com / admin123
#   Agent login: agent@crm.com / agent123
# ========================================
```

### Reset Completo

```bash
# Conectar a MySQL
mysql -u root -p

# Dentro de MySQL:
DROP DATABASE IF EXISTS crm_profesional;
CREATE DATABASE crm_profesional CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Luego:
php database/migrate.php
php database/seed.php
```

---

## 3. Cómo Agregar un Nuevo Endpoint de API

### Ejemplo: GET /api/v1/products/{id}

### Paso 1: Agregar ruta en `routes/api.php`

```php
$r->addRoute('GET', '/api/v1/products/{id:\d+}', [ProductApiController::class, 'show', [AuthMiddleware::class]]);
$r->addRoute('POST', '/api/v1/products', [ProductApiController::class, 'store', [AuthMiddleware::class, CsrfMiddleware::class]]);
```

### Paso 2: Crear controlador API

`app/Controllers/Api/ProductApiController.php`:
```php
<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\ProductService;
use App\Exceptions\HttpException;

class ProductApiController extends BaseController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function show(array $vars): void
    {
        $product = $this->productService->find((int) $vars['id']);
        if (!$product) {
            $this->error('Producto no encontrado.', 404);
        }
        $this->json($product);
    }

    public function store(): void
    {
        try {
            $data = $this->getJsonBody();
            $id = $this->productService->create($data);
            $product = $this->productService->find($id);
            $this->json($product, 201, 'Producto creado.');
        } catch (HttpException $e) {
            $this->error($e->getMessage(), $e->getStatusCode());
        }
    }
}
```

### Formato de respuesta consistente

- **Éxito GET**: `{"success": true, "data": {...}}`
- **Éxito POST**: `{"success": true, "data": {...}, "message": "..."}` con HTTP 201
- **Error 404**: `{"success": false, "error": {"code": 404, "message": "..."}}`
- **Error 422**: `{"success": false, "error": {"code": 422, "message": "..."}}`

---

## 4. Cómo Probar el Sistema Manualmente

### Flujo Completo de Prueba

```bash
# 1. Iniciar servidor
cd /c/xampp/htdocs/crm-ia-first
php -S localhost:8000 -t public

# 2. Probar login desde terminal
# Obtener página de login + CSRF
curl -s -c cookies.txt http://localhost:8000/login > login_page.html
CSRF=$(grep -oP '_csrf_token" value="\K[^"]+' login_page.html)

# Login
curl -s -c cookies.txt -b cookies.txt -X POST \
  -d "_csrf_token=$CSRF&email=admin@crm.com&password=admin123" \
  http://localhost:8000/login > /dev/null

# 3. Ver dashboard
curl -s -b cookies.txt http://localhost:8000/ | head -50

# 4. API - Login + obtener token CSRF
# Primero obtener un token CSRF de la sesión
# (En API, el token se obtiene tras login)
curl -s -c api_cookies.txt -X POST \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@crm.com","password":"admin123"}' \
  http://localhost:8000/api/v1/auth/login

# 5. API - Listar clientes (solo auth, sin CSRF)
curl -s -b api_cookies.txt \
  http://localhost:8000/api/v1/clients

# 6. API - Crear cliente (auth + CSRF)
# Obtener CSRF token de la sesión
CSRF_TOKEN=$(curl -s -b api_cookies.txt http://localhost:8000/api/v1/auth/me | grep -o '"token":"[^"]*"')

curl -s -b api_cookies.txt \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
  -X POST \
  -d '{"company_name":"Test S.A.","email":"test@test.com"}' \
  http://localhost:8000/api/v1/clients

# 7. Exportar PDF de clientes
curl -s -b cookies.txt -o clientes.pdf \
  http://localhost:8000/export/clients
```

### Prueba de Fuerza Bruta

```bash
# Intentar login 5+ veces con credenciales incorrectas
for i in $(seq 1 6); do
  curl -s http://localhost:8000/login > /tmp/login_$i.html
  CSRF=$(grep -oP '_csrf_token" value="\K[^"]+' /tmp/login_$i.html)
  echo "Intento $i:"
  curl -s -X POST \
    -d "_csrf_token=$CSRF&email=admin@crm.com&password=wrong" \
    http://localhost:8000/login | grep -oP 'flash-error">[^<]+'
done
```

---

## 5. Cómo Depurar Errores

### Activar Modo Debug

En `.env`:
```ini
APP_ENV=development
APP_DEBUG=true
```

Con `APP_DEBUG=true`:
- PHP muestra todos los errores y warnings
- Las excepciones devuelven mensajes detallados
- La respuesta JSON incluye la traza del error

### Log de Servidor

```bash
# Iniciar servidor con log separado
php -S localhost:8000 -t public 2>/tmp/php_errors.log

# Ver errores en tiempo real
tail -f /tmp/php_errors.log

# Ver errores de PHP (todos los logs)
cat /tmp/php_errors.log | grep -i "fatal\|error\|exception\|warning"
```

### Errores Comunes y Soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| `Class "App\... not found` | Falta composer autoload | Ejecutar `composer dump-autoload` |
| `PDOException: SQLSTATE[HY000]` | MySQL no disponible | Verificar que MySQL/MariaDB esté corriendo |
| `PDOException: Unknown database` | BD no creada | Ejecutar `php database/migrate.php` |
| `500 en dashboard` | Variable PHP incorrecta | Revisar que todas las variables pasadas a la vista existan |
| `403 CSRF inválido` | Token no coincide | Regenerar página, verificar cookies de sesión |
| `Undefined array key` | Falta variable en la vista | Revisar `$this->render()` en el controlador |
| `429 Too Many Attempts` | Bloqueo por fuerza bruta | Esperar 15 minutos o limpiar tabla `login_attempts` |
| `Slow page load` | Múltiples queries SQL | Revisar DashboardController, añadir índices |

### Forzar Recreación de Base de Datos

```sql
DROP DATABASE IF EXISTS crm_profesional;
CREATE DATABASE crm_profesional CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego: `php database/migrate.php && php database/seed.php`

---

## 6. Cómo Extender el Dashboard

### Agregar un Nuevo KPI

En `app/Controllers/DashboardController.php`:
```php
// Dentro del método index(), agregar:
$newLeadsThisMonth = (int) $db->query("
    SELECT COUNT(*) AS total FROM leads 
    WHERE deleted_at IS NULL 
    AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')
")->fetch()->total;

// Pasar a la vista:
$this->render('dashboard/index', [
    // ... variables existentes ...
    'newLeadsThisMonth' => $newLeadsThisMonth,
]);
```

En `app/Views/dashboard/index.php`:
```html
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all hover:-translate-y-0.5">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Nuevos Leads (Mes)</p>
            <p class="text-3xl font-bold text-gray-800 mt-1"><?= number_format($newLeadsThisMonth) ?></p>
        </div>
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" ...></svg>
        </div>
    </div>
</div>
```

### Agregar un Nuevo Gráfico Chart.js

```javascript
// En el bloque <script> del dashboard:
(function() {
    const ctx = document.getElementById('myNewChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut', // o 'bar', 'line', 'polarArea'
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($values) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#eab308'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
})();
```

---

## 7. Cómo Implementar reCAPTCHA

### Estado Actual

Actualmente el login tiene el campo oculto pero **no hay verificación server-side**. Para activarlo:

### Paso 1: Obtener API Keys

1. Ir a https://www.google.com/recaptcha/admin
2. Crear una nueva aplicación
3. Seleccionar reCAPTCHA v2 (Invisible) o v3
4. Copiar Site Key y Secret Key

### Paso 2: Configurar .env

```ini
RECAPTCHA_SITE_KEY=6Lc..._tu_site_key
RECAPTCHA_SECRET_KEY=6Lc..._tu_secret_key
```

### Paso 3: Agregar widget JS en el login

En `app/Views/auth/login.php`, agregar antes del `</form>`:
```html
<script src="https://www.google.com/recaptcha/api.js?render=<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>"></script>
<script>
grecaptcha.ready(function() {
    grecaptcha.execute('<?= $_ENV['RECAPTCHA_SITE_KEY'] ?>', {action: 'login'})
        .then(function(token) {
            document.getElementById('recaptchaResponse').value = token;
        });
});
</script>
```

### Paso 4: Verificar en AuthController

En `app/Controllers/AuthController.php`, método `login()`:
```php
// Después de validateCsrf(), antes de procesar login:
if (!empty($_ENV['RECAPTCHA_SECRET_KEY'])) {
    $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'content' => http_build_query([
                'secret' => $_ENV['RECAPTCHA_SECRET_KEY'],
                'response' => $recaptchaToken,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
            ]),
        ],
    ]));
    $result = json_decode($verify);
    if (!$result->success || ($result->score ?? 1) < 0.5) {
        Session::flash('error', 'Verificación reCAPTCHA fallida.');
        $this->redirect('/login');
    }
}
```

---

## 8. Cómo Configurar el Entorno de Producción

### .env para Producción

```ini
APP_NAME="CRM Empresarial"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://crm.miempresa.com

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=crm_produccion
DB_USERNAME=crm_user
DB_PASSWORD=contraseña_segura_123

SESSION_LIFETIME=60
SESSION_NAME=crm_session
SESSION_SECURE=true

CSRF_TOKEN_NAME=crm_csrf_token
```

### Apache Virtual Host

```apache
<VirtualHost *:80>
    ServerName crm.miempresa.com
    DocumentRoot /var/www/crm/public
    
    <Directory /var/www/crm/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/crm_error.log
    CustomLog ${APACHE_LOG_DIR}/crm_access.log combined
</VirtualHost>
```

### Verificación Post-Despliegue

```bash
# 1. Verificar que las rutas funcionan
curl -s -o /dev/null -w "%{http_code}" https://crm.miempresa.com/login
# Debe responder: 200

# 2. Verificar que errores no muestran detalles
curl -s https://crm.miempresa.com/ruta-inexistente
# Debe responder JSON sin trazas

# 3. Verificar cabeceras de seguridad
curl -sI https://crm.miempresa.com/login | grep -i "X-Frame-Options\|X-Content-Type-Options"

# 4. Verificar conexión a BD
php -r "require 'vendor/autoload.php'; \$(require 'config/database.php'); echo 'OK';"

# 5. Verificar que APP_DEBUG=false oculta errores
curl -s https://crm.miempresa.com/api/v1/clientes
# Debe devolver {"success":false,"error":{"code":404,"message":"Not Found"}}
```
