# Manual de uso — Invensys

Sistema de gestión de inventario y control de stock.

Este manual explica **cómo se usa el sistema día a día**: qué hace cada módulo, qué datos
pedir y qué reglas se aplican. Para instalar el proyecto, ver el [README](README.md).

---

## Índice

1. [Concepto central: cómo se calcula el stock](#1-concepto-central-cómo-se-calcula-el-stock)
2. [Acceso y roles](#2-acceso-y-roles)
3. [Flujo de trabajo recomendado](#3-flujo-de-trabajo-recomendado)
4. [Módulo Artículos](#4-módulo-artículos)
5. [Módulo Movimientos](#5-módulo-movimientos)
6. [Módulo Personas](#6-módulo-personas)
7. [Kardex](#7-kardex)
8. [Reportes](#8-reportes)
9. [Módulo Usuarios](#9-módulo-usuarios)
10. [Categorías y unidades de medida](#10-categorías-y-unidades-de-medida)
11. [Panel de control](#11-panel-de-control)
12. [Auditoría](#12-auditoría)
13. [Contacto](#13-contacto)
14. [Perfil](#14-perfil)
15. [Reglas y límites del sistema](#15-reglas-y-límites-del-sistema)
16. [Preguntas frecuentes](#16-preguntas-frecuentes)

---

## 1. Concepto central: cómo se calcula el stock

Esta es la regla más importante para entender todo el sistema.

> **El stock de un artículo no es un número que se edita. Es la suma de sus movimientos.**

```
Stock actual = (ENTRADAS + AJUSTES POSITIVOS) − (SALIDAS + AJUSTES NEGATIVOS)
```

Si un artículo no tiene ningún movimiento, su stock es **0**, aunque tenga un stock
mínimo configurado. No existe ningún campo "stock" que se pueda escribir directamente.

**Consecuencia práctica:** para cambiar el stock de un artículo siempre se registra un
movimiento. Nunca se edita el stock a mano. Esto garantiza que el historial siempre
cuadre con la realidad.

### movements y su efecto

| Tipo de movimiento | Efecto en el stock | Cuándo usarlo |
|---|---|---|
| **Entrada** | Suma | Llega mercadería: compra, donación, devolución de un proveedor |
| **Salida** | Resta | Se entrega a una persona: préstamo, entrega, consumo |
| **Ajuste positivo** | Suma | Corrección: se contó mal, apareció stock extra |
| **Ajuste negativo** | Resta | Corrección: merma, rotura, pérdida, sobrante de un conteo |

### Alerta de stock bajo

Un artículo aparece marcado como **bajo mínimo** cuando:

```
Stock actual ≤ Stock mínimo
```

Ojo con el signo: es **menor o igual**, no menor. Un artículo con stock 5 y mínimo 5 ya
aparece en rojo. El número aparece en rojo en el listado y en el reporte de stock, y se
cuenta en el Panel.

### Valorización del inventario (costos)

Esta parte es **opcional**. Si no te interesa valorizar el inventario, puedes ignorar todos
los campos de costo y el sistema funciona igual.

Cuando sí la usas, la regla es:

> **El costo del stock es el promedio ponderado de las entradas que tengan costo.**

En cada entrada de inventario puedes escribir el **costo unitario** de esa mercadería. El
sistema recalcula el costo promedio del artículo combinando lo que ya tenía con lo nuevo:

```
Costo promedio nuevo = (stock anterior × costo promedio anterior + cantidad × costo de la entrada)
                       ────────────────────────────────────────────────────────────────────────
                                          stock anterior + cantidad
```

Ejemplo: tienes 10 unidades a $1.000 y recibes 30 unidades a $2.000. El costo promedio pasa
a $1.750, y el **valor del stock** es 40 × $1.750 = $70.000.

Puntos importantes:

- El costo se registra **por entrada**, no en el artículo. El campo "Costo promedio" del
  artículo es calculado y **no se puede editar a mano**.
- Cada movimiento **congela** el costo que tenía vigente cuando se registró. Si el promedio
  cambia después, el histórico no se altera.
- Una **salida** descuenta stock al costo vigente, pero **no cambia** el costo promedio.
- Un **ajuste negativo** tampoco cambia el promedio. Un **ajuste positivo** con costo sí lo
  recalcula (igual que una entrada).
- Si nunca registras costos, el artículo queda **sin valorizar** y aparece con un guion (—)
  en los reportes, en vez de un cero engañoso.

El **valor del inventario** total se ve en el Panel y en el reporte de stock.

### Márgenes

Si cargas el **precio de venta** de un artículo (en el formulario de artículo) y el artículo
tiene costo promedio, el reporte de stock muestra el **margen**: la diferencia entre el
precio de venta y el costo, y qué porcentaje representa sobre el precio.

```
Margen = Precio de venta − Costo promedio
Margen % = Margen ÷ Precio de venta × 100
```

Ejemplo: vendes a $1.500 y el costo promedio es $1.000 → margen de $500, que es un 33,3 %.

---

## 2. Acceso y roles

### Iniciar sesión

1. Abra la portada del sistema.
2. Click en **Iniciar sesión**.
3. Ingrese correo y contraseña.
4. Pulse **Iniciar sesión**.

Un usuario **desactivado no puede iniciar sesión** aunque tenga la contraseña correcta.

### Los dos roles

| | `admin` (Administrador) | `usuario` (Operador) |
|---|---|---|
| Ver artículos y movimientos | Sí | Sí |
| Registrar entradas, salidas y ajustes | Sí | Sí |
| **Anular movimientos** | Sí | Sí |
| Kardex y reportes | Sí | Sí |
| Ver personas | Sí | Sí |
| Consultar costos (sin poder cambiarlos) | Sí | Sí |
| **Cambiar costo unitario y precio de venta** | **Sí** | **No** |
| **Administrar usuarios** | **Sí** | No |
| **Ver bandeja de contacto** | **Sí** | No |
| **Ver auditoría** | **Sí** | No |
| **Importar artículos masivamente** | **Sí** | No |
| **Administrar categorías y unidades** | **Sí** | No |

La diferencia práctica: el menú **Administración** solo le aparece al administrador.

> **Nota sobre seguridad:** las categorías y las unidades de medida están reservadas al
> administrador. Un usuario normal que escriba la dirección directamente recibe un error
> 403, igual que en el módulo de Administración.

> **Sobre los costos:** un operador **sí** puede registrar una entrada, pero **no** puede
> escribir el costo unitario ni el precio de venta. La razón es práctica: el costo falsea la
> valorización del inventario que se entrega al contador, así que queda reservado a quien
> administra. Si no ves el campo de costo, no es un error: es que tu rol no lo modifica.

### Sesión

La sesión dura **120 minutos** de inactividad. Después se cierra automáticamente y hay
que volver a iniciar sesión. Puedes cerrar sesión manualmente desde el menú de tu usuario,
arriba a la derecha.

---

## 3. Flujo de trabajo recomendado

Para partir desde cero con la base de datos vacía, en este orden:

```
1. Crear categorías          (Administración)
2. Crear unidades de medida  (Administración)
3. Crear artículos           (asignando categoría y unidad)
4. Crear personas            (solo si harás entregas a personas)
5. Registrar ENTRADAS        (cargar el stock inicial)
6. Registrar SALIDAS         (entregar a personas)
7. Usar ajustes              (solo para corregir conteos)
```

Los pasos 1 y 2 son el orden obligatorio: **no se puede crear un artículo sin categoría
ni unidad de medida** previamente configuradas.

---

## 4. Módulo Artículos

Es el catálogo maestro. Ruta: `/articulos`.

### Listado

Muestra **15 registros por página**, ordenados alfabéticamente por nombre. Incluye:

- Código, nombre, categoría y unidad de medida
- **Stock actual** calculado
- Una etiqueta verde (**stock normal**) o roja (**bajo mínimo**)
- Indicador cuando el artículo requiere control individual

**Filtros disponibles** (se pueden combinar):

| Filtro | Qué hace |
|---|---|
| Buscar | Busca en **código, nombre y descripción** a la vez |
| Categoría | Muestra solo los de una categoría |
| Estado | `Activos`, `Inactivos` o todos |

Los filtros se conservan al cambiar de página. Para borrarlos, usa **Limpiar**.

> El listado **solo muestra artículos activos**. Para ver los desactivados, elige
> **Inactivos** en el filtro de estado.

### Ficha del artículo

Click en el nombre de cualquier artículo para abrir su detalle. Muestra:

- Datos completos, categoría y unidad
- **Stock actual** y **stock mínimo**, con alerta si está bajo
- **Costo promedio** y **valor del stock**, si el artículo tiene costos cargados
- Si el control individual está activado
- Fecha de la **última entrada** y de la **última salida**
- Los **15 movimientos más recientes** de ese artículo, con tipo, cantidad, fecha,
  persona, usuario y referencia

Desde la ficha hay accesos directos a registrar una entrada, salida o ajuste de ese
artículo en particular.

### Crear un artículo

Pulse **Nuevo artículo**. Complete:

| Campo | Obligatorio | Reglas |
|---|---|---|
| Código | **Sí** | Máx. 50 caracteres. **No puede repetirse** |
| Nombre | **Sí** | Máx. 150 caracteres |
| Categoría | **Sí** | Solo categorías activas |
| Unidad de medida | **Sí** | Solo unidades activas |
| Stock mínimo | **Sí** | Número mayor o igual a 0. Si no tienes un mínimo definido, escribe `0` |
| Precio de venta | No | Número mayor o igual a 0. Opcional. Solo el administrador puede escribirlo |
| Descripción | No | Texto libre |
| Control individual | No | Casilla. Ver nota abajo |

Pulsa **Guardar**. El sistema lo registra en la auditoría y te devuelve al listado con un
mensaje de confirmación.

> **Sobre "Control individual":** la casilla existe y se muestra en el listado y la ficha,
> pero **actualmente no altera ningún cálculo ni validación**. Úsala como etiqueta
> informativa para marcar qué artículos requieren seguimiento detallado (por ejemplo, los
> que tienen número de serie), aunque el sistema todavía no lo aprovecha.

### Editar y desactivar

- **Editar** permite cambiar todos los datos, incluido el **estado activo/inactivo**.
- **Desactivar** (el botón rojo de la tabla) **no borra el artículo**: lo marca como
  inactivo. Su historial de movimientos se conserva íntegro y sigue visible en el Kardex.

**No existe el borrado definitivo.** Un artículo desactivado:
- desaparece del listado por defecto
- no aparece en los selectores de movimientos ni en el Kardex
- **conserva todos sus movimientos y su historial para siempre**

Para reactivar un artículo, entra a **Editar** y márcalo como activo.

> Puedes desactivar un artículo aunque tenga movimientos registrados. Al reactivarlo, todo
> su historial y su stock vuelven a la vista tal como estaban.

---

## 5. Módulo Movimientos

Es el corazón del sistema. Ruta: `/movimientos`.

### Regla fundamental

> **Los movimientos no se editan ni se borran. Nunca.**

Si te equivocaste al registrar, hay dos formas de corregirlo, y la elección depende de qué
fue lo que pasó:

- **El movimiento quedó mal registrado** (te equivocaste en la cantidad, en el artículo, en la
  fecha): se **anula** desde el historial. Véase [Anular un movimiento](#anular-un-movimiento).
- **La diferencia es real** (un conteo físico que no cuadra, una merma, algo que se perdió): se
  registra un **ajuste** en sentido contrario, dejándolo en la referencia u observaciones.

Esto es intencional: el historial debe ser confiable.

### Anular un movimiento

En **Movimientos → Historial**, cada movimiento tiene la acción **Anular**. Al pulsarla:

1. Se abre una ventana donde **debes escribir el motivo** (es obligatorio).
2. El movimiento original queda marcado como **Anulado** y aparece con esa Distintiva en el
   listado. No se borra: sigue estando, y se puede ver que fue anulado.
3. El sistema genera un **movimiento compensatorio** en sentido contrario, enlazado al original.
4. El stock queda **como estaba** antes del movimiento.
5. La anulación queda registrada en la **Auditoría**: quién, cuándo y por qué.

Dos cosas que conviene saber:

- **No se puede anular dos veces** el mismo movimiento, ni anular un movimiento ya anulado.
- Si anulas una **entrada** y ya no hay existencias suficientes para compensarla, el sistema
  **rechaza la anulación** con un mensaje claro, en vez de dejar el inventario en negativo. En
  ese caso, corrige primero el origen de la diferencia (un ajuste o una salida que faltaba).

### Historial

La tabla `Historial` muestra **todos** los movimientos, del más reciente al más antiguo,
con: fecha y hora, artículo, tipo, cantidad, persona destinataria, usuario que lo
registró y referencia. Los movimientos anulados aparecen con la distintiva **Anulado**.

> El listado tiene **filtros** (artículo, tipo de movimiento, persona y rango de fechas) y está
> paginado (50 registros por página, en orden del más reciente al más antiguo). Los filtros se
> mantienen al cambiar de página.

### Los tres formularios de registro

Todos piden **cantidad** (mayor que 0) y **fecha del movimiento** (puede ser una fecha
pasada, para registrar movimientos atrasados). Opcionalmente **referencia** (máx. 100
caracteres, ej. número de orden de compra) y **observaciones**. Las entradas y los ajustes
positivos además ofrecen el campo **costo unitario** (opcional, ver
[Valorización del inventario](#valorización-del-inventario-costos)).

#### Nueva entrada — `ENTRADA`

Suma stock. **No pide persona** (no va dirigida a nadie).

Úsala cuando llega mercadería. Campos: artículo, cantidad, **costo unitario** (opcional),
fecha, referencia, observaciones.

#### Nueva salida — `SALIDA`

Resta stock. **Pide persona destinataria** (obligatorio).

Úsala cuando entregas algo a una persona. Campos: artículo, **persona**, cantidad, fecha,
referencia, observaciones.

> **Validación:** el sistema **impide registrar una salida mayor al stock disponible**.
> Si intentas sacar 10 unidades de un artículo que tiene 5, aparece el error
> *"Stock insuficiente. Stock disponible: 5."* y no se registra nada.

#### Ajuste de inventario — `AJUSTE_POSITIVO` / `AJUSTE_NEGATIVO`

Corrige el stock sin entrada ni salida real. Campos: artículo, **tipo de ajuste**,
cantidad, **costo unitario** (opcional, solo en ajuste positivo), fecha, referencia,
observaciones.

- **Ajuste positivo:** para corregir un conteo a la baja, o registrar mercadería
  encontrada. Ejemplo: el conteo físico dio 20 pero el sistema dice 18 → ajuste positivo
  de 2.
- **Ajuste negativo:** para mermas, roturas o pérdidas. Ejemplo: se rompieron 3 unidades
  → ajuste negativo de 3.

> **Validación:** un ajuste negativo **tampoco puede superar el stock disponible**.

Los tres formularios aceptan el parámetro `?articulo_id=N` para preseleccionar el
artículo, que es lo que hacen los botones directos desde la ficha de un artículo.

---

## 6. Módulo Personas

Son las personas a quienes se les entrega mercadería. Ruta: `/personas`.

Solo se usan en las **salidas**. Si nunca registras salidas a personas, puedes ignorar
este módulo.

| Campo | Obligatorio | Reglas |
|---|---|---|
| Nombre | **Sí** | Máx. 100 caracteres |
| Apellido | **Sí** | Máx. 100 caracteres |
| Identificador | No | Máx. 50. **Único** si se completa (ej. RUT) |
| Correo electrónico | No | Formato de email válido |
| Área | No | Máx. 100 |
| Cargo | No | Máx. 100 |
| Activo | No | Casilla |

El listado ordena por **apellido y luego nombre**. En cada fila hay una acción **Desactivar**:
pregunta confirmación y marca a la persona como inactiva, sin borrar nada. Al desactivar una
persona, deja de aparecer en el selector de salidas, pero **los movimientos ya registrados
conservan el vínculo** con ella.

Como los movimientos nunca se borran, desactivar es la única forma de "eliminar" una
persona, y es segura. Para reactivarla, entra a **Editar** y márcala como activa.

---

## 7. Kardex

Ruta: `/kardex`. Es el **historial cronológico de un solo artículo**, con saldo
acumulado.

### Cómo usarlo

1. Elige un **artículo** en el selector superior.
2. Se muestra la tabla con una fila por movimiento, **en orden cronológico** (del más
   antiguo al más reciente).

Cada fila muestra: fecha, tipo, entrada, salida, **costo unitario**, **valor** (cantidad ×
costo), **saldo** (acumulado después de ese movimiento), persona, usuario, referencia y
observaciones.

> **El saldo de la última fila es el stock actual del artículo.**

Arriba de la tabla se muestra la ficha del artículo, que incluye el costo promedio y el
valor del stock cuando hay costos cargados. El botón **Descargar PDF** genera el kardex con
esas mismas columnas.

Si no seleccionas ningún artículo, la página solo muestra el selector.

---

## 8. Reportes

Ruta: `/reportes`. Son lecturas: **no modifican datos**.

### Reporte de stock actual

Lista todos los artículos **activos** con su stock calculado, stock mínimo y estado. Si el
artículo tiene costo cargado, también muestra el **costo promedio** y el **valor del
stock**; si además tiene precio de venta, muestra el **margen**. Al pie aparece el **valor
total del inventario**.

Casilla **"Mostrar solo artículos bajo mínimo"**: filtra en vivo los que estén en o bajo
el mínimo. Es el reporte para responder "¿qué debo reponer?".

Botón **"Inventario valorizado (contabilidad)"**: descarga un CSV con los artículos que
tienen existencias y costo, con cantidad, costo unitario, valor y el **total general**. Es
el detalle que se entrega al contador para respaldar el inventario a fin de período.

### Reporte de movimientos por período

Filtros (combinables):

| Filtro | Effect |
|---|---|
| Fecha desde / hasta | Rango de fechas del movimiento |
| Artículo | Un artículo específico |
| Tipo | ENTRADA, SALIDA, ajuste positivo o ajuste negativo |

Lista los movimientos del más reciente al más antiguo, con artículo, tipo, cantidad,
persona, usuario y referencia.

### Reporte de entregas por persona

Muestra **solo salidas** (los movimientos en que se entregó mercadería a alguien).

Filtros: persona, fecha desde y fecha hasta. Responde a "¿qué recibió cada persona?".

> **Los tres reportes se muestran en pantalla y se pueden imprimir desde el navegador
> (Ctrl+P). El sistema no genera archivos Excel ni PDF.**

---

## 9. Módulo Usuarios

Solo administradores. Ruta: `/usuarios`.

### Crear un usuario

| Campo | Obligatorio | Reglas |
|---|---|---|
| Nombre | **Sí** | Máx. 255 |
| Correo electrónico | **Sí** | Formato válido, **no puede repetirse** |
| Contraseña | **Sí** | Mínimo 8 caracteres, debe coincidir con la confirmación |
| Rol | **Sí** | `admin` o `usuario` |

El usuario queda **activo** y puede iniciar sesión de inmediato con ese correo.

### Editar

- La contraseña es **opcional**: déjala vacía para no cambiarla.
- Si la escribes, se cambia.
- Puedes cambiar el rol y el estado activo/inactivo.

### Desactivar

Marca al usuario como inactivo. **No puede iniciar sesión** desde ese momento.

> **Te protects de desactivarte a ti mismo:** si intentas hacerlo, el sistema lo rechaza
> con el aviso *"No puedes desactivarte a ti mismo."*

---

## 10. Categorías y unidades de medida

Rutas: `/categorias` y `/unidades-medida`. **Solo administradores**: un usuario normal
recibe un error 403 si entra por URL directa.

Son catálogos de apoyo. Se usan al crear artículos.

### Categorías

| Campo | Obligatorio | Reglas |
|---|---|---|
| Nombre | **Sí** | Máx. 100, **único** |
| Descripción | No | Texto libre |
| Activo | No | Casilla |

### Unidades de medida

| Campo | Obligatorio | Reglas |
|---|---|---|
| Nombre | **Sí** | Máx. 50, **único** (ej. "Unidad", "Paquete", "Caja") |
| Abreviatura | No | Máx. 10 caracteres (ej. "un", "paq", "caja") |

Al desactivar una categoría o unidad, deja de aparecer en los selectores de artículos
nuevos. Los artículos que ya la usaban **la conservan** y se muestran igual, aunque ya no
puedas elegirla al crear otro artículo. Para volver a usarla, reactívala desde su edición.

---

## 11. Panel de control

Ruta: `/dashboard`. La primera pantalla al entrar.

Muestra cuatro indicadores:

| Indicador | Qué cuenta |
|---|---|
| **Artículos activos** | Artículos no desactivados |
| **Personas activas** | Personas no desactivadas |
| **Movimientos este mes** | Movimientos del mes en curso |
| **Stock bajo** | Artículos en o bajo su mínimo (en **rojo** si hay alguno) |
| **Valor del inventario** | Valor total del stock valorizado; un guion (—) si ningún artículo tiene costo |

Debajo: la lista de artículos con stock bajo y los **8 movimientos más recientes** de todo
el sistema.

---

## 12. Auditoría

Solo administradores. Ruta: `/auditoria`.

Registro automático de las operaciones realizadas en el sistema. El sistema escribe una
entrada por cada acción; no se agrega ni se edita a mano.

### Qué se registra

| Módulo | Acciones registradas |
|---|---|
| Artículos | CREAR, ACTUALIZAR, DESACTIVAR |
| Movimientos | ENTRADA, SALIDA, AJUSTE_POSITIVO, AJUSTE_NEGATIVO |
| Personas | CREAR, ACTUALIZAR, DESACTIVAR |
| Categorías | CREAR, ACTUALIZAR, DESACTIVAR |
| Usuarios | CREAR, ACTUALIZAR, DESACTIVAR |
| Contacto | CREAR, ATENDER |

> Las unidades de medida generan entradas de auditoría con la misma estructura que las
> categorías.

En una actualización se guarda tanto el **valor anterior** como el **cambio aplicado**, así
que puedes ver qué exactamente se modificó.

### Filtros

Fecha desde, fecha hasta, usuario, módulo y acción. Se combinan entre sí. El listado
está paginado (50 entradas por página) y el botón **Exportar CSV** descarga el resultado
completo, sin paginar.

Las contraseñas de los usuarios **nunca** se guardan en la auditoría.

---

## 13. Contacto

Ruta: `/contacto`.

Formulario para enviar un mensaje a los administradores. Campos: nombre, correo
electrónico, asunto y mensaje. Todos obligatorios.

El mensaje queda registrado con estado **PENDIENTE**.

El administrador, en `/contacto/administracion`, ve la bandeja con los pendientes primero
y puede marcar cualquiera como **ATENDIDO** con un botón.

> No se envía ningún correo electrónico: es una bandeja interna dentro del sistema.

---

## 14. Perfil

Ruta: `/profile`. Cualquier usuario puede entrar.

Tres secciones:

- **Información del perfil:** cambiar nombre y correo electrónico.
- **Contraseña:** escribe la actual, la nueva y su confirmación.
- **Eliminar cuenta:** **desactiva** tu propia cuenta.

> Al eliminar tu cuenta quedas desactivado y no podrás volver a entrar. Pide a un
> administrador que la reactive si fuera un error.

> **Excepción:** si ya registraste movimientos, el botón de eliminar cuenta no está
> disponible. Tu identidad queda asociada a esas operaciones y el historial de inventario
> es inmutable; un administrador puede desactivarte, pero la cuenta no se elimina.

---

## 15. Reglas y límites del sistema

Conviene conocerlos para evitar sorpresas durante su uso:

1. **El stock nunca se edita.** Solo cambia mediante movimientos.
2. **Los movimientos son inmutables.** No hay edición ni borrado; se corrigen anulando el
   movimiento o con un ajuste.
3. **Nada se borra físicamente.** Artículos, personas, categorías, unidades y usuarios se
   **desactivan**. El historial asociado se conserva siempre.
4. **Las salidas y los ajustes negativos no pueden dejar el stock en negativo.** El
   sistema lo bloquea.
5. **No se puede crear un artículo sin categoría y unidad de medida activas.**
6. **Un usuario desactivado no puede iniciar sesión.**
7. **No puedes desactivar tu propia cuenta de usuario.**
8. **El estado "bajo mínimo" es stock ≤ mínimo** (menor o igual).
9. **Los reportes se pueden descargar** en CSV (Excel en español) y el Kardex en PDF.
10. **El historial de movimientos tiene filtros** (artículo, tipo, persona y fechas) y está
    paginado (50 por página).
11. **Las categorías y las unidades de medida exigen rol administrador.** Un usuario
    normal recibe un error 403 si entra por URL directa.
12. **El registro público de usuarios está deshabilitado** (la ruta `/register` no existe).
    Las cuentas las crea un administrador desde **Administración → Usuarios**.
13. **Los costos solo los modifica el administrador.** Un operador registra entradas sin
    costo; si intenta cambiar un costo, el sistema se lo rechaza.
14. **Un doble clic no duplica movimientos.** Cada formulario viaja con una clave única: si
    la misma clave llega dos veces, la segunda se reconoce y no repite el movimiento.
15. **La importación de artículos no destruye datos.** Solo cambia las columnas que el
    archivo trae con valor, y se aplica completa o no se aplica.

---

## 16. Preguntas frecuentes

**¿Por qué mi artículo tiene stock 0 si nunca le hice una salida?**
Porque el stock se calcula solo con movimientos. Si recién lo creaste, parte en 0. Necesitas
registrar una entrada.

**Registré una salida de más. ¿Cómo la corrijo?**
Vuelve a **Movimientos → Historial** y pulsa **Anular** en esa salida: el sistema marca el
movimiento como anulado, genera una entrada compensatoria y deja el stock como estaba. Si la
salida fue correcta y lo que cambió fue el conteo físico, registra en cambio un **ajuste
positivo** con la diferencia.

**¿Se puede deshacer una salida?**
Sí, de dos formas: **anulando** la salida desde el historial (si el movimiento estaba mal
registrado) o registrando un **ajuste positivo** por la misma cantidad (si la diferencia es
real).

**¿Puedo cambiar el stock mínimo de un artículo?**
Sí, edita el artículo y cambia el stock mínimo. El stock actual no se ve afectado.

**Creé un artículo con el código equivocado. ¿Lo borro?**
No hay borrado. Edita el artículo y corrige el código. Solo se "
desactiva".

**¿Por qué no me aparece un artículo al registrar un movimiento?**
Porque está desactivado. Reactívalo desde **Editar**.

**¿Por qué no me aparece una categoría en el formulario de artículo?**
Está desactivada. Reactívala o crea una nueva.

**¿Cómo sé quién hizo un movimiento?**
Cada movimiento guarda el usuario que lo registró, y puedes verlo en el listado, en la
ficha del artículo y en el reporte de movimientos.

**¿Se puede deshacer una salida?**
Sí, de dos formas: **anulando** la salida desde el historial (si el movimiento estaba mal
registrado) o registrando un **ajuste positivo** por la misma cantidad (si la diferencia es
real).

**¿Por qué no me aparece el campo de costo unitario?**
Porque tu rol no puede modificar los costos. Un operador puede registrar entradas **sin**
costo; el costo unitario y el precio de venta los carga el administrador.

**¿Los datos se borran si desinstalo la aplicación?**
Depende de dónde estén los datos. Si usaste **MySQL**, los datos están en la base de datos
y sobreviven. Si usaste **SQLite**, están en el archivo `database/database.sqlite`, que
también es un archivo que puedes respaldar copiándolo.

**¿Cómo respaldo mis datos?**
Si usas MySQL, desde phpMyAdmin exporta la base a un archivo `.sql`. Si usas SQLite, copia
el archivo `database/database.sqlite`. Hazlo periódicamente.

---

*Manual de uso de Invensys. Para instalación y desarrollo, consulta el README. :)*
