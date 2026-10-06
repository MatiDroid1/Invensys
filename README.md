# Invensys

Sistema web de control de inventario construido con **Laravel 12**, **Tailwind CSS 3** y **Alpine.js**.

Invensys permite registrar artículos, controlar el stock mediante movimientos de entrada, salida y
ajuste, consultar el **Kardex** histórico de cada producto, generar reportes y auditar todas las
operaciones realizadas por los usuarios.

---

## Contenido

- [Características](#características)
- [Stack tecnológico](#stack-tecnológico)
- [Requisitos previos](#requisitos-previos)
- [Instalación](#instalación)
- [Acceso al sistema](#acceso-al-sistema)
- [Cómo funciona el stock](#cómo-funciona-el-stock)
- [Módulos del sistema](#módulos-del-sistema)
- [Roles y permisos](#roles-y-permisos)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Comandos útiles](#comandos-útiles)
- [Ejecutar los tests](#ejecutar-los-tests)
- [Desarrollo](#desarrollo)
- [Preguntas frecuentes](#preguntas-frecuentes)
- [Licencia](#licencia)

---

## Características

- **Catálogo de artículos** con código, nombre, descripción, categoría, unidad de medida, stock
  mínimo y control individual. Búsqueda por texto, filtro por categoría y por estado, con
  paginación.
- **Ficha de artículo** con indicadores de stock, últimas entradas y salidas, y los movimientos más
  recientes.
- **Movimientos de inventario** de cuatro tipos: `ENTRADA`, `SALIDA`, `AJUSTE_POSITIVO` y
  `AJUSTE_NEGATIVO`.
- **Control de stock seguro**: no se permite registrar una salida ni un ajuste negativo mayor al
  stock disponible. La validación se hace dentro de una transacción de base de datos con
  `lockForUpdate()`, por lo que dos operaciones simultáneas no pueden descuadrar el inventario.
- **Kardex por artículo** con el detalle de entradas, salidas y saldo acumulado de cada movimiento.
- **Personas** como destinatarias de entregas, con área, cargo e identificador.
- **Reportes**: stock actual, movimientos por período y entregas por persona.
- **Auditoría**: todo alta, edición, desactivación y movimiento queda registrado con usuario,
  fecha, módulo y detalle del cambio.
- **Gestión de usuarios** con dos roles (`admin` y `usuario`) y activación/desactivación de cuentas.
- **Bandeja de contacto** para que los usuarios reporten problemas.
- **Interfaz responsive** con soporte de **modo oscuro**, pensada para funcionar en escritorio,
  tablet y móvil.
- **Interfaz completamente en español**, incluidos los mensajes de validación de formularios.

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12.69.2 |
| Lenguaje | PHP ^8.2 |
| Frontend | Blade + Alpine.js 3 |
| Estilos | Tailwind CSS 3 + `@tailwindcss/forms` |
| Build | Vite 7 + PostCSS |
| Base de datos | MySQL 8 (o SQLite para pruebas) |
| Autenticación | Laravel Breeze 2.4 (sesiones) |
| Cola / caché / sesión | Base de datos |
| Tests | Pest 3 + PHPUnit |
| Formato de código | Laravel Pint |

---

## Requisitos previos

- **PHP 8.2** o superior
- **Composer 2**
- **Node.js 20.19+ o 22.12+** y **npm** — Vite 7 no funciona con Node 18
- **MySQL 5.7+ / 8** (o MariaDB 10.4+)
- Extensiones de PHP requeridas: `ctype`, `filter`, `hash`, `mbstring`, `openssl`, `session`, `tokenizer`, `pdo`
- Extensiones para este proyecto: `pdo_mysql` (o `pdo_sqlite` si usas SQLite) y `fileinfo`

> **Windows con XAMPP:** la extensión `pdo_mysql` suele venir desactivada. Ábrela en
> `php.ini` quitando el `;` de la línea `extension=pdo_mysql` y reinicia Apache.

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/MatiDroid1/Invensys.git
cd Invensys
```

### 2. Instalar las dependencias de PHP

```bash
composer install
```

### 3. Configurar el archivo de entorno

```bash
cp .env.example .env
```

En Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

Edita `.env` y ajusta la conexión a la base de datos:

```env
APP_NAME=Invensys
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invensys
DB_USERNAME=root
DB_PASSWORD=
```

> **¿Prefieres SQLite?** No necesitas servidor de base de datos. Comenta las líneas de MySQL y
> descomenta estas:
>
> ```env
> DB_CONNECTION=sqlite
> DB_DATABASE=database/database.sqlite
> ```
>
> Recuerda crear el archivo vacío: `touch database/database.sqlite`.

### 4. Crear la base de datos

Con MySQL:

```sql
CREATE DATABASE invensys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Generar la clave de aplicación

```bash
php artisan key:generate
```

### 6. Instalar las dependencias de JavaScript

```bash
npm install
```

### 7. Compilar los assets

```bash
npm run build
```

> Si vas a desarrollar, usa `npm run dev` en vez de `npm run build` para recompilar
> automáticamente los cambios.

### 8. Crear las tablas y cargar los datos iniciales

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y carga:

- **5 categorías** (Médico, Limpieza, Informática, Oficina, Mantención)
- **8 unidades de medida** (Unidad, Caja, Paquete, Resma, Litro, Kilogramo, Metro, Rollo)
- **1 usuario administrador** (ver sección siguiente)

### 9. Levantar el servidor

```bash
php artisan serve
```

Abre <http://localhost:8000> en tu navegador.

---

## Acceso al sistema

El seeder crea un usuario administrador. Define sus datos en `.env` **antes** de ejecutar
`php artisan migrate --seed`:

```env
ADMIN_NAME=Administrador
ADMIN_EMAIL=admin@invensys.cl
ADMIN_PASSWORD=una-clave-larga-y-propia
```

| | |
|---|---|
| **URL** | <http://localhost:8000/login> |
| **Correo** | el que definiste en `ADMIN_EMAIL` |
| **Contraseña** | la que definiste en `ADMIN_PASSWORD` |

> `ADMIN_PASSWORD` es **obligatoria**. Si falta, el seeder se detiene con un error en vez de crear la
> cuenta con una contraseña adivinable. También rechaza claves demasiado cortas o evidente.

No hay registro público: las cuentas se crean desde **Administración → Usuarios**.

### Olvidé mi contraseña

No hay servidor de correo configurado, así que el enlace de recuperación no se puede enviar. La
contraseña se restablece desde la terminal del servidor:

```bash
php artisan usuario:clave admin@invensys.cl
```

Pide la nueva clave de forma oculta y además **cierra las sesiones abiertas** de esa persona, para que
si la clave se cambió porque se filtró, quien la tenía pierde el acceso de inmediato.

Para ejecutarla sin interacción, por ejemplo desde un script:

```bash
php artisan usuario:clave admin@invensys.cl --clave="AlgoMuySeguro123"
```

---

## Cómo funciona el stock

Esta es la parte más importante para entender el sistema.

**El stock no se guarda en ninguna columna.** Es un valor calculado a partir de todos los
movimientos del artículo:

```sql
stock_actual = SUM(entradas + ajustes positivos) - SUM(salidas + ajustes negativos)
```

| Tipo de movimiento | Efecto en el stock |
|---|---|
| `ENTRADA` | Suma |
| `AJUSTE_POSITIVO` | Suma |
| `SALIDA` | Resta |
| `AJUSTE_NEGATIVO` | Resta |

Consecuencias prácticas:

- Los movimientos son **inmutables**: no se editan ni se eliminan desde la interfaz. Para corregir
  un error se registra un ajuste en sentido contrario.
- **Desactivar un artículo no borra su historial.** El campo `activo` solo lo oculta de los
  selectores; el Kardex y la auditoría siguen intactos.
- El cálculo se resuelve con una subconsulta SQL (`selectSub`) en los listados, evitando una
  consulta por fila.
- Un artículo se considera con **stock bajo** cuando su stock actual es menor o igual a su
  `stock_minimo`. El dashboard muestra una alerta con estos artículos.

---

## Módulos del sistema

| Módulo | Ruta | Descripción |
|---|---|---|
| Portada | `/` | Página pública de presentación. |
| Panel | `/dashboard` | KPIs, alertas de stock bajo y últimos movimientos. |
| Artículos | `/articulos` | CRUD con búsqueda, filtros y ficha de detalle. |
| Movimientos | `/movimientos` | Historial, entrada, salida y ajuste de inventario. |
| Personas | `/personas` | Destinatarios de entregas. |
| Kardex | `/kardex` | Historial por artículo con saldo acumulado. |
| Reportes | `/reportes/*` | Stock actual, movimientos por período, entregas por persona. |
| Usuarios | `/usuarios` | *(solo admin)* Gestión de cuentas y roles. |
| Categorías | `/categorias` | *(solo admin en el menú)* Catálogo de categorías. |
| Unidades de medida | `/unidades-medida` | *(solo admin en el menú)* Catálogo de unidades. |
| Contacto | `/contacto` | Solicitud de soporte; la bandeja es *(solo admin)*. |
| Auditoría | `/auditoria` | *(solo admin)* Registro de operaciones. |
| Perfil | `/profile` | Datos de cuenta, contraseña y eliminación. |

---

## Roles y permisos

El sistema tiene dos roles, definidos en la columna `rol` de la tabla `users`:

| Rol | Descripción |
|---|---|
| `usuario` | Acceso al inventario: artículos, movimientos, personas, kardex y reportes. |
| `admin` | Todo lo anterior, más usuarios, auditoría y bandeja de contacto. |

El acceso restringido se controla con el middleware `admin`
(`app/Http/Middleware/EnsureUserIsAdmin.php`), que devuelve un **HTTP 403** si el usuario no es
administrador. Está aplicado a las rutas `usuarios.*`, `auditoria.index`, `contacto.index` y
`contacto.atender`.

> **Nota:** las rutas de `categorias` y `unidades-medida` solo se ocultan del menú para los usuarios
> no administradores, pero no están protegidas con el middleware. Si necesitas restringirlas,
> añade `->middleware('admin')` a ambas en `routes/web.php`.

---

## Estructura del proyecto

```
app/
├── AuditoriaService.php              Registro centralizado de auditoría
├── Http/
│   ├── Controllers/                   Controladores del sistema
│   │   └── Auth/                      Controladores de autenticación (Breeze)
│   ├── Middleware/
│   │   └── EnsureUserIsAdmin.php      Restringe rutas al rol admin
│   ├── Requests/                      Validación de formularios
│   └── ...
├── Models/                            Articulo, Movimiento, Persona, Categoria, ...
├── Services/
│   └── MovimientoService.php          Entradas, salidas y ajustes con validación de stock
└── View/Components/                   AppLayout, GuestLayout

resources/
├── views/
│   ├── articulos/                     Listado, alta, edición y ficha
│   ├── movimientos/                   Historial, entrada, salida y ajuste
│   ├── personas/  categorias/  usuarios/  unidades-medida/
│   ├── kardex/  reportes/  auditorias/  contacto/
│   ├── auth/  profile/                Autenticación y perfil
│   ├── components/                    Componentes Blade reutilizables
│   ├── layouts/                       Layout principal y de invitados
│   ├── dashboard.blade.php
│   └── welcome.blade.php              Portada pública
├── css/app.css                        Punto de entrada de Tailwind
└── js/app.js                          Punto de entrada de Alpine.js

database/
├── migrations/                        Estructura de la base de datos
├── seeders/                           Datos iniciales
└── factories/                         Generadores de datos para los tests

lang/
├── en/                                Mensajes en inglés
└── es/                                Mensajes en español (validación, auth, contraseñas)

tests/
├── Feature/
│   ├── Auth/                          Tests de autenticación (Breeze)
│   ├── InventarioTest.php             Cálculo de stock y validación de salidas
│   ├── VistasTest.php                 Verifica que cada vista se renderiza
│   └── ProfileTest.php
└── Unit/
```

### Tablas principales

| Tabla | Contenido |
|---|---|
| `users` | Usuarios, con `rol` y `activo`. |
| `articulos` | Catálogo: código, categoría, unidad de medida y stock mínimo. |
| `movimientos` | Libro mayor del inventario (entradas, salidas y ajustes). |
| `personas` | Destinatarios de entregas. |
| `categorias` / `unidad_medidas` | Catálogos auxiliares. |
| `auditorias` | Traza de operaciones. |
| `contactos` | Solicitudes de soporte. |

---

## Comandos útiles

| Comando | Descripción |
|---|---|
| `php artisan serve` | Levanta el servidor de desarrollo en `:8000`. |
| `php artisan migrate` | Aplica las migraciones pendientes. |
| `php artisan migrate:rollback` | Revierte la última tanda de migraciones. |
| `php artisan migrate:fresh --seed` | **Recrea** la base de datos desde cero con datos iniciales. |
| `php artisan db:seed` | Carga los datos iniciales (es idempotente). |
| `php artisan tinker` | Consola interactiva de PHP con la app cargada. |
| `php artisan route:list` | Lista todas las rutas registradas. |
| `php artisan optimize:clear` | Limpia todas las cachés. |
| `npm run dev` | Vite en modo desarrollo con recarga automática. |
| `npm run build` | Compila los assets para producción. |
| `php vendor/bin/pint` | Formatea el código PHP. |
| `php artisan test` | Ejecuta la suite de tests. |

---

## Ejecutar los tests

```bash
php artisan test
```

Para ejecutar solo los tests de un archivo:

```bash
php artisan test --filter=InventarioTest
php artisan test --filter=VistasTest
```

La suite corre contra **SQLite en memoria**, así que no toca tu base de datos de desarrollo. Cubre:

- **Autenticación** (Breeze): registro, login, restablecimiento de contraseña y verificación.
- **Inventario** (`InventarioTest`): cálculo de stock, entradas, salidas, ajustes, rechazo de
  salidas sin stock disponible y desactivación sin pérdida de historial.
- **Vistas** (`VistasTest`): que cada pantalla del sistema se renderice correctamente y que las
  rutas de administración estén protegidas.

Para generar un reporte de cobertura se requiere Xdebug o PCOV:

```bash
php artisan test --coverage
```

---

## Desarrollo

### Instalar todo de una vez

Laravel incluye un script que automatiza los pasos 2 a 7 (dependencias de PHP, `.env`,
`key:generate`, migraciones, `npm install` y `npm run build`):

```bash
composer run setup
```

Para completar la instalación, carga los datos iniciales:

```bash
php artisan db:seed
```

### Modo desarrollo

```bash
composer run dev
```

Levanta en paralelo el servidor, la cola de trabajos, el monitor de logs y Vite con recarga
automática.

### Añadir un campo al modelo

```bash
php artisan make:migration add_fecha_vencimiento_to_articulos_table
php artisan migrate
```

Después de modificar un modelo, no olvides añadir el campo a `$fillable` y a `$casts`.

### Publicar las vistas de paginación

Si alguna vez desaparecen los estilos de la paginación, vuelve a publicarlas:

```bash
php artisan vendor:publish --tag=laravel-pagination
```

> Estas vistas viven en `resources/views/vendor/pagination/`, dentro de la carpeta que Tailwind
> escanea. Si se eliminan del proyecto, la paginación se renderiza sin estilos.

### Variables de entorno relevantes

| Variable | Por defecto | Descripción |
|---|---|---|
| `APP_NAME` | `Invensys` | Nombre mostrado en la interfaz. |
| `APP_LOCALE` | `es` | Idioma de la interfaz y los mensajes. |
| `APP_TIMEZONE` | `America/Santiago` | Zona horaria de las fechas. |
| `DB_*` | MySQL en `127.0.0.1` | Conexión a la base de datos. |
| `ADMIN_*` | `admin@invensys.cl` | Usuario administrador que crea el seeder. |

---

## Preguntas frecuentes

**¿No encuentro la categoría o unidad de medida que necesito?**
Créala primero en *Administración → Categorías* o *Unidades de medida*. Los selectores de artículos
solo muestran las que están activas.

**¿Cómo corrijo un movimiento mal registrado?**
Los movimientos no se editan. Registra un ajuste de inventario en sentido contrario desde
*Movimientos → Ajuste de inventario*, dejando la explicación en el campo de referencia u
observaciones.

**Ingresé un artículo pero su stock es 0.**
Es el comportamiento esperado: el stock se construye con los movimientos. Ve a
*Movimientos → Nueva entrada* para registrar el stock inicial.

**¿Aparecen mensajes de error en inglés?**
Revisa que `APP_LOCALE=es` en tu `.env` y que la carpeta `lang/es/` exista. Si acabas de cambiarlo,
ejecuta `php artisan optimize:clear`.

**¿La paginación aparece sin estilos?**
Publica las vistas de paginación (ver sección anterior) y recompila con `npm run build`.

**¿Cómo creo un usuario administrador adicional?**
Edítalo en *Administración → Usuarios* y cambia su rol a `admin`.

**¿Se puede volver a abrir el registro público?**
No es recomendable. La ruta `POST /register` estaba abierta y cualquier persona que conociera la URL
podía crearse una cuenta activa y entrar a inventario, mensajería y reportes. Si realmente hace
falta, descomenta las dos líneas comentadas en `routes/auth.php` y ajusta el middleware `verified`.

---

## Puesta en producción

Checklist para sacar Invensys de un equipo de desarrollo.

### 1. `.env`

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://inventario.ejemplo.cl
LOG_LEVEL=warning

# Si el sitio va por HTTPS
SESSION_SECURE_COOKIE=true
```

`APP_DEBUG=false` es lo más importante: con `true`, cada error muestra la traza completa y valores del
entorno, incluida `APP_KEY`.

### 2. Instalar dependencias sin paquetes de desarrollo

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 3. Preparar la aplicación

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Permisos

```bash
# En Linux
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 5. Permisos de escritura

Apache necesita escribir en `storage/` y en `bootstrap/cache/`. En Windows se concede el permiso al
usuario que corre el servicio, no a todos.

### 6. Solo `public/` debe quedar expuesto

Es el punto más fácil de ocultar y el más grave si se olvida.

Si el DocumentRoot de Apache apunta a la raíz del proyecto, cualquiera podría descargar
`/.env` —con `APP_KEY` y la contraseña de la base de datos— además de `/app/`, `/database/` y
`/storage/logs/laravel.log`. El archivo `.htaccess` de la raíz del proyecto cierra esa puerta.

Lo correcto sigue siendo apuntar el vhost directamente a `public/`:

```apache
<VirtualHost *:80>
    ServerName inventario.ejemplo.cl
    DocumentRoot "C:/xampp/htdocs/invensys/public"

    <Directory "C:/xampp/htdocs/invensys/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 7. HTTPS

Sin HTTPS las contraseñas y cookies viajan en texto plano. Con Apache:

```bash
# Certbot en Linux
sudo certbot --apache -d inventario.ejemplo.cl
```

### 8. Lista de verificación

```bash
# Debe responder 403 o 404. Si devuelve el archivo, hay un problema grave.
curl -I https://inventario.ejemplo.cl/.env

# La aplicación debe seguir respondiendo
curl -I https://inventario.ejemplo.cl/login

# Comprobar que se puede instalar en producción
composer install --no-dev --dry-run
```

- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` es la URL real, no `localhost`
- [ ] `ADMIN_PASSWORD` definida y no obvia
- [ ] `/.env` inaccesible por web
- [ ] Migraciones ejecutadas
- [ ] Assets compilados
- [ ] HTTPS activo
- [ ] Copia de seguridad de MySQL configurada

---

## Licencia
Proyecto con fines educativos.
