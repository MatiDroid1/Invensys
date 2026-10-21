# Fase 1 — Arquitectura multiempresa propuesta

**Estado:** propuesta aprobada para implementación por fases.
**Complementa:** [`01_DIAGNOSTICO.md`](01_DIAGNOSTICO.md).

Este documento fija las decisiones de arquitectura (ADR) que Mankey se van a aplicar. Cada decisión
registra el contexto, la opción elegida, las alternativas descartadas y las consecuencias, para que
las fases siguientes no reintenten decisiones ya tomadas.

---

## ADR-001 — Base de datos compartida con separación lógica por `organization_id`

**Estado:** aceptada · **Fase:** 3

### Contexto
El producto debe servir a dos escenarios: (a) una instalación por cliente, en su propio servidor;
(b) una sola instalación multi-tenant para varios clientes. Ambos necesitan aislamiento de datos
real.

### Decisión
Una base de datos. Toda tabla de negocio lleva `organizacion_id`. El aislamiento lo impone la
aplicación, en tres capas:

1. **Global scope Eloquent** (`App\Models\Concerns\PerteneceAOrganizacion`) que añade
   `where <tabla>.organizacion_id = <contexto>` a toda consulta del modelo.
2. **Asignación forzada:** `organization_id` no está en el `$fillable` de ningún modelo. Se
   asigna desde `OrganizacionActual::id()` y nunca desde el request.
3. **Comprobación explícita** (`verificarPertenencia()`) en las operaciones sensibles: además del
   scope, se comprueba que el modelo pertenece a la organización activa. Esto protege contra
   fugas por `withoutGlobalScope`, relaciones eager-loaded, o registros huérfanos.

### Alternativas descartadas
- **BD por cliente:** una migración, un respaldo y una actualización por cliente. Rompe el objetivo
  de "un producto, muchas empresas" y multiplica el coste operativo.
- **Esquema por cliente:** mismo problema, con migraciones por esquema.
- **Row Level Security de PostgreSQL:** técnicamente superior, pero el proyecto usa MySQL/MariaDB
  (requisito de despliegue XAMPP). Reconsiderable si el producto migra a PostgreSQL.

### Consecuencias
- **A favor:** una migración, un respaldo, una actualización. Consultas entre organizaciones
  triviales para soporte. Backup único.
- **En contra:** el aislamiento depende del código. Requiere pruebas de aislamiento obligatorias en
  CI (se crean en Fase 3) y prohibits explícito de `withoutGlobalScope()` fuera de consola.
- **Mitigación:** el scope se desactiva únicamente mediante
  `OrganizacionActual::sinOrganizacion()`, que registra en el log quién lo desactivó y por qué.

---

## ADR-002 — El stock se sigue derivando de los movimientos

**Estado:** aceptada (mantiene la decisión actual) · **Fase:** 4

### Contexto
El proyecto actual calcula el stock como la suma de movimientos
(`Articulo::EXPRESION_STOCK`). Es la decisión de diseño más importante del sistema.

### Decisión
Se mantiene. **No** se crea una tabla de existencias materializada como fuente de verdad.

Para las consultas de listado se mantiene el subquery correlacionado (`scopeConStock`), que evita
el N+1. Cuando el volumen lo justifique (fase de rendimiento, Fase 8), se puede evaluar una tabla de
saldo **materializada** como caché, siempre con un comando de conciliación
(`inventario:conciliar`) que la compare contra el historial y la reconstruya si difieren.

### Consecuencias
- Auditabilidad total: el stock es siempre explicable por movimientos.
- Coste de lectura: cada artículo requiere sumar sus movimientos. Mitigado con índices
  `(articulo_id, fecha_movimiento)` y moviendo `stockBajo()` a SQL (Fase 2/8).
- Con traspasos entre bodegas, el total no cambia pero el saldo por bodega sí: se modela con
  `bodega_origen_id` / `bodega_destino_id`.

---

## ADR-003 — Autorización por permisos granulares con Gates y Policies

**Estado:** aceptada · **Fase:** 3

### Contexto
Hoy el control de acceso es binario (`admin` / `usuario`) mediante un middleware. La especificación
del producto requiere 7 roles ampliables por organización, con mínimo privilegio.

### Decisión
- **Permisos atómicos** (p. ej. `articulos.editar`, `costos.editar`, `movimientos.anular`),
  definidos como constantes en `App\Enums\Permiso`.
- **Mapa por defecto rol → permisos** en `App\Enums\RolOrganizacion`. Código, documentado, testeado.
- **Anomalías por organización** en la tabla `organizacion_permisos`
  (`organizacion_id`, `rol`, `permiso`, `permitido`). Permite abrir/cerrar sin desplegar código.
- Laravel **Policies** por modelo + **Gates** para acciones transversales (exportar, importar,
  auditar, configurar).
- El backend es la única frontera de seguridad. La UI usa `@can` solo para **ocultar** acciones.

### Roles
`propietario` · `administrador` · `encargado_inventario` · `bodeguero` · `compras` · `auditor` ·
`consulta`

### Migración desde `users.rol`
El campo `users.rol` se conserva durante la transición. En la Fase 3:
1. Se crea la membresía `organizacion_usuario` con rol equivalente (`admin` → `propietario`,
   `usuario` → `consulta`).
2. `users.rol` queda **obsoleto pero no se borra** (evita romper código no revisado).
3. Se elimina en la Fase 9, cuando ya no quede ninguna referencia.

---

## ADR-004 — Contexto de organización como servicio *scoped*

**Estado:** aceptada · **Fase:** 3

### Decisión
`App\Services\OrganizacionActual` es un singleton *scoped* (`$this->app->scoped()`), no un
helper global con estado en la sesión.

- El middleware `OrganizacionActiva` lo resuelve desde `session('organizacion_id')` y valida la
  membresía activa. Si no es miembro → 403.
- La sesión guarda el `organization_id`; la base de datos es la autoridad (la sesión puede estar
  manipulada, la membresía no).
- **Consola, colas y seeders**: `OrganizacionActual::sinOrganizacion()` desactiva el scope de
  forma explícita y auditable. Los jobs encolados rehidratan el contexto desde su
  `organization_id`.

### Alternativa descartada
Un `Session::get('organization_id')` leído dentro del global scope. Se descartó porque la sesión no
está disponible en colas ni en procesos de consola, y porque un global scope que lee estado mutable
en cada query es difícil de testear de forma determinista.

---

## ADR-005 — Configuración por rubro como *configuración*, no como sistemas separados

**Estado:** aceptada · **Fase:** 5

### Decisión
Los 6 perfiles (`GENERAL`, `COMERCIO`, `SALUD`, `INFORMATICA`, `BODEGA`, `TALLER`) se materializan
como datos:

- `organizaciones.perfil` — perfil activo.
- `organizaciones.configuracion` (JSON) — preferencias (stock negativo permitido, umbral de
  vencimiento, etc.).
- `modulos_organizacion` — módulos habilitados y su configuración.

Un perfil **escribe configuración**, nunca borra datos ni altera movimientos históricos. Cambiar de
perfil solo habilita/deshabilita módulos y ajusta valores por defecto. El núcleo del inventario es
idéntico para todos los rubros.

### Consecuencias
- Un solo código base que vender. Un rubro nuevo = un perfil + configuración, no un fork.
- Los módulos no habilitados se ocultan en la navegación y bloquean la API por permiso, de modo que
  "ocultar" y "impedir" van juntos.

---

## ADR-006 — Migración multiempresa en pasos reversibles

**Estado:** aceptada · **Fase:** 3

### Secuencia

| Paso | Migración | Reversible | Riesgo |
|---|---|---|---|
| 1 | Crear `organizaciones`, `organizacion_usuario`, `organizacion_permisos`, `sucursales`, `bodegas`, `proveedores`, `modulos_organizacion` | sí | nulo |
| 2 | Añadir `organizacion_id` **nullable** + índices a `articulos`, `movimientos`, `categorias`, `unidad_medidas`, `personas`, `auditorias`, `contactos`, `conversaciones`, `mensajes` | sí | nulo |
| 3 | Backfill: crear organización principal y asignar todos los registros y usuarios | sí (truncable) | bajo |
| 4 | Unicidades: `articulos.codigo` → `(organizacion_id, codigo)`; ídem categorías, unidades, personas | sí | medio |
| 5 | `organizacion_id` → NOT NULL + FK `restrictOnDelete` | sí | bajo |
| 6 | Ampliar ENUM `movimientos.tipo`; añadir columnas de estado, bodega, idempotencia | sí | medio |

**Reglas invariables:**
- Ningún paso borra datos de negocio.
- `down()` de cada migración restaura el estado anterior.
- Antes del paso 4 se verifica que no existan duplicados por organización.
- El paso 3 **no** toca `users.email`: la credencial es global; la pertenencia se modela en
  `organizacion_usuario`.

---

## ADR-007 — Idempotencia y anulación en lugar de edición de movimientos

**Estado:** aceptada · **Fase:** 4

### Decisión
- Un movimiento **confirmado no se edita ni se borra**. Se anula.
- La anulación crea un movimiento compensatorio (`reversa_de_id`) y marca el original
  (`estado = ANULADO`, `anulado_por_id`, `anulado_at`). Queda traza completa: qué se anuló, quién y
  por qué.
- Cada formulario de movimiento emite un `idempotency_key` (token único por formulario). El
  movimiento se guarda con `unique(idempotency_key)`. Un reintento con la misma clave devuelve el
  movimiento ya creado en vez de duplicarlo.
- Las transferencias entre bodegas se registran como **un** movimiento con `bodega_origen_id` y
  `bodega_destino_id`: el total no cambia, el saldo por bodega sí.

### Consecuencias
- El historial es inmutable y auditable.
- El reverso es explícito: el Kardex muestra ambos movimientos y el motivo.
- Requiere que el índice único de `idempotency_key` tolere nulos (MySQL lo hace: varios `NULL` no
  chocan).

---

## ADR-008 — Límites numéricos alineados con el esquema

**Estado:** aceptada · **Fase:** 2

### Decisión
Toda validación numérica declara el rango exacto que acepta la columna:

| Campo | Columna | Validación |
|---|---|---|
| `cantidad` | `decimal(12,2)` | `numeric`, `gt:0`, `decimal:2`, `max:9999999999.99` |
| `costo_unitario` | `decimal(12,4)` | `numeric`, `min:0`, `decimal:4`, `max:99999999.9999` |
| `precio_venta` | `decimal(12,2)` | `numeric`, `min:0`, `decimal:2`, `max:9999999999.99` |
| `stock_minimo` | `decimal(12,2)` | `numeric`, `min:0`, `decimal:2`, `max:9999999999.99` |

Un valor fuera de rango produce **422 con mensaje en español**, nunca un 500 por `QueryException`.
El importador aplica los mismos límites y reporta el error por línea.

---

## ADR-009 — Identificador fiscal configurable, no hardcodeado

**Estado:** aceptada · **Fase:** 3

### Decisión
`organizaciones.identificador_tributario` (texto libre, hasta 50) +
`organizaciones.identificador_tipo` (lista configurable: `RUT`, `NIT`, `CUIT`, `RFC`, `NIF`,
`VAT`, `CUIT`, `TAX_ID`, otro).

El sistema **no** valida ni se conecta a ninguna autoridad tributaria: no existe integración
oficial. La etiqueta del formulario cambia según el país seleccionado y el tipo elegido. El README
debe declarar explícitamente que Invensys **no** emite comprobantes fiscales ni cumple
obligaciones tributarias.

---

## ADR-010 — Preparación para planes comerciales sin cobrar

**Estado:** aceptada · **Fase:** 9

### Decisión
Se prepara la **estructura** (no la funcionalidad) para planes, límites y funciones premium:

- `organizaciones.plan` (`enum`), nullable, por defecto `null` = sin restricciones.
- `organizacion_permisos` permite distinguir un permiso "premium" de uno incluido.
- Tabla `suscripciones` **no** se crea todavía: no hay requisitos de cobro definidos y no se
  implementan pagos, pasarelas ni restricciones ficticias.

La regla: **no se simulan capacidades de pago ni se bloquean funciones sin un requisito real.**

---

## ADR-011 — Pruebas de aislamiento obligatorias

**Estado:** aceptada · **Fase:** 3

### Decisión
La suite de pruebas incluye una clase dedicada que **intenta** acceder a datos de otra
organización por todos los caminos: listado, detalle por id, exportación, importación, API,
auditoría, kardex y reportes. Cada intento debe fallar con 403/404 y **nunca** devolver datos de
la otra organización.

Esta suite es un requisito de aceptación, no una cortesía: sin ella, la Fase 3 no está terminada.
