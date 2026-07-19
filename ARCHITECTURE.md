# Arquitectura Técnica - CRM Profesional

## 🏗️ Arquitectura General

### MVC Monolítico Modular

```
┌─────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────────┐  │
│  │    Views/     │  │    API/      │  │     Export PDF       │  │
│  │  (HTML+PHP)   │  │  (JSON)      │  │    (Dompdf HTML)    │  │
│  └──────┬───────┘  └──────┬───────┘  └──────────┬───────────┘  │
├─────────┼─────────────────┼──────────────────────┼──────────────┤
│         │    CONTROLLERS  │                      │             │
│  ┌──────┴──────────────────┴──────────────────────┴──────────┐  │
│  │                    BaseController                          │  │
│  │  ┌──────┐ ┌────────┐ ┌──────┐ ┌────────┐ ┌────────────┐  │  │
│  │  │ Auth │ │ Client │ │ Lead │ │ Ticket │ │ Dashboard  │  │  │
│  │  │  C   │ │  C     │ │  C   │ │  C     │ │  C         │  │  │
│  │  └──────┘ └────────┘ └──────┘ └────────┘ └────────────┘  │  │
│  └───────────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────────┤
│                        BUSINESS LAYER                           │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                      Services                             │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │  │
│  │  │  AuthService  │  │ ClientService │  │  BaseService │  │  │
│  │  │ (login/logout)│  │   (CRUD)      │  │  (abstract)  │  │  │
│  │  │ (brute force) │  │ (unique check)│  │              │  │  │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────────┤
│                       DATA ACCESS LAYER                         │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Repositories                           │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │  │
│  │  │  BaseRepo    │  │ ClientRepo   │  │  UserRepo    │  │  │
│  │  │ (CRUD gené-  │  │ (search,     │  │ (findByEmail,│  │  │
│  │  │  rico PDO)   │  │  softDelete) │  │  findAll)    │  │  │
│  │  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘  │  │
│  │         └─────────────────┼──────────────────┘          │  │
│  │                    ┌──────┴──────┐                      │  │
│  │                    │  Database   │                      │  │
│  │                    │  (Singleton)│                      │  │
│  │                    └──────┬──────┘                      │  │
│  └───────────────────────────┼─────────────────────────────┘  │
│                              │                                 │
│                    ┌─────────┴─────────┐                       │
│                    │    MySQL 8 DB     │                       │
│                    └───────────────────┘                       │
└─────────────────────────────────────────────────────────────────┘
```

## 🔄 Flujo de una Petición Web

### Request Cycle Completo

```
1. Navegador: GET /clients
   ↓
2. public/index.php (Front Controller)
   ├── require vendor/autoload.php (PSR-4)
   ├── Dotenv::load() (.env → $_ENV)
   ├── Error handler (debug/production)
   ├── session_start() + cookie params
   ├── Database::connect() (singleton PDO)
   ├── FastRoute::dispatch(GET, /clients)
   │   └── Match: [ClientController::class, 'index', [AuthMiddleware::class]]
   ├── Middleware Pipeline:
   │   └── AuthMiddleware::handle()
   │       └── Session::isAuthenticated()? → OK → continue
   │       └── No? → redirect /login
   ├── Controller: new ClientController()
   │   └── index()
   │       ├── Pagination::currentPage() (GET['page'])
   │       ├── ClientService::list($page, $perPage, $search)
   │       │   ├── ClientRepository::countActive() (COUNT SQL)
   │       │   └── ClientRepository::findAllActive() (SELECT SQL)
   │       └── $this->render('clients/index', $data)
   │           └── Response::view($view, $data, 'layouts/main')
   │               ├── extract($data) → variables PHP
   │               ├── ob_start()
   │               ├── require views/clients/index.php
   │               ├── $content = ob_get_clean()
   │               └── require views/layouts/main.php
   │                   └── echo HTML con $content insertado
   └── HTML response al navegador
```

### Request Cycle API

```
1. Cliente HTTP: POST /api/v1/clients
   ↓
2. public/index.php
   ├── FastRoute::dispatch(POST, /api/v1/clients)
   │   └── Match: [ClientApiController::class, 'store', [AuthMiddleware, CsrfMiddleware]]
   ├── Middleware Pipeline:
   │   ├── AuthMiddleware::handle() → Session::isAuthenticated()
   │   └── CsrfMiddleware::handle() → verifica token en header/body
   ├── Controller: new ClientApiController()
   │   └── store()
   │       ├── $this->getJsonBody() (php://input decode)
   │       ├── ClientService::create($data)
   │       │   ├── requireField('company_name')
   │       │   ├── ClientRepository::exists('company_name', $name)
   │       │   └── ClientRepository::create($data)
   │       ├── ClientService::find($id)
   │       └── $this->json($client, 201, 'Cliente creado exitosamente.')
   │           └── Response::success($data, 201, $message)
   └── JSON response
```

## 🗄️ Diagrama Entidad-Relación

```
┌─────────────┐       ┌──────────────┐
│    users    │       │   clients    │
├─────────────┤       ├──────────────┤
│ id (PK)     │──┐    │ id (PK)      │
│ name        │  │    │ company_name │
│ email (UQ)  │  │    │ email        │
│ password_   │  │    │ phone        │
│  hash       │  │    │ industry     │
│ role        │  │    │ owner_id(FK)─┼──┐
│ is_active   │  │    │ deleted_at   │  │
│ created_at  │  │    │ created_at   │  │
│ updated_at  │  │    │ updated_at   │  │
└─────────────┘  │    └──────────────┘  │
                  │                      │
                  │  ┌──────────────┐    │
                  │  │   contacts   │    │
                  │  ├──────────────┤    │
                  │  │ id (PK)      │    │
                  └──│ client_id(FK)│────┘
                     │ full_name    │
                     │ position     │
                     │ email        │
                     │ phone        │
                     │ is_primary   │
                     │ created_at   │
                     └──────────────┘

┌─────────────┐       ┌──────────────────┐
│    leads    │       │  interactions    │
├─────────────┤       ├──────────────────┤
│ id (PK)     │       │ id (PK)          │
│ client_id   │───────│ client_id (FK)   │──────┐
│  (FK)       │       │ type (enum)      │      │
│ assigned_to │──┐    │ subject          │      │
│  (FK)       │  │    │ description      │      │
│ title       │  │    │ created_by (FK)  │──┐   │
│ status(enum)│  │    │ created_at       │  │   │
│ estimated_  │  │    │ updated_at       │  │   │
│  value      │  │    └──────────────────┘  │   │
│ source      │  │                          │   │
│ expected_   │  │    ┌────────────┐        │   │
│  close_date │  │    │  tickets   │        │   │
│ deleted_at  │  │    ├────────────┤        │   │
│ created_at  │  │    │ id (PK)    │        │   │
│ updated_at  │  │    │ client_id  │────────┘   │
└─────────────┘  │    │  (FK)      │            │
                  │    │ assigned_to│──┐         │
                  │    │  (FK)      │  │         │
                  │    │ subject    │  │         │
                  │    │ description│  │         │
                  │    │ priority   │  │         │
                  │    │  (enum)    │  │         │
                  │    │ status     │  │         │
                  │    │  (enum)    │  │         │
                  │    │ created_by │──┘         │
                  │    │  (FK)      │            │
                  │    │ created_at │            │
                  │    │ updated_at │            │
                  │    └────────────┘            │
                  │                              │
                  │  ┌──────────────────┐         │
                  │  │ login_attempts   │         │
                  │  ├──────────────────┤         │
                  │  │ id (PK)          │         │
                  │  │ email            │         │
                  │  │ ip_address       │         │
                  │  │ success          │         │
                  │  │ attempted_at     │         │
                  │  └──────────────────┘         │
                  │                               │
                  └───────────────────────────────┘
```

## 🛡️ Middleware Pipeline

```
                    ┌──────────────┐
                    │   Request    │
                    └──────┬───────┘
                           │
              ┌────────────┴────────────┐
              │   ¿Requiere auth?       │
              └─────┬────────────┬──────┘
               No   │            │  Sí
                    │    ┌───────┴───────┐
                    │    │ AuthMiddleware │
                    │    │  → ¿Session   │
                    │    │    has 'user_ │
                    │    │    id'?       │
                    │    └───┬───────┬───┘
                    │     No │       │ Sí
                    │  ┌─────┴─┐     │
                    │  │redirect│     │
                    │  │/login  │     │
                    │  └───────┘     │
                    │         ┌──────┴──────────┐
                    │         │  ¿Ruta mutable  │
                    │         │  (POST/PUT/     │
                    │         │   DELETE)?      │
                    │         └───┬─────────┬───┘
                    │          No │         │ Sí
                    │             │  ┌──────┴───────┐
                    │             │  │CsrfMiddleware │
                    │             │  │ → validar     │
                    │             │  │   token       │
                    │             │  └───┬──────┬───┘
                    │             │  Inv. │     │ Vál.
                    │             │  ┌────┴─┐   │
                    │             │  │ 403  │   │
                    │             │  │ JSON │   │
                    │             │  └──────┘   │
                    │             │      ┌──────┴───────┐
                    │             │      │ ¿Requiere    │
                    │             │      │ rol admin?   │
                    │             │      └───┬──────┬───┘
                    │             │       No │      │ Sí
                    │             │          │  ┌───┴──────┐
                    │             │          │  │AdminMW   │
                    │             │          │  │→ ¿role=  │
                    │             │          │  │ 'admin'? │
                    │             │          │  └───┬──┬──┘
                    │             │          │   No │  │ Sí
                    │             │          │  ┌───┴┐ │
                    │             │          │  │403 │ │
                    │             │          │  └────┘ │
                    │             │          │         │
                    │             │          └─────────┘
                    │             │                    │
                    │             └────────────────────┘
                    │                                  │
              ┌─────┴──────────────────────────────────┴──┐
              │              Controller::method()          │
              │   → Service → Repository → SQL → PDO      │
              └────────────────────────────────────────────┘
```

## 💾 Patrón Repository

### BaseRepository (CRUD Genérico)

```
BaseRepository
├── find(int $id)           → SELECT * WHERE id = :id
├── findAll(page, perPage)  → SELECT * ORDER BY ... LIMIT ... OFFSET ...
├── count($where, $params)  → SELECT COUNT(*) ...
├── create(array $data)     → INSERT INTO ... (...columns...) VALUES (:placeholders)
├── update(int $id, $data)  → UPDATE ... SET col=:col, ... WHERE id = :id
├── delete(int $id)         → DELETE ... WHERE id = :id
├── raw($sql, $params)      → PDOStatement (para consultas complejas)
└── exists($column, $value, $excludeId) → Verifica duplicados
```

### Herencia de Repositorios

```
BaseRepository
├── UserRepository
│   ├── findByEmail($email)       → Busca usuario por email (login)
│   ├── findAllAgents()           → Agentes activos (dropdowns)
│   └── findAllActive()           → Todos los usuarios activos
│
└── ClientRepository
    ├── softDelete($id)            → UPDATE deleted_at = NOW()
    ├── findAllActive($page, $perPage) → JOIN con users (owner)
    ├── findWithOwner($id)         → Cliente + owner name
    ├── search($term, $page, $perPage) → Búsqueda LIKE
    ├── countSearch($term)         → COUNT con filtro LIKE
    └── countActive()              → COUNT WHERE deleted_at IS NULL
```

## 🧩 Singleton Database

```php
Database::connect($config)  // Primera llamada → crea PDO
Database::getInstance()      // Llamadas siguientes → retorna instancia
Database::disconnect()       // Resetea instancia (testing)
Database::beginTransaction() // Wrapper
Database::commit()           // Wrapper
Database::rollBack()         // Wrapper
```

## 🔎 Puntos de Mejora y Optimización Identificados

### 1. Arquitectura y Código

| Problema | Impacto | Solución Propuesta |
|----------|---------|-------------------|
| **Inconsistencia en Controllers**: Algunos usan Services (ClientController) y otros usan PDO directo (LeadController, TicketController, InteractionController) | Mantenibilidad | Migrar todos los controllers al patrón Service + Repository |
| **SQL en Controllers**: Consultas SQL directamente en controladores rompe la separación de capas | Testabilidad, mantenibilidad | Mover toda la lógica SQL a Repositories |
| **BaseValidator subutilizado**: Existe pero no se usa en ningún controller actual | Validación débil | Integrar BaseValidator en todos los stores/updates |
| **Models subutilizados**: `User` y `Client` existen pero no se usan (se usan stdClass) | Tipo débil | Migrar a typed objects con métodos de negocio |
| **Reuso de BaseRepository**: Lead, Ticket, Interaction no tienen su propio Repository | Duplicación de SQL | Crear repositorios faltantes |

### 2. Rendimiento

| Problema | Impacto | Solución Propuesta |
|----------|---------|-------------------|
| **Dashboard: consultas separadas** (6-8 queries en cada carga) | Latencia en dashboard | Crear procedimiento almacenado o cachear resultados |
| **Sin caché de consultas** | Cada request ejecuta SQL | Implementar query cache (memcached/file) para consultas pesadas |
| **Tailwind CSS vía CDN** | Dependencia de internet | Compilar Tailwind localmente o usar versión purgada |
| **Sin índices compuestos** en tablas grandes | Degradación con muchos datos | Revisar EXPLAIN y añadir covering indexes |

### 3. Seguridad

| Problema | Impacto | Solución Propuesta |
|----------|---------|-------------------|
| **reCAPTCHA sin verificación server-side**: El campo oculto existe pero no se valida | Protección nula contra bots | Implementar verificación vía Google API |
| **Sin rate limiting en API** (solo en login) | Abuso de endpoints | Middleware de rate limiting global |
| **Cookies sin SameSite=Strict** (usa Lax) | CSRF en subdominios | Evaluar Strict según necesidades |
| **Encabezados de seguridad** faltantes: CSP, HSTS, X-Frame-Options | Vulnerabilidades varias | Añadir security headers en index.php |

### 4. Testing

| Problema | Impacto | Solución Propuesta |
|----------|---------|-------------------|
| **Solo un test de ejemplo** | Sin cobertura real | Tests unitarios para Services, integration tests para APIs |
| **Base de datos de testing separada** (`crm_profesional_test`) pero no configurada | Tests sin DB | Automatizar creación de BD test + migraciones |
| **Sin CI/CD** | Sin integración continua | Configurar GitHub Actions con PHPUnit |

### 5. Funcionalidad

| Problema | Impacto | Solución Propuesta |
|----------|---------|-------------------|
| **Sin auditoría de cambios** | No hay trazabilidad | Implementar tabla `audit_log` con triggers o middleware |
| **Sin notificaciones** (email/in-app) | Usuarios no reciben alerts | Integrar PHPMailer/Symfony Mailer |
| **Sin archivos adjuntos** en tickets/interacciones | Limitación funcional | Sistema de subida de archivos con validación |
| **Sin dashboard en API** (StatsApiController cubre lo básico) | API incompleta | Endpoints analíticos más detallados |
| **Sin exportación CSV/Excel** (solo PDF) | Formato limitado | Añadir exportadores CSV y XLSX (PhpSpreadsheet) |
| **Sin búsqueda avanzada** (solo LIKE simple) | Filtrado limitado | Implementar Elasticsearch o FULLTEXT indexes |

### 6. DevOps

| Problema | Impacto | Solución Propuesta |
|----------|---------|-------------------|
| **Sin Docker** | Entorno inconsistente | Crear docker-compose.yml con PHP + MySQL + Adminer |
| **Sin scripts de deploy** | Despliegue manual | Crear Makefile con targets comunes |
| **Sin logging estructurado** (solo error_log nativo) | Debugging difícil | Implementar Monolog o logger PSR-3 |
| **Sin health check endpoint** | Monitoreo | Crear `/api/v1/health` con estado de DB y sesión |

---

> **Recomendaciones prioritarias:**
> 1. **Migrar LeadController, TicketController, InteractionController** al patrón Service+Repository (consistencia)
> 2. **Integrar BaseValidator** en todos los formularios (validación robusta)
> 3. **Crear repositorios faltantes** (LeadRepository, TicketRepository, InteractionRepository)
> 4. **Implementar rate limiting** global para la API
> 5. **Añadir más tests** empezando por los services
