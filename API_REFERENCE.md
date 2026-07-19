# API Reference - CRM Profesional

## 📡 Base URL

```
http://localhost:8000/api/v1
```

## 📦 Formato de Respuesta

### Éxito
```json
{
    "success": true,
    "data": { ... },
    "message": "Operación exitosa."
}
```

### Paginada
```json
{
    "success": true,
    "data": [ ... ],
    "pagination": {
        "total": 50,
        "page": 1,
        "per_page": 15,
        "total_pages": 4
    }
}
```

### Error
```json
{
    "success": false,
    "error": {
        "code": 422,
        "message": "Error de validación",
        "details": { ... }
    }
}
```

### Códigos de Estado

| Código | Significado |
|--------|------------|
| 200 | OK |
| 201 | Creado |
| 400 | Bad Request |
| 401 | No autenticado |
| 403 | Prohibido / CSRF inválido |
| 404 | No encontrado |
| 409 | Conflicto (duplicado) |
| 422 | Error de validación |
| 429 | Demasiados intentos (fuerza bruta) |
| 500 | Error interno |

## 🔐 Autenticación

### POST /auth/login

Inicia sesión y establece la sesión.

**Request:**
```json
{
    "email": "admin@crm.com",
    "password": "admin123"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Administrador",
            "email": "admin@crm.com",
            "role": "admin"
        }
    },
    "message": "Inicio de sesión exitoso."
}
```

**Response (401) - Credenciales inválidas:**
```json
{
    "success": false,
    "error": {
        "code": 401,
        "message": "Credenciales inválidas."
    }
}
```

**Response (429) - Bloqueo por fuerza bruta:**
```json
{
    "success": false,
    "error": {
        "code": 429,
        "message": "Demasiados intentos. Su IP ha sido bloqueada por 15 minutos."
    }
}
```

### POST /auth/logout

Cierra la sesión actual. No requiere autenticación.

**Response (200):**
```json
{
    "success": true,
    "data": null,
    "message": "Sesión cerrada."
}
```

### GET /auth/me

Obtiene los datos del usuario autenticado. **Requiere autenticación.**

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@crm.com",
        "role": "admin"
    }
}
```

---

## 👥 Clientes

Todas las rutas de clientes requieren **autenticación (AuthMiddleware)**.  
Las rutas mutables (POST, PUT, DELETE) requieren además **CSRF (CsrfMiddleware)**.

### GET /clients

Lista paginada de clientes activos (no eliminados).

**Query params:**
| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `page` | int | 1 | Número de página |
| `per_page` | int | 15 | Items por página (5-100) |
| `search` | string | "" | Búsqueda por nombre, email o industria |

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "company_name": "Corporación TechSolutions",
            "email": "contacto@techsolutions.com",
            "phone": "+52 55 1234 5678",
            "industry": "Tecnología",
            "owner_id": 1,
            "deleted_at": null,
            "created_at": "2026-07-19 12:00:00",
            "updated_at": "2026-07-19 12:00:00",
            "owner_name": "Administrador"
        }
    ],
    "pagination": {
        "total": 2,
        "page": 1,
        "per_page": 15,
        "total_pages": 1
    }
}
```

### POST /clients

Crea un nuevo cliente.

**Request:**
```json
{
    "company_name": "Nueva Empresa S.A.",
    "email": "contacto@nueva.com",
    "phone": "+52 55 9999 8888",
    "industry": "Salud"
}
```

**Response (201):**
```json
{
    "success": true,
    "data": { ... },
    "message": "Cliente creado exitosamente."
}
```

### GET /clients/{id}

Obtiene un cliente por ID.

**Response (200):** Datos del cliente con `owner_name`.

**Response (404):** `{"error": {"code": 404, "message": "Cliente no encontrado."}}`

### PUT /clients/{id}

Actualiza un cliente existente.

**Request:** Mismos campos que POST.

**Response (200):** Datos actualizados del cliente.

### DELETE /clients/{id}

Elimina (soft delete) un cliente.

**Response (200):**
```json
{
    "success": true,
    "data": null,
    "message": "Cliente eliminado exitosamente."
}
```

---

## 💰 Oportunidades (Leads)

### GET /leads

Lista paginada de oportunidades.

**Query params:**
| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `page` | int | 1 | Número de página |
| `per_page` | int | 15 | Items por página |
| `status` | enum | "" | Filtro: `new`, `contacted`, `qualified`, `proposal`, `won`, `lost` |

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "client_id": 1,
            "assigned_to": 1,
            "title": "Implementación ERP",
            "status": "qualified",
            "estimated_value": 150000.00,
            "source": "Web",
            "expected_close_date": null,
            "company_name": "Corporación TechSolutions",
            "assigned_name": "Administrador"
        }
    ],
    "pagination": { ... }
}
```

### POST /leads

Crea una nueva oportunidad.

**Request:**
```json
{
    "client_id": 1,
    "assigned_to": 1,
    "title": "Nuevo Proyecto",
    "status": "new",
    "estimated_value": 50000.00,
    "source": "Referido",
    "expected_close_date": "2026-12-31"
}
```

**Response (201):** Datos de la oportunidad creada.

### GET /leads/{id}

Obtiene una oportunidad por ID.

### PUT /leads/{id}

Actualiza una oportunidad.

### DELETE /leads/{id}

Elimina (soft delete) una oportunidad.

---

## 🎫 Tickets de Soporte

### GET /tickets

Lista paginada de tickets. Ordenados por prioridad (urgente → baja) y luego por fecha descendente.

**Query params:**
| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `page` | int | 1 | |
| `per_page` | int | 15 | |
| `status` | enum | "" | `open`, `in_progress`, `resolved`, `closed` |

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "client_id": 1,
            "assigned_to": 2,
            "subject": "Problema con módulo de facturación",
            "description": "El módulo no genera PDFs correctamente.",
            "priority": "high",
            "status": "open",
            "company_name": "Corporación TechSolutions",
            "assigned_name": "Agente de Ventas",
            "creator_name": "Administrador"
        }
    ],
    "pagination": { ... }
}
```

### POST /tickets

Crea un nuevo ticket.

**Request:**
```json
{
    "client_id": 1,
    "assigned_to": 2,
    "subject": "Error en sistema",
    "description": "Descripción detallada...",
    "priority": "medium"
}
```

### GET /tickets/{id}

Obtiene un ticket con datos del cliente, asignado y creador.

### PUT /tickets/{id}

Actualiza un ticket.

### DELETE /tickets/{id}

Elimina un ticket (hard delete).

---

## 📊 Estadísticas / Dashboard

### GET /stats/dashboard

Obtiene los indicadores clave del dashboard. **Requiere autenticación.**

**Response (200):**
```json
{
    "success": true,
    "data": {
        "summary": {
            "total_clients": 2,
            "active_leads": 2,
            "open_tickets": 2,
            "pipeline_value": 235000.00,
            "won_leads": 0
        },
        "leads_by_status": [
            {"status": "qualified", "total": 1},
            {"status": "proposal", "total": 1}
        ],
        "tickets_by_status": [
            {"status": "open", "total": 2}
        ],
        "recent_clients": [
            {"id": 2, "company_name": "Grupo Financiero Horizonte", "email": "info@horizonte.com", "created_at": "..."},
            {"id": 1, "company_name": "Corporación TechSolutions", "email": "contacto@techsolutions.com", "created_at": "..."}
        ]
    }
}
```

---

## 🛡️ CSRF Protection

Para rutas mutables (POST, PUT, DELETE), el token CSRF debe enviarse de una de las siguientes formas:

1. **Campo POST**: `_csrf_token=<token>` (formularios web)
2. **Header HTTP**: `X-CSRF-Token: <token>` (API)
3. **Header HTTP**: `X-XSRF-Token: <token>` (alternativa)
4. **JSON Body**: `{"_csrf_token": "<token>", ...}` (API)

> El token CSRF se obtiene de la sesión. En formularios web, se renderiza automáticamente con `<?= $csrf_field ?>` que genera `<input type="hidden" name="_csrf_token" value="...">`.

---

## 🌐 Web Routes (HTML)

| Método | Ruta | Controlador | Middleware |
|--------|------|-------------|------------|
| GET | `/login` | AuthController@showLoginForm | — |
| POST | `/login` | AuthController@login | — |
| GET | `/logout` | AuthController@logout | — |
| GET | `/` | DashboardController@index | Auth |
| GET | `/clients` | ClientController@index | Auth |
| GET | `/clients/create` | ClientController@create | Auth |
| POST | `/clients` | ClientController@store | Auth |
| GET | `/clients/{id}` | ClientController@show | Auth |
| GET | `/clients/{id}/edit` | ClientController@edit | Auth |
| POST | `/clients/{id}` | ClientController@update | Auth |
| POST | `/clients/{id}/delete` | ClientController@destroy | Auth |
| GET | `/clients/{clientId}/contacts` | ContactController@index | Auth |
| GET | `/clients/{clientId}/contacts/create` | ContactController@create | Auth |
| POST | `/clients/{clientId}/contacts` | ContactController@store | Auth |
| GET | `/contacts/{id}/edit` | ContactController@edit | Auth |
| POST | `/contacts/{id}` | ContactController@update | Auth |
| POST | `/contacts/{id}/delete` | ContactController@destroy | Auth |
| GET | `/leads` | LeadController@index | Auth |
| GET | `/leads/create` | LeadController@create | Auth |
| POST | `/leads` | LeadController@store | Auth |
| GET | `/leads/{id}` | LeadController@show | Auth |
| GET | `/leads/{id}/edit` | LeadController@edit | Auth |
| POST | `/leads/{id}` | LeadController@update | Auth |
| POST | `/leads/{id}/delete` | LeadController@destroy | Auth |
| GET | `/interactions` | InteractionController@index | Auth |
| GET | `/interactions/create` | InteractionController@create | Auth |
| POST | `/interactions` | InteractionController@store | Auth |
| POST | `/interactions/{id}/delete` | InteractionController@destroy | Auth |
| GET | `/tickets` | TicketController@index | Auth |
| GET | `/tickets/create` | TicketController@create | Auth |
| POST | `/tickets` | TicketController@store | Auth |
| GET | `/tickets/{id}` | TicketController@show | Auth |
| GET | `/tickets/{id}/edit` | TicketController@edit | Auth |
| POST | `/tickets/{id}` | TicketController@update | Auth |
| POST | `/tickets/{id}/delete` | TicketController@destroy | Auth |
| GET | `/export/clients` | ExportController@clientsPdf | Auth |
| GET | `/export/leads` | ExportController@leadsPdf | Auth |
| GET | `/export/dashboard` | ExportController@dashboardPdf | Auth |
