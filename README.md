# CRM Profesional

Sistema de gestión de clientes, ventas y soporte (CRM) construido con una arquitectura MVC monolítica en PHP 8.3 con MySQL 8.

## 📋 Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| **Lenguaje** | PHP 8.3 (`declare(strict_types=1)`) |
| **Base de datos** | MySQL 8 / MariaDB 10.4+ |
| **Servidor** | Apache 2.4 / PHP Built-in Server |
| **Frontend** | Tailwind CSS (CDN) + Chart.js 4 |
| **Router** | FastRoute (nikic/fast-route) |
| **Env** | phpdotenv (vlucas/phpdotenv) |
| **PDF** | Dompdf (dompdf/dompdf) |
| **DB Driver** | PDO MySQL (native) |
| **Testing** | PHPUnit 11 |
| **Autoload** | Composer PSR-4 |

## 🚀 Instalación Rápida

### Requisitos

- PHP 8.1 o superior con extensiones: `pdo_mysql`, `sodium`, `mbstring`
- MySQL 8 / MariaDB 10.4+
- Composer

### Pasos

```bash
# 1. Clonar el repositorio
git clone <repo> crm
cd crm

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
# Editar .env con los datos de tu base de datos

# 4. Crear base de datos y ejecutar migraciones
php database/migrate.php

# 5. (Opcional) Poblar con datos de prueba
php database/seed.php

# 6. Iniciar servidor de desarrollo
php -S localhost:8000 -t public
```

### Credenciales por Defecto

| Rol | Email | Contraseña |
|-----|-------|-----------|
| **Admin** | admin@crm.com | admin123 |
| **Agente** | agent@crm.com | agent123 |

## 📁 Estructura del Proyecto

```
crm/
├── public/                    # Punto de entrada pública
│   ├── index.php              # Front controller (autoload, env, sesión, DB, router)
│   └── .htaccess              # Rewrite rules (Apache)
│
├── config/                    # Configuración
│   ├── app.php                # App, sesión, CSRF, paginación
│   └── database.php           # Conexión PDO MySQL
│
├── routes/                    # Definición de rutas
│   ├── web.php                # ~30 rutas web (HTML)
│   └── api.php                # ~20 rutas API REST (JSON)
│
├── app/
│   ├── Controllers/           # Controladores web
│   │   ├── BaseController.php # Clase base con métodos helpers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ClientController.php
│   │   ├── ContactController.php
│   │   ├── LeadController.php
│   │   ├── InteractionController.php
│   │   ├── TicketController.php
│   │   └── ExportController.php
│   │   └── Api/               # Controladores API REST
│   │       ├── AuthApiController.php
│   │       ├── ClientApiController.php
│   │       ├── LeadApiController.php
│   │       ├── TicketApiController.php
│   │       └── StatsApiController.php
│   │
│   ├── Services/              # Lógica de negocio
│   │   ├── BaseService.php
│   │   ├── AuthService.php    # Autenticación + fuerza bruta
│   │   └── ClientService.php
│   │
│   ├── Repositories/          # Capa de acceso a datos
│   │   ├── Database.php       # Singleton PDO
│   │   ├── BaseRepository.php # CRUD genérico
│   │   ├── UserRepository.php
│   │   └── ClientRepository.php
│   │
│   ├── Models/                # Modelos de dominio
│   │   ├── User.php
│   │   └── Client.php
│   │
│   ├── Middleware/             # Pipeline de middlewares
│   │   ├── AuthMiddleware.php  # Protege rutas autenticadas
│   │   ├── AdminMiddleware.php # Restricción por rol
│   │   └── CsrfMiddleware.php  # Protección CSRF en API
│   │
│   ├── Validators/            # Validación de datos
│   │   └── BaseValidator.php
│   │
│   ├── Helpers/               # Utilidades
│   │   ├── Session.php        # Manejo de sesión
│   │   ├── Response.php       # JSON, vistas, redirects
│   │   ├── Csrf.php           # Tokens CSRF
│   │   └── Pagination.php     # Paginación
│   │
│   ├── Exceptions/            # Excepciones personalizadas
│   │   ├── HttpException.php  # HTTP con código de estado
│   │   └── ValidationException.php
│   │
│   └── Views/                 # Plantillas HTML (PHP puro)
│       ├── layouts/
│       │   ├── main.php       # Layout autenticado (sidebar)
│       │   └── guest.php      # Layout público (login)
│       ├── auth/login.php
│       ├── dashboard/index.php
│       ├── clients/{index,form,show}.php
│       ├── contacts/{index,form}.php
│       ├── leads/{index,form,show}.php
│       ├── interactions/{index,form}.php
│       └── tickets/{index,form,show}.php
│
├── database/
│   ├── migrations/            # Migraciones SQL (7 archivos)
│   ├── seeds/                 # Datos de prueba SQL
│   ├── migrate.php            # Runner de migraciones
│   └── seed.php               # Runner de semillas (hashes Argon2id)
│
├── storage/                   # Almacenamiento
│   ├── logs/
│   ├── cache/
│   └── views/
│
├── tests/                     # Tests PHPUnit
│   ├── TestCase.php           # Clase base con helpers de reflexión
│   └── Feature/ExampleTest.php
│
├── composer.json
├── phpunit.xml
└── .env.example
```

## 🔐 Flujo de Autenticación

```
Login Form → AuthController::login()
  → AuthService::login(email, password)
    → checkBruteForce() [verifica IP y email]
    → UserRepository::findByEmail()
    → password_verify() [Argon2id]
    → session_regenerate_id() [anti-fijación]
    → Session::set() [user_id, user_name, user_role]
    → Csrf::regenerate()
  → Redirect a Dashboard

Logout → AuthService::logout()
  → Session::destroy()
  → Csrf::regenerate()
```

### Middleware Pipeline

Todas las rutas protegidas ejecutan un pipeline de middlewares:

```
Request → AuthMiddleware → [AdminMiddleware] → Controller
         API mutables: + CsrfMiddleware
```

- **AuthMiddleware**: Verifica `Session::isAuthenticated()`. Redirige a `/login` (web) o 401 (API).
- **AdminMiddleware**: Verifica rol `admin`. 403 si no es admin.
- **CsrfMiddleware**: Verifica token en POST/PUT/DELETE. Soporta: `_csrf_token` POST, header `X-CSRF-TOKEN`, JSON body.

## 🗄️ Esquema de Base de Datos

### Tablas (7 migraciones)

```
users ─────────┐
               ├── clients ─────── contacts
               │       │
               ├── leads
               │
               ├── interactions
               │
               └── tickets

login_attempts (independiente, para fuerza bruta)
```

### Relaciones Clave

| Tabla | Clave Foránea | Referencia | Tipo |
|-------|--------------|------------|------|
| clients | owner_id | users.id | SET NULL |
| contacts | client_id | clients.id | CASCADE |
| leads | client_id | clients.id | CASCADE |
| leads | assigned_to | users.id | SET NULL |
| interactions | client_id | clients.id | CASCADE |
| interactions | created_by | users.id | SET NULL |
| tickets | client_id | clients.id | CASCADE |
| tickets | assigned_to | users.id | SET NULL |
| tickets | created_by | users.id | SET NULL |

### Soft Delete

Las tablas `clients` y `leads` implementan soft delete mediante el campo `deleted_at DATETIME NULL`. Todas las consultas de listado filtran con `WHERE deleted_at IS NULL`.

## 🔄 Flujo de Información del Sistema

### Web (HTML + Sesión)

```
Navegador → GET /path
  → public/index.php (Front Controller)
    → FastRoute dispatcher
    → Middleware pipeline
    → Controller::method()
      → Services (lógica)
        → Repositories (SQL)
      → Response::view('vista', $data, 'layout')
        → Layout + Content combinados en HTML
  → HTML renderizado al navegador
```

### API REST (JSON)

```
Cliente HTTP → POST /api/v1/...
  → public/index.php
    → FastRoute dispatcher
    → Middleware pipeline (Auth + Csrf)
    → ApiController::method()
      → getJsonBody() [php://input]
      → Services/Repositories
      → Response::success() / Response::error()
  → JSON response
```

### Diagrama de Flujo: Cliente → Oportunidad → Atención

```
REGISTRO DE CLIENTE
  → Gestión de Contactos (personas en la empresa)
  → Interacciones (llamadas, correos, reuniones)
  → Creación de Oportunidad/Lead
    → Pipeline: Nuevo → Contactado → Calificado → Propuesta → Ganado/Perdido
    → Asignación a agente responsable
  → Tickets de Soporte (problemas/post-venta)
    → Prioridad: Baja → Media → Alta → Urgente
    → Estados: Abierto → En Progreso → Resuelto → Cerrado
```

## 📊 Dashboard

El dashboard muestra 4 indicadores KPI y 3 gráficos interactivos con Chart.js:

- **Pipeline de Ventas** (gráfico doughnut): leads agrupados por estado
- **Tickets por Estado** (gráfico de barras): tickets por estado actual
- **Tendencia de Interacciones** (gráfico de líneas): últimos 6 meses
- **Resumen Rápido**: valor ganado vs pipeline activo
- **Interacciones y Clientes Recientes**: listas con los últimos 5 registros

## 🛡️ Seguridad

- **Contraseñas**: Argon2id con `memory_cost=65536, time_cost=4, threads=3`
- **CSRF**: Token por sesión, regenerado en login/logout. Validado en POST/PUT/DELETE.
- **SQL Injection**: Todas las consultas usan PDO prepared statements.
- **Protección de Fuerza Bruta**: Bloqueo por IP y por email tras 5 intentos fallidos en 15 minutos.
- **Sesión**: Regeneración de ID en login, cookie httponly + samesite=Lax.
- **XSS**: Escape con `htmlspecialchars()` en todas las vistas.
- **Error Handling**: En producción, mensajes genéricos; en debug, detalles completos.

## 🧪 Tests

```bash
# Ejecutar tests
composer test

# Estructura de tests
tests/
├── TestCase.php           # Clase base con helpers (invokeMethod, setProperty)
└── Feature/
    └── ExampleTest.php    # Tests de autoload, env, CSRF
```

## 📦 Exportación PDF

El sistema puede exportar a PDF usando Dompdf:

| Ruta | Contenido |
|------|-----------|
| `/export/clients` | Lista completa de clientes |
| `/export/leads` | Lista de oportunidades (respeta filtro activo) |
| `/export/dashboard` | Reporte del dashboard con KPIs |

## 📋 Convenciones de Código

- PHP 8.3 con `declare(strict_types=1)` en todos los archivos
- PSR-12 para estilo de código
- PSR-4 para autoloading (`App\` → `app/`)
- Controladores delgados: lógica en Services, SQL en Repositories
- Respuesta JSON uniforme: `{"success": true, "data": ..., "message": "..."}`
- Errores: `{"success": false, "error": {"code": 404, "message": "..."}}`
- Rutas API bajo prefijo `/api/v1`
- Nomenclatura: camelCase para métodos, snake_case para DB
