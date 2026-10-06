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
4. Revisa el resultado: el sistema informa cuántos artículos se crearon,
   cuántos se actualizaron y qué filas tienen error (con el motivo). Corrige
   solo esas filas y vuelve a subir.

Reglas importantes:

- El cruce se hace por **código**: si el código ya existe se actualiza el
  artículo; si no, se crea uno nuevo.
- La categoría y la unidad deben **existir y estar activas** en el sistema.
- El stock se carga después con un movimiento de **entrada** (el importador
  solo define el stock mínimo).

### Opción B: uno por uno

**Artículos → Nuevo artículo** y luego registrar la entrada inicial desde
**Movimientos → Nueva entrada**.

## 3. Operación diaria

- **Entradas y salidas**: siempre por los formularios de Movimientos; nunca
  se edita el stock a mano.
- **Alertas de stock**: el panel muestra los artículos iguales o inferiores a
  su mínimo, y la barra de navegación marca el contador junto a *Artículos*.
- **Mensajes internos**: el sistema avisa con sonido y contador.

## 4. Exportaciones

| Qué | Dónde | Formato |
|-----|-------|---------|
| Stock actual | Reportes → Stock actual → Descargar CSV | CSV (Excel español) |
| Movimientos por período | Reportes → Movimientos | CSV con filtros de fecha/tipo/artículo |
| Entregas por persona | Reportes → Entregas por persona | CSV por persona/período |
| Kardex de un artículo | Kardex → elegir artículo → Descargar PDF | PDF con saldo acumulado |
| Auditoría | Más → Administración → Auditoría → Exportar CSV | CSV con filtros |

Todos los CSV abren directo en Excel en español (separador `;` y acentos
correctos).

## 5. Auditoría

**Más → Administración → Auditoría**: quién hizo qué y cuándo.

- Filtros por fecha, usuario, módulo y acción.
- Se registran altas, ediciones, bajas, movimientos, importaciones y accesos.
- No se puede editar ni borrar: es el registro de trazabilidad.

## 6. Diagnóstico del sistema

**Más → Administración → Diagnóstico** (`/diagnostico`): comprueba base de
datos, migraciones pendientes, permisos de escritura, assets compilados,
`APP_KEY`, `APP_DEBUG` y versión de PHP. Revísalo:

- Después de cada actualización del sistema.
- Si la aplicación se ve rara (sin estilos, errores 500).

## 7. Contraseñas olvidadas

Desde la terminal del servidor:

```bash
php artisan usuario:clave CORREO@ejemplo.cl                  # genera una clave
php artisan usuario:clave CORREO@ejemplo.cl --clave="NuevaClave.2026"
```

El comando cierra las sesiones abiertas del usuario. Los usuarios
desactivados no se tocan salvo que se agregue `--todo`.

También funciona **Olvidé mi contraseña** por correo (si hay correo
configurado).

## 8. Respaldo de la base de datos

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

## 9. Checklist semanal

- [ ] Revisar el panel: artículos en rojo (stock bajo).
- [ ] Revisar **Auditoría** por movimientos inusuales.
- [ ] Respaldo de la base de datos.
- [ ] Revisar `storage/logs/laravel.log` si hubo errores.
- [ ] `/diagnostico` en verde.

## 10. Puesta en producción (recordatorio)

Antes de exponer el sistema:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.cl
```

Detalles completos en `README.md` → *Puesta en producción*, incluida la
lista de verificación de seguridad (el `.htaccess` de la raíz ya bloquea
`.env`, el código fuente y los directorios sensibles).
