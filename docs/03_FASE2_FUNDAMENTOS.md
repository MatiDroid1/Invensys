# Fase 2 — Fundamentos: permisos, idempotencia, anulación, importación segura y límites

**Estado:** completada.
**Complementa:** [`01_DIAGNOSTICO.md`](01_DIAGNOSTICO.md) y [`02_ARQUITECTURA_MULTIEMPRESA.md`](02_ARQUITECTURA_MULTIEMPRESA.md).

La Fase 2 deja el sistema en condiciones de venderse como producto: cierra los riesgos que
encontró el diagnóstico y, sobre todo, deja la **decisión de autorización en un solo lugar**, que
es lo que permite que en la Fase 3 el aislamiento por organización se añada sin reescribir
controladores.

---

## 1. Qué se hizo

| Frente | Antes | Ahora |
|---|---|---|
| Autorización | Middleware `admin` por rol | Permiso por acción, decidido en un único servicio |
| Costos | Cualquiera con acceso al formulario los podía cambiar | Solo quien administra; el campo ni siquiera se envía |
| Movimientos | Un doble clic podía registrar dos entradas | Clave de idempotencia: el reintento no duplica |
| Corrección | Ajuste manual en sentido contrario | Anulación con motivo, auditada y con compensatorio automático |
| Importación CSV | Sobrescribía columnas vacías, sin revisión | No destructiva, atómica y con previsualización |
| Números | Un valor desbordado daba un error 500 | Topes numéricos con mensaje de validación |

---

## 2. Capa de permisos

### Vocabulario

`app/Enums/Permiso.php` enumera las acciones que el sistema sabe autorizar. No hay cadenas
sueltas por ahí: cada permiso existe una vez y se nombra una vez.

### Fuente única de verdad

`app/Services/Permisos.php` es el **único** lugar donde se decide qué puede hacer un usuario.

```php
Permisos::de($usuario)   // lista de permisos
Permisos::puede($usuario, Permiso::CostosEditar)  // pregunta puntual
User::puede(Permiso::Importar)   // atajo en el modelo
```

De ahí salen, sin duplicar la lógica:

- **Gates** (`AppServiceProvider`): `Gate::allows('articulos.editar')`, y por lo tanto
  `@can('articulos.editar')` en las plantillas.
- **Middleware** `permiso:<permiso>` (`EnsureUserHasPermission`), registrado con el alias
  `permiso` en `bootstrap/app.php`. Devuelve **403** si el usuario no tiene el permiso, y
  redirige al login si no hay sesión.
- **Policies** (`app/Policies/`) para las comprobaciones a nivel de modelo.

### Rutas

`routes/web.php` dejó de usar `Route::resource`: ahora cada ruta declara el permiso que exige.
Los **nombres de ruta no cambiaron**, así que las vistas y los tests siguen funcionando igual.

```php
Route::get('/articulos/{articulo}/edit', [ArticuloController::class, 'edit'])
    ->middleware('permiso:articulos.editar')
    ->name('articulos.edit');
```

### Reparto por rol

| | `usuario` (operador) | `admin` |
|---|:---:|:---:|
| Operar inventario (artículos, movimientos, personas, kardex, reportes) | Sí | Sí |
| Consultar costos | Sí | Sí |
| **Modificar costos** | **No** | Sí |
| **Administrar** usuarios, categorías, unidades, configuración | **No** | Sí |
| **Importar** artículos | **No** | Sí |
| Ver **auditoría**, contacto, diagnóstico | **No** | Sí |

**Decisión de fondo:** el rol `usuario` **conserva** todo lo que podía hacer antes (operar el
inventario y mantener el catálogo). Lo único que se le restringe es lo que falsea números que se
entregan a un tercero: el costo. Un operador puede registrar entradas sin costo; lo que no puede
es fijar el costo ni el precio de venta.

**Decisión de fondo 2:** el operador **sí** puede anular movimientos. Quien registra un movimiento
también necesita corregirlo, y la anulación no borra nada: genera un compensatorio y queda
auditada. El riesgo está controlado por diseño, no por ocultar el botón.

### Dónde se comprueba el costo

En la **capa HTTP** (`FormRequest::withValidator`), nunca en `MovimientoService`. El servicio se
invoca también desde procesos internos y tests que no pasan por un formulario; meter la
comprobación ahí rompería esos usos legítimos.

El formulario tampoco envía el campo si el usuario no puede modificarlo (no basta con ocultarlo:
un campo oculto que se sigue enviando dispara el error "campo requerido" al validarlo).

---

## 3. Idempotencia de los movimientos

### El problema

Un doble clic, un botón que se queda pulsado o un reintento del navegador dejaban dos entradas
donde había una. En un inventario eso significa stock inflado sin que nadie lo note.

### La solución

Cada movimiento lleva una **clave de idempotencia** (`idempotency_key`, 64 caracteres, única y
opcional). El formulario la genera con `Str::uuid()` y la conserva con `old()` si la validación
falla, para que un reintento use la misma clave.

- `MovimientoService::crearMovimiento()` busca primero un movimiento con esa clave; si lo
  encuentra, lo devuelve tal cual en vez de crear otro.
- Si la clave no existe y dos peticiones llegan a la vez, la restricción `UNIQUE` de la base de
  datos es la que decide: la segunda recibe la violación de unicidad, la detecta
  (`esViolacionDeUnicidad()`) y devuelve el movimiento que la primera creó.

El resultado es que **la garantía final es la base de datos**, no el código de aplicación: es lo
único que puede arbitrar entre dos peticiones simultáneas.

---

## 4. Anulación de movimientos

### La decisión

Un movimiento confirmado **no se borra ni se edita**. Se anula.

La razón es la misma que Justifica que el stock se derive de los movimientos: si el libro mayor
se puede reescribir, deja de ser un libro mayor. Un inventario comercializable tiene que poder
responder "por qué el stock es este" y "quién lo cambió".

### Cómo funciona

1. Se pide un **motivo** (obligatorio, máximo 150 caracteres).
2. El movimiento original pasa a `estado = ANULADO`, se guardan `anulado_por`, `anulado_at` y el
   `motivo`.
3. Se genera un **movimiento compensatorio** de sentido contrario, enlazado con `reversa_de_id`.
4. Se registra la auditoría (`modulo = movimientos`, `accion = ANULAR`).

El stock se mantiene porque la expresión de stock sigue contando el movimiento original **y** el
compensatorio: `10 - 10 = 0`. No hay que recalcular nada a mano.

### Reglas

- No se puede anular dos veces el mismo movimiento.
- Anular una **entrada** genera una salida compensatoria: si ya no hay existencias que
  compensar, la anulación **se rechaza** con un mensaje claro, en vez de dejar el inventario en
  negativo. Anular una salida genera una entrada, que siempre es posible.

---

## 5. Importación masiva segura

### Las tres reglas

1. **No es destructiva.** Solo se modifican las columnas que el archivo trae **con valor**. Una
   celda vacía o una columna ausente deja el artículo como estaba. Un artículo desactivado no se
   reactiva solo.
2. **Es atómica.** Todo el archivo se aplica dentro de una transacción: si algo falla, no queda
   el catálogo a medias.
3. **Es acotada.** Detecta códigos duplicados dentro del mismo archivo, valida los valores
   numéricos contra los límites de las columnas y corta por debajo del máximo de filas.

### Revisión previa

Se añadió un paso opcional antes de aplicar: *Revisar antes de importar*. Muestra cuántos
artículos se crearán, cuántos se actualizarán, cuáles filas tienen error y el detalle de los
cambios, sin tocar el catálogo. Al confirmar se aplica el archivo.

El archivo a la espera de confirmación se guarda en `storage/app/importaciones/<token>.csv` y el
token viaja en la sesión; el par se valida con `hash_equals()`, así que un token inventado no
sirve de nada.

### Lo que no cambió

El contrato anterior se mantiene: `POST /articulos/importar` sigue aplicando el archivo de
inmediato y el resultado sigue en `session('importacion')` con `creados`, `actualizados` y
`errores` por número de línea. Las filas inválidas no impiden que las válidas se guarden. La
importación es una operación de bloque que el administrador puede querer aplicar de una.

---

## 6. Límites numéricos

`app/Support/Limites.php` traduce el tipo de cada columna en un tope utilizable.

| Columna | Tipo | Tope |
|---|---|---|
| `cantidad` | `decimal(12,2)` | 9 999 999 999,99 |
| `costo_unitario` | `decimal(12,4)` | 99 999 999,9999 |
| `precio_venta` | `decimal(12,2)` | 9 999 999 999,99 |
| `stock_minimo` | `decimal(12,2)` | 9 999 999 999,99 |

Con estos topes, un valor desbordado produce un mensaje de validación en el formulario en vez de
un error 500 con la traza de SQL.

---

## 7. Correcciones de interfaz

| Corrección | Dónde |
|---|---|
| Filtros en el historial de movimientos (artículo, tipo, persona, fechas) | `movimientos/index.blade.php` |
| Acción de **anular** con motivo, en un modal en vez de un `confirm()` del navegador | `movimientos/index.blade.php` |
| Distintivo **Anulado** en el historial | `components/tipo-movimiento.blade.php` |
| Acción de **desactivar persona** (la ruta existía pero no había forma de llegar) | `personas/index.blade.php` |
| El costo y el precio no se muestran sin permiso | `components/movimiento-campos.blade.php`, `articulos/partials/form.blade.php` |
| Anti doble envío en los formularios | `components/pagina-form.blade.php` |
| Los filtros se mantienen al cambiar de página (`withQueryString`) | `ReporteController` |
| Paginación y total real en *Entregas por persona* | `reportes/entregas_persona.blade.php` |
| Tipos de movimiento legibles en vez del valor crudo (`ENTRADA`) | `kardex/index.blade.php`, `kardex/pdf.blade.php` |
| Enlaces de administración que faltaban en el menú móvil | `layouts/navigation.blade.php` |
| Un artículo se puede editar aunque su categoría esté desactivada | `UpdateArticuloRequest` |

---

## 8. Migraciones

| Migración | Qué hace |
|---|---|
| `2026_10_20_000001_add_costos_a_articulos_y_movimientos.php` | `costo_unitario` en artículos y movimientos, `costo_promedio` y `precio_venta` en artículos. |
| `2026_10_21_000001_add_idempotencia_y_estado_a_movimientos.php` | `idempotency_key` (única), `estado`, `motivo`, `anulado_por_id`, `anulado_at`, `reversa_de_id`. |

Ambas son reversibles (`down()` completo). La segunda añade un índice por
`(estado, fecha_movimiento)` porque es el filtro que usa el historial.

---

## 9. Pruebas

```
php artisan test --no-coverage
Tests: 184 passed (683 assertions)
```

| Archivo | Qué cubre |
|---|---|
| `PermisosTest.php` | Qué puede hacer cada rol; los costos quedan reservados; las pantallas de administración están protegidas. |
| `IdempotenciaMovimientosTest.php` | El doble envío con la misma clave deja un solo movimiento, tanto por formulario como por servicio. |
| `AnulacionMovimientoTest.php` | El compensatorio, el stock que vuelve a su valor, el motivo obligatorio, el rechazo cuando no hay existencias y la auditoría. |
| `ImportacionSeguraTest.php` | No destruye datos, no reactiva artículos desactivados, detecta duplicados, es atómica y la previsualización no toca el catálogo. |
| `LimitesNumericosTest.php` | Los valores fuera de rango dan un mensaje de validación. |
| `ReglasNegocioTest.php` | (Ampliado) Edición con categoría desactivada y desactivación de personas sin pérdida de historial. |

La suite corre contra **SQLite en memoria** y no toca la base de datos de desarrollo.

---

## 10. Riesgos que quedan abiertos

| Riesgo | Dónde se resuelve |
|---|---|
| **C-1** — credencial de administrador débil en la base de desarrollo | Fuera del repositorio: la cuenta `admin@invensys.cl` de la base de desarrollo **sigue con la contraseña `password`**. Hay que rotarla con `php artisan usuario:clave admin@invensys.cl`. El seeder ahora **rechaza** contraseñas obvias, también en cuentas que ya existían, así que `db:seed` se detiene y avisa hasta que se rote. |
| **C-2** — `APP_DEBUG=true` en `.env` | Fuera del repositorio: poner `APP_DEBUG=false` en producción. |
| **Control individual** sin efecto real | Es una funcionalidad, no un error: el control por unidad (series) necesita su propia tabla de artículos serializados. Queda para una fase posterior. |
| **Aislamiento por organización** | Fase 3. La capa de permisos ya está preparada: `Permisos::de()` es el único punto que hay que cambiar. |

---

## 11. Estado de la Fase 3

La Fase 3 (aislamiento por organización) ya no tiene que tocar los controladores: la decisión de
autorización está centralizada y `organization_id` puede entrar por la misma puerta.

Ver [`02_ARQUITECTURA_MULTIEMPRESA.md`](02_ARQUITECTURA_MULTIEMPRESA.md) para el detalle de las
decisiones (ADR-001 a ADR-011) y el orden de los seis pasos de migración.
