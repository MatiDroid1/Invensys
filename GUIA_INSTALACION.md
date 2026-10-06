# Guía de instalación — Invensys

Instalación exprés en una máquina nueva. El detalle de cada paso está en el
`README.md` (sección *Instalación*); esta guía es el chequeo rápido.

## Requisitos

| Requisito | Versión mínima |
|-----------|----------------|
| PHP | 8.2 (con `pdo_mysql`, `mbstring`, `openssl`, `gd` o `intl`) |
| Composer | 2.x |
| Node.js + npm | 18+ / 9+ |
| MySQL o MariaDB | 5.7+ / 10.4+ |
| Servidor web | Apache (XAMPP sirve) o `php artisan serve` |

## Pasos

```bash
# 1. Dependencias de PHP
composer install

# 2. Archivo de entorno y clave
copy .env.example .env      # en Linux/macOS: cp .env.example .env
php artisan key:generate

# 3. Base de datos: crearla en phpMyAdmin o por consola
mysql -u root -e "CREATE DATABASE invensys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Ajustar credenciales en .env si es necesario
#    DB_DATABASE=invensys  DB_USERNAME=root  DB_PASSWORD=

# 5. Front-end
npm install
npm run build

# 6. Tablas y datos iniciales
php artisan migrate --seed

# 7. Levantar (si no se usa Apache)
php artisan serve
```

## Acceso inicial

El usuario administrador se crea desde el seeder y **exige** la variable
`ADMIN_PASSWORD` con una clave fuerte (12+ caracteres, mayúscula, número y
símbolo):

```bash
ADMIN_PASSWORD="TuClaveSegura.2026" php artisan migrate --seed
```

Si el seeder ya corrió y no recuerdas la clave:

```bash
php artisan usuario:clave admin@invensys.cl
```

## Verificación final

1. Abre la aplicación y entra con el usuario administrador.
2. Entra a **Más → Administración → Diagnóstico** (`/diagnostico`).
3. Todos los chequeos deben aparecer en **Correcto**:

   - Base de datos
   - Migraciones (sin pendientes)
   - `storage/framework`, `storage/logs`, `bootstrap/cache` escribibles
   - Assets compilados (`public/build`)
   - Clave de aplicación
   - Modo debug desactivado en producción
   - Versión de PHP ≥ 8.2

## Problemas comunes

| Sintoma | Causa y solución |
|---------|------------------|
| Página sin estilos | Falta el build: `npm install && npm run build` |
| Error 500 al abrir | Revisar `storage/logs/laravel.log`; casi siempre es `.env` sin `APP_KEY` o credenciales de BD incorrectas |
| 404 en todas las rutas con Apache | El `.htaccess` de la raíz exige servir desde `public/`; revisar la sección *Deploy en XAMPP* del README |
| `No application encryption key` | `php artisan key:generate` |
| Los estilos se pierden tras limpiar | `npm run build` regenera `public/build` |
| Cambios en vistas no se ven | `php artisan view:clear` (vistas en cache) |
| Cambios en `.env` no se aplican | `php artisan config:clear` |

## Actualizar una instalación existente

```bash
git pull
composer install
npm install && npm run build
php artisan migrate --force
php artisan config:clear && php artisan view:clear
```

Verifica el resultado en `/diagnostico` después de cada actualización.
