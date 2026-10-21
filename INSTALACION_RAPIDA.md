# INSTALACION RAPIDA

## Requisitos
- PHP 8.2+
- Composer
- Node.js y NPM
- MySQL/MariaDB o SQLite

## Pasos
1. Copiar `.env.example` a `.env`
2. `composer install`
3. `npm install && npm run build`
4. `php artisan key:generate`
5. Configurar base de datos en `.env`
6. `php artisan migrate --force`
7. `php artisan db:seed --force` (requiere `ADMIN_PASSWORD` en `.env`)
8. `php artisan serve`

## Primer uso
- Al iniciar sesión con un usuario sin organización, el sistema te pedirá crear tu empresa/negocio.
- Puedes personalizarla desde el menú de usuario > Mi empresa.
- También puedes agregar más empresas desde el mismo menú.

## Notas
- En producción poner `APP_DEBUG=false`
- Los datos de inventario quedan aislados por empresa
- Las migraciones ya incluyen todo lo necesario
