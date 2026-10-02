# Guión para una demostración de Invensys (5–10 minutos)

## Objetivo
Mostrar, en orden, las funcionalidades clave de Invensys sin perder tiempo ni entrar en detalles técnicos. Centrar la demo en el **problema que resuelve** (control de inventario con trazabilidad), no en el código.

## Preparación (1 minuto antes)

1. Base de datos limpia con datos de demo
   ```bash
   php artisan migrate:fresh --seed
   php artisan db:seed --class="DemoSeeder"
   ```

2. Abrir en navegador: `http://localhost:8000/login`
3. Tener dos usuarios listos para mostrar mensajería (o abrir dos ventanas en modo incógnito)
4. Verificar que modo oscuro/claro funciona (opcional)

## Usuarios para la demo

| Rol | Correo | Contraseña | Para mostrar |
|---|---|---|---|
| Administrador | `demo.admin@invensys.cl` | `Demo123456` | Usuarios, auditoría, contacto recibido |
| Jefe de bodega | `demo.bodega@invensys.cl` | `Demo123456` | Entradas, salidas, ajustes, kardex |
| Vendedor | `demo.vendedor@invensys.cl` | `Demo123456` | Artículos, movimientos, reportes |

---

## Guión paso a paso

### 1. Presentación (30 seg) — ¿Qué es Invensys?
- **Pantalla**: Login → Dashboard
- Explicar: "Invensys es un sistema de control de inventario. El problema: las planillas pierden trazabilidad. La solución: cada cambio queda registrado, con quién y cuándo."
- Mostrar dashboard: totales, artículos bajo mínimo, mensajes pendientes.

### 2. Catálogo de artículos (1 min)
- Ir a **Artículos → Listado**
- Mostrar filtros (buscar por código/nombre/categoría)
- Destacar: código único, categoría, unidad de medida, **stock actual**, stock mínimo/máximo
- Abrir un artículo: mostrar precio, ubicación, estado (activo)
- Explicar: **el stock NO se edita manualmente**. Solo cambia con movimientos.

### 3. Registrar una entrada (1 min) — Compra/ingreso
- Ir a **Movimientos → Nueva entrada**
- Seleccionar artículo (p.ej. "Tornillos M8 x 25")
- Cantidad + fecha + observación
- Guardar. Mostrar mensaje de éxito.
- Volver a artículo o a **Kardex**: mostrar que el saldo subió + registro con usuario y fecha.

### 4. Registrar una salida (1 min) — Entrega
- Ir a **Movimientos → Nueva salida**
- Seleccionar artículo con stock
- Cantidad (probar que no permite sacar más de lo disponible)
- Seleccionar persona (destinatario)
- Guardar. Validar stock actualizado.
- Explicar: **validación de stock en tiempo real** (evita salidas negativas).

### 5. Kardex (1 min) — Trazabilidad
- Ir a **Kardex**
- Filtrar por artículo
- Mostrar columnas: Fecha, Tipo (ENTRADA/SALIDA/AJUSTE), Cantidad, Saldo, Usuario, Observación
- Destacar: "Aquí está la respuesta a ¿en qué se gastó este artículo y quién lo sacó?".

### 6. Ajuste de inventario (30 seg)
- Ir a **Movimientos → Ajuste de inventario**
- Mostrar caso de uso: conteo físico difiere del sistema
- Explicar: queda registrado como AJUSTE con observación (no borra historial).

### 7. Reportes (1 min)
- **Reportes → Stock actual**: filtrar "Solo bajo stock mínimo". Mostrar alertas.
- **Reportes → Movimientos por período**: filtrar por fecha.
- **Reportes → Entregas por persona**: qué recibió cada persona.

### 8. Mensajería interna (1–2 min) — Demo en dos ventanas
- Abrir ventana 1 (Bodega) y ventana 2 (Vendedor) en incógnito
- Vendedor: **Mensajes → Nueva conversación** → escribir a Bodega → "Necesito 50 tornillos para pedido urgente"
- Bodega (ventana 2): recibe **notificación + campana + sonido**. Abrir chat, responder.
- Mostrar: polling 3s, "Ir al final", Enter/Shift+Enter, notificaciones globales (funcionan en cualquier pantalla).

### 9. Administración + Auditoría (1 min)
- Login como Admin
- **Administración → Usuarios**: crear/desactivar usuario, cambiar rol.
- **Administración → Auditoría**: mostrar quién creó/editó artículo/movimiento (trazabilidad completa).
- **Administración → Contacto**: mensajes recibidos.

### 10. Cierre (30 seg)
- Destacar 3 puntos: **Trazabilidad (quién/cuándo)**, **Integridad (stock nunca manual)**, **Usable (responsivo + modo oscuro)**.
- Volver a Dashboard.

## Consejos para la presentación

- **Habla del problema primero.** "Hoy esto se hace en Excel, ¿quién sabe la cifra correcta?". Eso vende el valor.
- **No mostrar código.** Mostrar flujo, no implementación.
- **Usa datos reales de demo.** Nombres de artículos coherentes (ferretería, bodega).
- **Un fallo intencional controlado.** Intentar sacar más de lo disponible (sale error) — muestra validación.
- **Modo oscuro.** Mostrar toggle en navbar: queda profesional.

## Notas importantes

- No ejecutar `migrate:fresh` sobre BD de producción.
- Los datos demo son solo para presentación (se pueden borrar).
- `APP_DEBUG=false` si se presenta en red externa.
