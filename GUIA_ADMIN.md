# Guía del administrador — Invensys

Operación diaria del sistema. El uso completo de cada módulo está en
`MANUAL_USUARIO.md`; esta guía concentra las tareas del administrador.

## 1. Primeros pasos

1. Entra con el usuario administrador.
2. **Más → Administración → Usuarios**: crea una cuenta para cada persona
   que usará el sistema. El registro web está cerrado a propósito.
3. Define la estructura base antes de cargar datos:
   - **Categorías** (Más → Administración → Categorías)
   - **Unidades de medida** (Más → Administración → Unidades de medida)
   - **Personas** (aquí se registran quienes reciben entregas)

## 2. Cargar los artículos

### Opción A: importación masiva (recomendada)

1. Ve a **Artículos → Importar CSV** (solo administradores).
2. Descarga la **plantilla CSV** y complétala en Excel:

   | Columna | Obligatoria | Ejemplo |
   |---------|-------------|---------|
   | `codigo` | sí | ART-001 |
   | `nombre` | sí | Notebook Dell 14" |
   | `categoria` | sí | Informática |
   | `unidad` | sí | Unidad |
   | `stock_minimo` | sí | 5 |
   | `descripcion` | no | Equipo de trabajo |
   | `control_individual` | no | sí / no |

3. Sube el archivo (máximo 500 filas y 1 MB).
4. Elige cómo quieres aplicarlo:
   - **Revisar antes de importar**: el sistema analiza el archivo y te muestra
     cuántos artículos se crearán, cuántos se actualizarán, qué filas tienen
     error y el detalle de los cambios, **sin tocar el catálogo**. Si está todo
     bien, pulsa **Confirmar importación**.
   - **Importar**: aplica el archivo de inmediato.
5. Revisa el resultado: el sistema informa cuántos artículos se crearon,
   cuántos se actualizaron y qué filas tienen error (con el motivo). Corrige
   solo esas filas y vuelve a subir.

Reglas importantes:

- El cruce se hace por **código**: si el código ya existe se actualiza el
  artículo; si no, se crea uno nuevo.
- **La importación no destruye datos**: solo se modifican las columnas que el
  archivo trae con valor. Las celdas vacías y las columnas que no aparecen
  dejan el artículo como estaba.
- Un artículo **desactivado no se reactiva** solo por importarlo.
- Un **código repetido dentro del mismo archivo** se informa como error en la
  segunda ocurrencia: solo se crea la primera.
- Si algo falla al guardar, **no queda nada a medias**: la importación se
  aplica completa o no se aplica.
- La categoría y la unidad deben **existir y estar activas** en el sistema.
- El stock se carga después con un movimiento de **entrada** (el importador
  solo define el stock mínimo).
- El **precio de venta** se puede incluir en el archivo, pero solo lo carga
  quien tiene permiso para modificar costos.

### Opción B: uno por uno

**Artículos → Nuevo artículo** y luego registrar la entrada inicial desde
**Movimientos → Nueva entrada**.

## 3. Operación diaria

- **Entradas y salidas**: siempre por los formularios de Movimientos; nunca
  se edita el stock a mano.
- **Corrección de un movimiento mal registrado**: en **Movimientos → Historial**,
  cada movimiento tiene la acción **Anular**. Pides el motivo y el sistema:
  1. marca el movimiento original como **anulado** (no lo borra),
  2. genera un **movimiento compensatorio** en sentido contrario,
  3. deja el stock como estaba, y
  4. registra quién lo anuló y por qué en la auditoría.

  Anular una **entrada** se rechaza si ya no hay existencias que compensar: es
  preferible a dejar el inventario en negativo.
- **Alertas de stock**: el panel muestra los artículos iguales o inferiores a
  su mínimo, y la barra de navegación marca el contador junto a *Artículos*.
- **Valor del inventario**: el panel muestra el valor total del stock valorizado.
  Si ningún artículo tiene costo cargado, aparece un guion (—).
- **Mensajes internos**: el sistema avisa con sonido y contador.

## 3 bis. Qué puede hacer cada rol

| | Operador (`usuario`) | Administrador (`admin`) |
|---|:---:|:---:|
| Operar inventario, personas, kardex y reportes | Sí | Sí |
| Registrar entradas, salidas, ajustes y anular | Sí | Sí |
| Consultar costos | Sí | Sí |
| **Cambiar costos y precios** | **No** | Sí |
| **Administrar** usuarios, categorías, unidades, configuración | **No** | Sí |
| **Importar artículos** | **No** | Sí |
| Ver auditoría, contacto y diagnóstico | **No** | Sí |

Un operador que intente modificar un costo recibe un mensaje de permiso, no un
error: el campo ni siquiera se le muestra.

## 3 ter. Anular un movimiento: cuándo y por qué

- **Anular**, no ajustar, cuando el movimiento estaba **mal registrado**: la
  anulación lo deja marcado y genera el compensatorio, y el historial queda
  legible ("esto se anuló y por qué").
- **Ajustar** cuando la diferencia es real: un conteo físico que no cuadra, una
  merma, un artículo que se perdió.
- La anulación queda en la auditoría con la acción `ANULAR`.

## 4. Valorización del inventario

El sistema puede valorizar el stock con **costo promedio ponderado**. Es
opcional: si no cargas costos, todo lo demás funciona igual.

- El costo se registra **al hacer una entrada de inventario** (campo "Costo
  unitario"). También en los ajustes positivos.
- El **costo promedio** del artículo se calcula solo y **no se edita a mano**.
- Cada movimiento guarda el costo que tenía vigente al registrarse, así el
  histórico no cambia cuando el promedio se mueve.
- El **reporte de stock** muestra costo promedio, precio de venta, **margen**, valor
  por artículo y valor total; el CSV los incluye.
- El campo **precio de venta** del artículo es opcional y sirve para calcular el
  **margen** sobre el costo promedio.
- Botón **"Inventario valorizado (contabilidad)"** en Reportes → Stock: CSV con los
  artículos con existencias y costo, más el total general. Entrégalo al contador a
  fin de período.

Regla práctica: **el costo se carga una sola vez por entrada**. Si te equivocas,
registra una entrada nueva con el costo correcto; el promedio se corrige solo.

## 5. Exportaciones

| Qué | Dónde | Formato |
|-----|-------|---------|
| Stock actual | Reportes → Stock actual → Descargar CSV | CSV (Excel español) |
| Inventario valorizado | Reportes → Stock actual → Inventario valorizado (contabilidad) | CSV con costo, valor y total general |
| Movimientos por período | Reportes → Movimientos | CSV con filtros de fecha/tipo/artículo |
| Entregas por persona | Reportes → Entregas por persona | CSV por persona/período |
| Kardex de un artículo | Kardex → elegir artículo → Descargar PDF | PDF con saldo acumulado |
| Auditoría | Más → Administración → Auditoría → Exportar CSV | CSV con filtros |

Todos los CSV abren directo en Excel en español (separador `;` y acentos
correctos). Si un texto empieza con `=`, `+`, `-` o `@` (cosas que Excel
interpretaría como fórmula), se le antepone un apóstrofo para que se muestre
como texto y no se ejecute.

## 6. Auditoría

**Más → Administración → Auditoría**: quién hizo qué y cuándo.

- Filtros por fecha, usuario, módulo y acción.
- Se registran altas, ediciones, bajas, movimientos, importaciones y accesos.
- No se puede editar ni borrar: es el registro de trazabilidad.

## 7. Diagnóstico del sistema

**Más → Administración → Diagnóstico** (`/diagnostico`): comprueba base de
datos, migraciones pendientes, permisos de escritura, assets compilados,
`APP_KEY`, `APP_DEBUG` y versión de PHP. Revísalo:

- Después de cada actualización del sistema.
- Si la aplicación se ve rara (sin estilos, errores 500).

## 8. Contraseñas olvidadas

Desde la terminal del servidor:

```bash
php artisan usuario:clave CORREO@ejemplo.cl                  # genera una clave
php artisan usuario:clave CORREO@ejemplo.cl --clave="NuevaClave.2026"
```

El comando cierra las sesiones abiertas del usuario. Los usuarios
desactivados no se tocan salvo que se agregue `--todo`.

También funciona **Olvidé mi contraseña** por correo (si hay correo
configurado).

## 9. Respaldo de la base de datos

Respaldo (XAMPP / MySQL por consola):

```bash
mysqldump -u root invensys > respaldo_$(date +%Y-%m-%d).sql
```

Restaurar:

```bash
mysql -u root invensys < respaldo_2026-10-06.sql
```

En phpMyAdmin: **Exportar** (rápido, SQL) y **Importar** para restaurar.

**Recomendación**: respaldo semanal y antes de cada actualización.

## 10. Checklist semanal

- [ ] Revisar el panel: artículos en rojo (stock bajo).
- [ ] Revisar **Auditoría** por movimientos inusuales.
- [ ] Respaldo de la base de datos.
- [ ] Revisar `storage/logs/laravel.log` si hubo errores.
- [ ] `/diagnostico` en verde.

## 11. Puesta en producción (recordatorio)

Antes de exponer el sistema:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.cl
```

Detalles completos en `README.md` → *Puesta en producción*, incluida la
lista de verificación de seguridad (el `.htaccess` de la raíz ya bloquea
`.env`, el código fuente y los directorios sensibles).
