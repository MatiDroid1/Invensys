# Fase 1 — Diagnóstico del sistema Invensys

**Fecha:** 2026-10-21
**Alcance:** inspección completa del repositorio `Invensys` (Laravel 12) sin modificar código.
**Método:** lectura de código, ejecución de la suite de pruebas, verificación del estado real de
la base de datos en ejecución y auditoría de seguridad independiente.

---

## 1. Tecnologías detectadas

| Capa | Tecnología | Versión / nota |
|---|---|---|
| Framework | Laravel | 12.69.2 (`laravel/framework ^12.0`) |
| Lenguaje | PHP | 8.2.12 (ZTS, XAMPP) |
| Frontend | Blade + Alpine.js | Alpine 3.4+ (CDN o npm) |
| Estilos | Tailwind CSS | 3.1 + `@tailwindcss/forms` |
| Build | Vite | 7.0.7 + PostCSS + `laravel-vite-plugin` |
| Autenticación | Laravel Breeze 2.4 | Sesiones con Bcrypt; Sanctum instalado (API sin usar) |
| Base de datos | MySQL 8 / MariaDB 10.4 | En ejecución: MariaDB 10.4.32 (XAMPP) |
| Pruebas | Pest 3 + PHPUnit | SQLite `:memory:` en `phpunit.xml` |
| PDF | `barryvdh/laravel-dompdf` | 3.1 — Kardex |
| Consola | `laravel/tinker` | Diagnóstico interactivo |
| Formato | Laravel Pint | 1.24 (dev) |
| Entradas/salidas | CSV UTF-16LE con `sep=;` | Exportaciones e importación masiva |
| Sistema base | XAMPP / Apache en `C:\xampp\htdocs\Invensys` | `DocumentRoot` apunta al directorio **padre**, no a `public/` |

### Lo que NO existe

No hay: API REST real (solo `routes/api.php` con un `/user` de ejemplo), SPA, microservicios,
Redis/SQS,对象存储, pasarela de pago, ni integraciones externas de cualquier tipo. **Todo el
producto es un monolito Blade server-rendered.** Eso es correcto para el tamaño del proyecto y no
debe cambiarse.

---

## 2. Arquitectura actual

```
routes/web.php (41 rutas)
        │
        ▼
app/Http/Controllers/          ~27 controladores
        │   └── Concerns/ExportaCsv  (trait de exportación CSV)
        ▼
app/Http/Requests/             8 FormRequest (validación + autorización)
        ▼
app/Services/                  3 servicios: MovimientoService, AuditoriaService,
        │                       ImportacionArticulosService
        ▼
app/Models/                    10 modelos Eloquent
        ▼
MySQL/MariaDB  (12 tablas de dominio + migraciones de Laravel)
```

**Patrón real:** los controladores son "grasos" en validación y consulta; solo la lógica de negocio
de stock está correctamente extraída a `MovimientoService`. `AuditoriaService` es un envoltorio
fino y bien usado. `ImportacionArticulosService` mezcla parseo, validación y persistencia.

**Modelo mental del dominio actual:**

- `Articulo` = artículo (consumible genérico). El stock **no se guarda**: se deriva de la suma de
  movimientos (`Articulo::EXPRESION_STOCK`, `app/Models/Articulo.php:21-32`). Este es el diseño
  correcto y hay que conservarlo.
- `Movimiento` = asiento de movements con 4 tipos: `ENTRADA`, `SALIDA`, `AJUSTE_POSITIVO`,
  `AJUSTE_NEGATIVO` (`database/migrations/2026_09_29_114507_create_movimientos_table.php:15-20`).
- `Persona` = destinatario de entregas (no es un usuario).
- `User` = cuenta con `rol` de texto libre: `'admin'` o `'usuario'`.
- `Auditoria` = bitácora genérica (`modulo`, `accion`, `modelo`, `modelo_id`, `datos` JSON).
- Módulos **no** inventariados en el core: mensajería interna (`conversaciones`, `mensajes`) y
  bandeja de contacto (`contactos`). Son features dealer/producto, no de inventario.

**Existen dos características "huérfanas":**

1. `articulos.control_individual` (boolean) — se guarda, se muestra y se importa, pero **ningún
   código lo usa** para cambiar el comportamiento. Es una promesa al usuario que el sistema no
   cumple.
2. `users.rol` — solo dos valores, chequeados por un middleware binario (`EnsureUserIsAdmin`).

---

## 3. Funcionalidades existentes

### 3.1 Inventario (funcional y con buena base)

- Catálogo de artículos con código, nombre, descripción, categoría, unidad de medida, stock
  mínimo, precio de venta, costo promedio, control individual, activo.
- Búsqueda por texto, filtro por categoría y por estado, paginación (15 por página).
- **Stock derivado del historial**, no de un contador. `Articulo::scopeConStock()` (línea 78) evita
  el N+1 con un subquery correlacionado.
- Entradas, salidas y ajustes. Transaccionales, con `lockForUpdate()` sobre la fila del artículo.
- Costeo por **promedio ponderado**; cada movimiento congela su `costo_unitario`.
- Sin stock negativo en salidas ni en ajuste negativo.
- Kardex por artículo (HTML + PDF).
- Reportes: stock actual, inventario valorizado (CSV), movimientos por período, entregas por
  persona. Todos con export CSV.
- Importación masiva de artículos desde CSV con plantilla descargable, reporte de errores por
  línea y límite de 500 filas.

### 3.2 Seguridad (buena base, con huecos)

- Contraseñas con Bcrypt (`casts` `hashed`), nunca en texto plano.
- Registro público **desactivado** deliberadamente (documentado en `routes/auth.php:6-14`).
- Throttling: login 5/min, reset 3/min, mensajes 10/min, sondeo 60/min.
- Middleware `EnsureUserIsActive` que revisa `activo` en **cada** request (cierra el hueco de
  desactivation en caliente).
- Protección contra inyección de fórmulas en CSV (`ExportaCsv::neutralizarFormula`).
- CSRF activo en rutas web.
- Auditoría en todas las escrituras, excluyendo contraseñas.
- `.htaccess` bloquea el acceso web a `.env`, `app/`, `database/`, `config/`, `storage/` (verificado
  por HTTP: 403).
- Login con `remember me`, hash de contraseña con `Hash::check` real.
- **Sin SQL dinámico**: los 6 usos de `raw()` son constantes. **Sin `{!! !!}` con datos de
  usuario**. Sin IDOR en la mensajería (`abort_unless($conversacion->participa(...))`).

### 3.3 Interfaz

- Layout responsive con modo oscuro (persistido), 27 componentes Blade reutilizables
  (`x-tabla`, `x-pagina-form`, `x-estado-vacio`, `x-modal`, `x-alerta`, `x-dinero`,
  `x-tipo-movimiento`, `x-movimiento-campos`…).
- Paginación Tailwind, modales de confirmación, estados vacíos.
- Interfaz 100 % en español, incluidos los mensajes de validación.

### 3.4 Calidad

- **143 pruebas Pest pasando (521 aserciones), 82 s.** Suite amplia: inventario, reglas de negocio,
  costos, exportación, importación, seguridad, vistas, tema, auditoría, alertas de stock.
- `DiagnosticoController`: panel de comprobación de instalación (BD, migraciones, permisos de
  escritura, assets compilados, `APP_KEY`, `APP_DEBUG`, versión de PHP).
- `README.md`, `GUIA_ADMIN.md`, `MANUAL_USUARIO.md` ya existen y son extensos.

### 3.5 Funcionalidades incompletas o rotas

| # | Hallazgo | Ubicación | Impacto |
|---|---|---|---|
| B-1 | `personas` sin middleware `admin` (a diferencia de `categorias`, `unidades-medida`, `usuarios`) | `routes/web.php:62-63` | Cualquier usuario autenticado puede crear/editar/**desactivar** personas por HTTP. Además la función está **invisible en la UI** (`resources/views/personas/index.blade.php:89-96` solo ofrece "Editar"): endpoint muerto + hueco de autorización. |
| B-2 | Filtros se pierden al paginar | `ReporteController.php:220`, `:324` (falta `withQueryString()`) | El usuario filtra, salta de página y pierde el filtro. |
| B-3 | Entregas por persona no renderiza la paginación | `resources/views/reportes/entregas_persona.blade.php` (sin `->links()`) | Las filas sobre la 50 son inalcanzables. |
| B-4 | El encabezado usa `count()` (página actual) en vez de `total()` | `reportes/movimientos.blade.php:164`, `reportes/entregas_persona.blade.php:100` | Reporta "50 movimientos" cuando hay 3.000. |
| B-5 | Se muestra el enum crudo del movimiento | `kardex/index.blade.php:202`, `kardex/pdf.blade.php:145` | `AJUSTE_POSITIVO` en pantalla y en el PDF entregado al cliente. |
| B-6 | Auditoría y Diagnóstico ausentes en el menú móvil | `resources/views/layouts/navigation.blade.php:430-448` | Funciones de administración inalcanzables en móvil. |
| B-7 | `$mensajesNoLeidos` sin guarda nula | `navigation.blade.php:77, 79, 409, 411` | Error si la vista se renderiza sin composer. |
| B-8 | `movimientos/index` sin filtros | `resources/views/movimientos/index.blade.php` | No hay forma de buscar movimientos: es la pantalla central del sistema. |
| B-9 | Desactivar una categoría deja artículos imposibles de editar | `StoreArticuloRequest.php:36-46` exige `activo=true`; el `<select>` solo lista activas | Bloqueo operativo real. |
| B-10 | `control_individual` sin efecto alguno | `app/Models/Articulo.php:45` | Promesa incumplida al usuario. |

---

## 4. Problemas encontrados

### CRÍTICOS

**C-1 · Cuenta de administrador con contraseña `password` viva y alcanzable por la red.**
`Hash::check('password', ...)` → `true` para `admin@invensys.cl` en la base en ejecución. La
credencial viene de `.env:75` (`ADMIN_PASSWORD=password`). El seeder **no la corrige** en
reejecuciones: `database/seeders/UsuarioAdminSeeder.php:37` usa `firstOrNew` y `:45-50` solo valida
la contraseña cuando la cuenta *no* existe. Apache escucha en `0.0.0.0:80` y la app responde 200 en
`http://192.168.0.200/Invensys/public/login`; el tráfico es HTTP sin cifrar y la cookie de sesión
viaja sin marca `Secure` (`config/session.php:172`, `SESSION_SECURE_COOKIE` no definido).
→ **Rotar la clave ahora** y endurecer el seeder.

**C-2 · `APP_DEBUG=true` en una instancia que sirve tráfico de red.**
`.env:4`. `bootstrap/app.php:36-38` no registra manejador de excepciones propio. Cualquier error
500 expone mensaje completo, SQL, traza con extractos del código fuente y rutas absolutas. Existe un
disparador trivial controlado por el usuario (importar un CSV con un número fuera de rango).

**C-3 · Autorización ausente en catálogo, personas e inventario.**
Sin `admin`: `articulos` (resource completa), `movimientos` (resource + salida + ajuste),
`personas` (resource completa). Un usuario con rol `usuario` puede: crear/desactivar artículos,
crear personas, registrar entradas, salidas y **ajustes de stock en ambos sentidos**, y leer
kardex y reportes. El middleware `EnsureUserIsAdmin` existe y funciona; simplemente no se aplica.

**C-4 · Un usuario sin privilegios puede manipular el costo y falsear la valorización.**
`StoreMovimientoRequest.php:27-31` y `StoreAjusteRequest.php:34-38` aceptan `costo_unitario`;
`UpdateArticuloRequest.php:57-61` acepta `precio_venta`. `cantidad` solo tiene `numeric|gt:0`, sin
máximo. Una entrada de 1 unidad a costo 1.000.000 desvía el costo promedio y por tanto el
inventario valorizado que se entrega al contador.

### ALTOS

**A-1 · La importación CSV borra datos silenciosamente.** `ImportacionArticulosService.php:344-354`
convierte columna ausente/vacía en `null`, y `:353` fuerza `activo = true`; `:393-406` hace
`update($datos)` con esos valores. Resultado: al reimportar un CSV con solo las columnas
obligatorias se **vacían todas las descripciones y precios de venta** y se **reactivan artículos**
desactivados a propósito. Sin aviso, sin vista previa.

**A-2 · La importación no es atómica.** `ImportacionArticulosService.php:80-97` guarda fila por
fila, fuera de transacción, y solo captura `\InvalidArgumentException`. Un `QueryException` (p. ej.
`precio_venta` fuera del rango `decimal(12,2)`) aborta el bucle con las filas anteriores **ya
confirmadas** y devuelve 500 en vez del reporte de errores por línea.

**A-3 · Los movimientos no son idempotentes ni anulables.** No hay token de idempotencia, ni
restricción única, ni rutas `PUT/DELETE` de movimientos, ni mecanismo de anulación/reversión
(`grep anul|revert|revers` en `app/` → 0 coincidencias). Un doble clic, un reintento de red o un
refresh con reenvío **crea dos movimientos**, y no hay forma de corregirlo salvo un ajuste manual
que no deja rastro del error.

**A-4 · `stockBajo()` se ejecuta en cada request y filtra en PHP.** `AppServiceProvider.php:26-35`
(la vista `layouts.app` está en todas las pantallas) + `Articulo::stockBajo()` (`:164-173`) que
materializa **todos** los artículos activos y filtra en PHP. `DashboardController.php:27-32` lo
repite y carga todo el catálogo para valorizar.

**A-5 · Consultas sin paginación con riesgo de agotar memoria.**
`ReporteController.php:17-25` y `:51-59` (todo el catálogo, HTML y CSV), `:239-258` y `:341-358`
(todos los movimientos para construir el CSV), `KardexController.php:55-62` (todos los movimientos
de un artículo), `MovimientoController.php:32-34, 68-77, 113-117` (todos los artículos para los
`<select>`), `PersonaController.php:19-21`, `UsuarioController.php:20`, `ContactoController.php:18-21`.

**A-6 · Validación numérica sin máximos, alineada con la escala de las columnas.**
`cantidad`, `stock_minimo`, `costo_unitario`, `precio_venta` solo validan `min:0` / `gt:0`, pero las
columnas son `decimal(12,2)` / `decimal(12,4)`. Un valor fuera de rango produce `QueryException` →
500 con página de depuración. El importador tiene el mismo hueco (`:314-322`).

**A-7 · Sin control de fechas: se puede desplazar el stock en el tiempo.**
`fecha_movimiento` solo se valida como `date`. El stock **ignora las fechas** (`Articulo:21-32`):
una entrada futura suma existencias de inmediato; una salida retroactiva no reduce el stock
"actual" pero produce saldo negativo acumulado en el Kardex (`KardexController.php:64-87`). El
resultado son decisiones de reposición erróneas.

**A-8 · Borrar la cuenta propia destruye el historial de chat.**
`ProfileController.php:43-74` hace borrado físico si el usuario no tiene movimientos;
`conversaciones` y `mensajes` tienen `cascadeOnDelete`. En `auditorias` y `contactos` el
`usuario_id` queda `NULL`.

### MEDIOS

- **M-1** — La protección de `.env` depende del `.htaccess` del directorio **padre compartido**
  (`C:\xampp\htdocs\.htaccess` no existe). Si el `.htaccess` se renombra o el `DocumentRoot` cambia,
  quedan expuestos `APP_KEY`, código y migraciones. `README.md`, `server.php` y los configs de
  Tailwind/PostCSS no están bloqueados.
- **M-2** — Política de contraseñas débil: solo longitud mínima 8
  (`UsuarioController.php:35`, `:80`; `RestablecerClave.php:114-125`).
- **M-3** — La recuperación de contraseña por correo **no funciona**: `MAIL_MAILER=log` escribe el
  enlace en `storage/logs/laravel.log` en texto plano. El comando `usuario:clave` sí funciona.
- **M-4** — Desactivar una categoría o unidad deja artículos sin poder editarse (ver B-9).
- **M-5** — Índices faltantes: `movimientos` no tiene índice sobre `fecha_movimiento` y los reportes
  filtran con `whereDate(...)` (no sargable → filesort); `articulos` no tiene índice en `nombre` ni
  `activo`; `personas` no tiene índice en `apellido, nombre` ni `activo`.
- **M-6** — Cookie de sesión sin `Secure` y sin cifrar (`SESSION_ENCRYPT=false`).
- **M-7** — MariaDB escucha en `0.0.0.0:3306` con `root` **sin contraseña**
  (`netstat` verificado). Hoy el acceso remoto está bloqueado por la columna `Host`, no por
  credenciales: cualquier `GRANT` futuro a `%` queda expuesto de inmediato.
- **M-8** — `usuario:clave --clave=` deja la contraseña en el historial del shell y en `ps`.
- **M-9** — Bandeja de chat y primera carga de mensajes sin acotar
  (`ConversacionController.php:28-39`, `MensajeController.php:106-113`).
- **M-10** — La auditoría guarda payloads completos, incluido el resultado literal de la
  importación con los errores del CSV (`ArticuloImportacionController.php:45-54`). Volumen y
  posible dato sensible. Sin política de retención.

### BAJOS

- L-1 `costo_promedio` está en `$fillable` aunque ninguna ruta web lo valida.
- L-2 `getStockActualAttribute()` hace una consulta por artículo si no existe `stock_calculado`.
- L-3 `stockBajo()` usa `<=`: un artículo con `stock_minimo = 0` y stock 0 aparece como "stock bajo".
- L-5 `mensaje` del formulario de contacto sin `max`.
- L-6 `session()->flash('importacion', ...)` guarda hasta 500 errores en la sesión.
- L-7 `MovimientoService::stockActual()` no comprueba `null` → 500 si el artículo desaparece entre
  la validación y el servicio.
- L-8 `KardexController@pdf` valida `exists` sin `activo` pero `prepararKardex` filtra por `activo`
  → 404 en artículos inactivos.
- L-9 El importador corta filas CSV con comillas multilínea (`preg_split` por saltos de línea).
- L-10 `neutralizarFormula` no cubre un `=` precedido por espacios.
- L-11 Unicidad global (`articulos.codigo`, `categorias.nombre`, `unidad_medidas.nombre`,
  `personas.identificador`) — rompe con un segundo tenant. Ver sección 7.
- L-12 3 componentes Blade muertos (`dropdown`, `dropdown-link`, `nav-dropdown-link`) y un
  `vendor/pagination/simple-tailwind.blade.php` sin uso.

### Verificaciones que resultaron **negativas** (sin hallazgos)

Sin inyección SQL (0 usos de SQL dinámico con entrada del usuario). Sin XSS reflejado
(`{!! !!}` solo en traducciones de paginación y en un icono SVG literal). Sin IDOR en la mensajería.
Sin CSRF ausente. Contraseñas nunca en texto plano y excluidas de la auditoría. Sin dependencia
vulnerable crítica detectada. `.env` correctamente ignorado por git.

---

## 5. Riesgos de modificar el sistema

| # | Riesgo | Mitigación |
|---|---|---|
| R-1 | **Romper el cálculo de stock.** Es la regla de negocio más valiosa y está implícita en `Articulo::EXPRESION_STOCK` y en `KardexController`. | No tocar la expresión ni la fórmula de `promedioTrasEntrada` sin pruebas que la fijen. |
| R-2 | **Perder datos al añadir `organization_id`.** Una columna NOT NULL sin default sobre tablas con datos falla. | Esquema en 3 pasos: (a) crear tablas nuevas, (b) añadir `organizacion_id` **nullable** + backfill, (c) en una migración posterior volverlo NOT NULL. Cada paso reversible. |
| R-3 | **Cambiar unicidad global a compuesta.** `articulos.codigo` es único global; al pasar a `(organizacion_id, codigo)` hay que quitar el índice global y crear el compuesto. | Migración que dropea y recrea; verificar que no haya duplicados por organización antes. |
| R-4 | **Romper la suite actual (143 pruebas).** Muchas asertan comportamiento actual. | Las 143 deben seguir verdes **antes** de la Fase 3. Las pruebas que cambien de semántica se reescriben de forma explícita, no se silencian. |
| R-5 | **Colisión de nombres con lo ya existente.** `UnidadMedida`, `Persona`, `Auditoria` ya existen. | Reutilizar, no duplicar: las bodegas, proveedores y artículos extensionados se **agregan** a la tabla actual. |
| R-6 | **El trabajo en curso sin commitear.** Hay 27 archivos modificados + 1 migración nueva + 1 test nuevo (`2026_10_20_000001_add_costos_a_articulos_y_movimientos.php`, `tests/Feature/CostosTest.php`). | Se commiteará ese trabajo **antes** de tocar la arquitectura, para tener un punto de retorno. |
| R-7 | **Scope global por sesión rompe los comandos de consola y las colas.** | El contexto de organización se resuelve desde un servicio *scoped*, con explícito `sinOrganizacion()` para consola/seeders, y las colas rehidratan el contexto desde `organization_id`. |
| R-8 | **Filtrado por rol en la UI sin filtrado en el backend.** El error C-3 ya está. | La autorización se aplica en **backend** (Policies + Gates). La UI solo refleja. |
| R-9 | **Costeo: tocar `promedioTrasEntrada` altera el valor histórico.** | El costo promedio se congela por movimiento; los reportes valorizados dependen del promedio actual. Cualquier cambio necesita un test de conciliación. |

---

## 6. Cambios necesarios en la base de datos

### 6.1 Nuevas tablas

| Tabla | Propósito | Columnas clave |
|---|---|---|
| `organizaciones` | Cada empresa cliente | `id`, `nombre_comercial`, `razon_social`, `identificador_tributario`, `identificador_tipo`, `giro`, `tipo_organizacion`, `perfil`, `logo_path`, `direccion`, `pais`, `region`, `moneda`, `zona_horaria`, `idioma`, `email_contacto`, `telefono`, `prefijo_documentos`, `estado`, `configuracion` (JSON) |
| `organizacion_usuario` | Membresía (usuario ↔ organización) con rol | `id`, `organizacion_id`, `usuario_id`, `rol`, `activo`; **único** `(organizacion_id, usuario_id)` |
| `organizacion_permisos` | Ajustes de permisos por organización | `id`, `organizacion_id`, `rol`, `permiso`, `permitido`; **único** `(organizacion_id, rol, permiso)` |
| `sucursales` | Sucursales de la organización | `id`, `organizacion_id`, `nombre`, `codigo`, `direccion`, `activo` |
| `bodegas` | Bodegas / ubicaciones de almacenamiento | `id`, `organizacion_id`, `sucursal_id` (null), `nombre`, `codigo`, `direccion`, `predeterminada`, `activo` |
| `proveedores` | Proveedores | `id`, `organizacion_id`, `nombre`, `rut`/`identificador`, `contacto`, `email`, `telefono`, `direccion`, `activo` |
| `modulos_organizacion` | Módulos habilitables por organización | `id`, `organizacion_id`, `clave`, `habilitado`, `config` (JSON); **único** `(organizacion_id, clave)` |

### 6.2 Columnas añadidas a tablas existentes

| Tabla | Columnas nuevas | Nota |
|---|---|---|
| `articulos` | `organizacion_id` (null→NOT NULL), `sucursal_id`, `tipo` (enum `CONSUMIBLE`/`ACTIVO`), `sku`, `marca`, `modelo`, `codigo_barras`, `imagen_path`, `proveedor_id`, `ubicacion`, `numero_serie`, `fecha_adquisicion`, `garantia_meses`, `estado_activo` | El stock, el costo y los movimientos **no se tocan**: se derivan. |
| `movimientos` | `organizacion_id`, `bodega_origen_id`, `bodega_destino_id`, `estado` (`CONFIRMADO`/`ANULADO`), `idempotency_key` (**único**), `motivo`, `tipo_documento`, `documento_id`, `reversa_de_id`, `anulado_por_id`, `anulado_at` | `tipo` es ENUM: se amplía con `DEVOLUCION`, `TRANSFERENCIA`, `BAJA`, `MERMA`, `PRESTAMO`, `DEVOLUCION_PRESTAMO`. |
| `categorias`, `unidad_medidas`, `personas`, `auditorias`, `contactos` | `organizacion_id` | Migración simple. |
| `conversaciones`, `mensajes` | `organizacion_id` | Para que el chat no mezcle organizaciones. |
| `users` | `organizacion_actual_id` (null) | Sesión multi-tenant: con qué organización está operando. |

### 6.3 Cambios de restricciones

- `articulos`: `unique(codigo)` → `unique(organizacion_id, codigo)`.
- `categorias`: `unique(nombre)` → `unique(organizacion_id, nombre)`.
- `unidad_medidas`: `unique(nombre)` → `unique(organizacion_id, nombre)`.
- `personas`: `unique(identificador)` → `unique(organizacion_id, identificador)`.
- `users.email` **se mantiene global**: es la credencial de acceso a la plataforma, no un dato de
  negocio. Un usuario puede pertenecer a varias organizaciones con la misma cuenta.

### 6.4 Estrategia de migración (no destructiva)

```
Paso 1  Crear tablas nuevas (organizaciones, membresías, permisos, sucursales, bodegas,
        proveedores, módulos). Nada que ver con datos existentes.
Paso 2  Añadir `organizacion_id` NULLABLE + índices a las 7 tablas existentes.
Paso 3  Backfill: crear la organización "principal" y asignarle todos los registros
        existentes y a todos los usuarios actuales como `propietario`.
Paso 4  Cambiar unicidades a compuestas (dropea la global, crea la compuesta).
Paso 5  Añadir NOT NULL + FK a `organizacion_id` (ya no queda ningún NULL tras el backfill).
Paso 6  Ampliar el ENUM de `movimientos.tipo` y añadir columnas de estado/bodega.
```

Cada paso es una migración independiente, reversible y verificable. **En ningún paso se borran
datos.** Si un paso falla, se revierte con `php artisan migrate:rollback --step=N`.

---

## 7. Propuesta de arquitectura multiempresa

### 7.1 Decisión de aislamiento

**Base de datos compartida con separación lógica por `organization_id`.**

Motivos, en el contexto de este proyecto:

| Alternativa | Evaluación |
|---|---|
| BD por cliente | Explosión operativa: 1 migración por cliente, 1 respaldo por cliente, imposible operar N instalaciones desde un solo despliegue. Descartada para el objetivo "instalable en servidor de cada cliente" **y** "SaaS multi-tenant". |
| Esquema por cliente | Mismo problema de operativa, con migraciones por esquema. Descartada. |
| **BD compartida + `organization_id`** | Una migración, un respaldo, una actualización. El aislamiento lo impone la capa de aplicación (global scope + validación explícita). Es el estándar para SaaS de este tamaño. **Elegida.** |

El aislamiento **jamás** dependerá del frontend. Tres capas de defensa:

1. **Global scope Eloquent** en todos los modelos multi-tenant: la consulta *siempre* filtra por la
   organización activa.
2. **Asignación forzada:** `organization_id` **no** es `fillable` en ningún modelo. Se asigna
   siempre desde el contexto de la aplicación, nunca desde el request.
3. **Comprobación explícita en cada operación sensible**: al resolver un modelo por id se verifica
   además que pertenece a la organización activa (protección frente a fugas por forgetting de
   scopes, eager loads y relaciones).

### 7.2 Contexto de organización

Un servicio *scoped* `OrganizacionActual`:

- Se resuelve en el middleware `organizacion.activa` desde `session('organizacion_id')`.
- Verifica que el usuario autenticado **sea miembro activo** de esa organización; si no → 403.
- Si el usuario tiene varias organizaciones, un selector en la cabecera cambia la sesión.
- Para consola, colas y seeders: `OrganizacionActual::sinOrganizacion()` desactiva el scope
  explícitamente; las colas rehidratan el contexto desde el `organization_id` del trabajo.

### 7.3 Roles y permisos

Roles por organización (`organizacion_usuario.rol`):

`propietario` · `administrador` · `encargado_inventario` · `bodeguero` · `compras` · `auditor` ·
`consulta`

Permisos atómicos (backend con Laravel Policies + Gates):

`articulos.ver` · `articulos.crear` · `articulos.editar` · `articulos.eliminar` ·
`movimientos.entrada` · `movimientos.salida` · `movimientos.ajuste` · `movimientos.anular` ·
`proveedores.gestionar` · `compras.gestionar` · `costos.ver` · `costos.editar` ·
`usuarios.gestionar` · `configuracion.ver` · `configuracion.editar` ·
`auditoria.ver` · `exportar` · `importar`

- El **mapa por defecto** de cada rol vive en código (`App\Enums\RolOrganizacion`) y está
  documentado.
- `organizacion_permisos` guarda las **anomalías** por organización (rol, permiso, permitido).
  Así cada organización puede abrir o cerrar sin tocar código.
- La UI usa `@can` / `Gate::allows` para **ocultar** acciones; la validación real está en el
  backend. Ocultar botones nunca es la protección.

### 7.4 Configuración de empresa

Módulo `Configuración de empresa` (ruta `/configuracion/empresa`), con asistente de 4 pasos para
organizaciones nuevas:

1. **Identidad** — nombre comercial, razón social, identificador tributario (con tipo
   configurable: RUT, NIT, CUIT, RFC, NIF, VAT…), giro, tipo de organización, país/región, logo,
   dirección, contacto, moneda, zona horaria, idioma.
2. **Perfil de rubro** — 6 perfiles predefinidos + personalizado:
   - `GENERAL` — Inventario general
   - `COMERCIO` — Comercio o pyme
   - `SALUD` — Centro de salud
   - `INFORMATICA` — Empresa informática
   - `BODEGA` — Bodega y distribución
   - `TALLER` — Taller y mantenimiento

   Cada perfil define: módulos habilitados, tipos de movimiento extra, categorías y unidades de
   medida sugeridas, campos opcionales visibles y alertas por defecto.
3. **Estructura** — sucursales, bodegas, stocking mínimo por defecto, reglas de stock negativo.
4. **Equipo** — alta de usuarios con rol.

**Cambiar de perfil nunca borra datos ni altera movimientos históricos.** El perfil escribe
*configuración* (`modulos_organizacion` + `organizaciones.configuracion`), no registros. Al
cambiar de perfil solo se activan/desactivan módulos y se ajustan valores por defecto.

### 7.5 Estructura de módulos de código (Fase 3+)

```
app/
├── Enums/            RolOrganizacion, Permiso, PerfilOrganizacion, TipoMovimiento, TipoArticulo
├── Models/           (los actuales + Organizacion, OrganizacionUsuario, Sucursal, Bodega, ...)
├── Middleware/       EnsureUserIsAdmin, EnsureUserIsActive, OrganizacionActiva
├── Policies/         ArticuloPolicy, MovimientoPolicy, UsuarioPolicy, OrganizacionPolicy, ...
├── Services/         OrganizacionActual, ConfiguracionEmpresa, PerfilOrganizacion
│                      MovimientoService, StockService, LoteService, CompraService, AuditoriaService
├── Http/Controllers/
│   ├── Organizaciones/  OrganizacionController, ConfiguracionEmpresaController, BodegaController...
│   └── (los actuales)
└── Console/Commands/  (los actuales + CrearOrganizacion, Respaldar, Restaurar)
```

### 7.6 Ejes que NO se cambiarán

- **Stock derivado de movimientos.** Es correcto, auditable y no requiere una tabla de saldos que
  pueda desincronizarse.
- **Monolito Blade server-rendered.** Reemplazar el frontend por SPA/Vue/React sería reescribir el
  producto sin ganancia funcional para este caso de uso.
- **CSV como formato de intercambio.** Es lo que el usuario final (Excel) ya tiene.
- **MySQL + SQLite en pruebas.** Sin cambio.

---

## 8. Plan priorizado de implementación

| Fase | Alcance | Depende de | Complejidad |
|---|---|---|---|
| **1. Diagnóstico** | Este documento + documento de arquitectura. | — | **Baja** ✅ |
| **2. Fundamentos** | Commit del trabajo en curso. Cerrar C-1..C-4, A-1, A-2, A-3, A-6. Arreglar B-1..B-10. Límites numéricos alineados con columnas. Política de contraseñas. Lint. Pruebas de regresión de los hallazgos. | 1 | **Media** ✅ → [informe](03_FASE2_FUNDAMENTOS.md) |
| **3. Multiempresa** | Tablas de organización, membresías, permisos, sucursales, bodegas. Global scope + contexto. Policies y Gates. Selector de organización. Asistente de Configuración de empresa con los 6 perfiles. | 2 | **Alta** |
| **4. Núcleo de inventario** | Bodegas en movimientos. Tipos extendidos (devolución, transferencia, baja, merma, préstamo). Anulación/reversión auditada. Idempotencia por token. Política de fechas. Transferencias que no crean ni destruyen stock. Conciliación stock ↔ historial. | 3 | **Alta** |
| **5. Configuración por rubro** | Campos opcionales por tipo (serial, lote, vencimiento, garantía, responsable). Catálogo adaptativo. Activación/desactivación de módulos y su efecto en la UI. | 3, 4 | **Media** |
| **6. Compras y operaciones avanzadas** | Proveedores. Órdenes de compra, recepciones parciales, documentos diferenciados. Lotes y vencimientos con FEFO y alertas. Activos: unidades individuales, asignación, préstamos. | 4, 5 | **Alta** |
| **7. Interfaz profesional** | Dashboard con KPIs por perfil y filtros. Navegación por permisos y módulos. Componentes `<x-campo>` / `<x-tabla>` / `<x-filtros>`. Confirmaciones, estados de carga, accesibilidad, responsive. | 3, 5 | **Media** |
| **8. Calidad y seguridad** | Pruebas de aislamiento entre organizaciones. Pruebas de permisos por rol. Pruebas de los flujos principales. Revisión de dependencias. Ajustes de rendimiento (índices, `stockBajo` en SQL, paginación). | 3-7 | **Media** |
| **9. Preparación comercial** | README profesional, `.env.example` saneado, backup/restauración verificados, checklist de lanzamiento, notas de arquitectura, perfilado de planes preparado (sin cobrar). | 8 | **Media** |

### Orden interno de la Fase 2 (primera tanda de código)

1. Commit del trabajo en curso (`costos`) → punto de retorno.
2. `.env.example` saneado + seeder que **siempre** valida la contraseña.
3. Policies + Gates; proteger artículos, movimientos, personas, costos.
4. Importación: no sobrescribir columnas ausentes + transacción + vista previa.
5. Idempotencia de movimientos (token único) + anulación auditada.
6. Límites numéricos alineados con las columnas.
7. Arreglos de UI B-1..B-10.
8. Pruebas nuevas para cada hallazgo cerrado.

---

## 9. Pruebas que deben crearse o corregirse

### Ya existen y deben seguir verdes (143, 521 aserciones)
`AlertasStockTest` · `AuthenticationTest` · `CostosTest` · `DiagnosticoTest` · `ExportacionesTest` ·
`ImportacionArticulosTest` · `InventarioTest` · `MensajesTest` · `ReglasNegocioTest` ·
`ReportesCsvTest` · `RestablecerClaveTest` · `SeguridadTest` · `TemaYAcercaTest` · `VistasTest` ·
`Auth\*`

### Deben crearse en la Fase 2

| Test | Qué fija | Estado |
|---|---|---|
| `PermisosTest` | Un rol `consulta` no puede crear artículos; `bodeguero` no puede tocar costos; `auditor` no puede escribir movimientos. | ✅ Creado (adaptado a los dos roles reales: `admin` y `usuario`) |
| `IdempotenciaMovimientosTest` | Un doble POST con el mismo token genera **un** movimiento. | ✅ Creado |
| `AnulacionMovimientoTest` | Anular compensa y queda auditado; el stock vuelve al valor previo. | ✅ Creado |
| `TransferenciaTest` | Una transferencia entre bodegas no crea ni destruye stock total. | ⏭️ Fase 4 (requiere bodegas) |
| `ImportacionNoDestructivaTest` | Reimportar un CSV sin `descripcion` **no** borra la descripción existente. | ✅ Creado dentro de `ImportacionSeguraTest` |
| `ImportacionAtomicaTest` | Un error en la fila N revierte toda la importación. | ✅ Creado dentro de `ImportacionSeguraTest` |
| `LimitesNumericosTest` | Un cantidad o costo fuera del rango de la columna se rechaza con 422, no con 500. | ✅ Creado |
| `FechasMovimientoTest` | Una entrada futura no suma stock; una salida retroactiva no produce saldo negativo en el Kardex. | ⏭️ Fase 4 (política de fechas) |
| `AuditoriaCompletaTest` | Toda escritura deja rastro con antes/después y sin secretos. | ⏭️ Fase 4 |

### Deben crearse en la Fase 3 (multiempresa) — **críticas**

| Test | Qué fija |
|---|---|
| `AislamientoOrganizacionTest` | El usuario de la organización A **no** puede leer, listar, exportar, mover ni auditar datos de la B, ni siquiera adivinando un id. |
| `OrganizacionIdNoForjableTest` | Enviar `organization_id` en el cuerpo de la petición **no** cambia la organización destino. |
| `MembresiaRequeridaTest` | Un usuario sin membresía de la organización solicitada recibe 403, no datos. |
| `PermisosPorOrganizacionTest` | Las anomalías de permisos de la organización A no afectan a la B. |
| `MultiplesOrganizacionesTest` | Una misma cuenta opera en dos organizaciones y no ve la mezcla. |
| `DatosExistentesMigradosTest` | Tras la migración, los datos previos pertenecen a la organización principal y siguen intactos. |

### Deben crearse en la Fase 4+
`LotesTest` (FEFO, alertas de vencimiento, historial por lote) · `ComprasTest` (órdenes vs.
recepciones vs. facturas) · `ActivosTest` (asignación, préstamo, devolución, estado) ·
`ConciliacionTest` (stock materializado == stock derivado del historial).

---

## 10. Complejidad estimada por fase

| Fase | Complejidad | Justificación |
|---|---|---|
| 1. Diagnóstico | **Baja** | Lectura y verificación. Hecha. |
| 2. Fundamentos | **Media** | Cambios localizados en controladores, FormRequests, un servicio y la UI. Sin cambio de esquema de datos (salvo el enum de movimientos). Riesgo alto de regresión → mitigated con pruebas. |
| 3. Multiempresa | **Alta** | Toca el modelo de datos, el aislamiento de todas las consultas, la autorización de todas las rutas, la sesión y la UI. Un error de ámbito filtra datos de otro cliente: es el punto de mayor riesgo del proyecto. |
| 4. Núcleo de inventario | **Alta** | Amplía el ENUM de movimientos, agrega bodegas, idempotencia y anulación. La concurrencia y la trazabilidad exigen pruebas específicas. |
| 5. Configuración por rubro | **Media** | Principalmente formularios adaptativos y configuración. Sin cambio estructural de la base. |
| 6. Compras y lotes | **Alta** | Modelo documental nuevo (OC ≠ recepción ≠ factura), lotes con FEFO, activos individualizados. Volumen alto de código nuevo y de casos de borde. |
| 7. Interfaz | **Media** | Mejora incremental sobre un sistema de diseño ya existente y consistente. Riesgo bajo, impacto alto en percepción de producto. |
| 8. Calidad y seguridad | **Media** | Trabajo de pruebas y corrección; depende de que 3-7 estén estables. |
| 9. Preparación comercial | **Media** | Documentación, scripts de respaldo, checklist. Bajo riesgo técnico, alto valor comercial. |

---

## 11. Conclusión de la Fase 1

El proyecto **no necesita ser reescrito**. Tiene una base sólida: stock derivado del historial
(diseño correcto y auditable), suite de pruebas amplia (143), CSV robusto, auditoría presente,
protección CSRF y anti-inyección de fórmulas, y un sistema de diseño consistente.

Lo que falta para convertirlo en un producto comercial es, en orden de importance:

1. **Autorización real** — cerrar el hueco C-3 y C-4. Hoy el control de acceso es decorativo.
2. **Multiempresa** — la tabla `organizaciones` y el aislamiento en backend (Fase 3).
3. **Integridad del inventario** — idempotencia, anulación/reversión, transferencia entre bodegas,
   control de fechas, límites numéricos.
4. **Configuración por rubro** — perfiles y módulos sobre un núcleo común, sin duplicar productos.
5. **Operación segura** — credencial por defecto, `APP_DEBUG` en red, política de contraseñas,
   backup verificado.

El orden propuesto (2 → 3 → 4) es correcto: la autorización primero, porque mientras no exista
separación entre organizaciones, un fallo de autorización es una fuga de datos entre clientes.

**Riesgo residual principal:** la Fase 3. El aislamiento por `organization_id` es inviable si
alguna consulta olvida el filtro. Se mitiga con global scope **más** validación explícita **más**
pruebas de aislamiento que intenten acceder a datos ajenos.
