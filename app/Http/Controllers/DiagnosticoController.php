<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DiagnosticoController extends Controller
{
    /**
     * Página de diagnóstico del sistema.
     *
     * Reúne en un solo lugar las comprobaciones que suelen faltar cuando la
     * aplicación "no anda" en un servidor nuevo: base de datos, permisos de
     * escritura, assets compilados y configuración de producción.
     */
    public function __invoke()
    {
        return view('diagnostico', [
            'chequeos' => $this->chequeos(),
        ]);
    }

    /**
     * @return array<int, array{nombre: string, estado: string, detalle: string}>
     */
    private function chequeos(): array
    {
        return [
            $this->baseDeDatos(),
            $this->migracionesPendientes(),
            $this->escritura('storage/framework', 'storage/framework'),
            $this->escritura('storage/logs', 'storage/logs'),
            $this->escritura('bootstrap/cache', 'bootstrap/cache'),
            $this->assetsCompilados(),
            $this->claveAplicacion(),
            $this->debugProduccion(),
            $this->versionPhp(),
        ];
    }

    private function baseDeDatos(): array
    {
        try {
            $conectados = DB::select('select 1 as ok');

            if ($conectados === []) {
                throw new \RuntimeException('La consulta no devolvió filas.');
            }

            return $this->ok(
                'Base de datos',
                'Conexión establecida con '.config('database.connections.mysql.database').'.'
            );
        } catch (\Throwable $e) {
            return $this->error(
                'Base de datos',
                'Sin conexión: '.$e->getMessage()
            );
        }
    }

    private function migracionesPendientes(): array
    {
        try {
            $migrator = app('migrator');

            if (! $migrator->getRepository()->hasRun()) {
                return $this->error(
                    'Migraciones',
                    'La tabla de migraciones no existe: ejecuta php artisan migrate.'
                );
            }

            $ejecutadas = array_flip(
                DB::table('migrations')->pluck('migration')->all()
            );

            $pendientes = array_values(array_filter(
                $migrator->getMigrationFiles($migrator->paths()),
                fn ($archivo) => ! isset($ejecutadas[basename($archivo, '.php')])
            ));

            if ($pendientes === []) {
                return $this->ok('Migraciones', 'No hay migraciones pendientes.');
            }

            return $this->advertencia(
                'Migraciones',
                count($pendientes).' pendiente(s): '.implode(', ', array_map(
                    fn ($archivo) => basename($archivo, '.php'),
                    $pendientes
                ))
            );
        } catch (\Throwable $e) {
            return $this->error('Migraciones', 'No se pudo verificar: '.$e->getMessage());
        }
    }

    private function escritura(string $ruta, string $nombre): array
    {
        $absoluta = base_path($ruta);

        if (! is_dir($absoluta)) {
            return $this->error($nombre, 'El directorio no existe.');
        }

        if (! is_writable($absoluta)) {
            return $this->error($nombre, 'El directorio no tiene permisos de escritura.');
        }

        return $this->ok($nombre, 'Directorio escribible.');
    }

    private function assetsCompilados(): array
    {
        if (! file_exists(public_path('build/manifest.json'))) {
            return $this->error(
                'Assets compilados',
                'Falta public/build: ejecuta npm install && npm run build.'
            );
        }

        return $this->ok('Assets compilados', 'Front-end compilado correctamente.');
    }

    private function claveAplicacion(): array
    {
        if (! config('app.key')) {
            return $this->error(
                'Clave de aplicación',
                'Falta APP_KEY: ejecuta php artisan key:generate.'
            );
        }

        return $this->ok('Clave de aplicación', 'APP_KEY configurada.');
    }

    private function debugProduccion(): array
    {
        if (config('app.debug')) {
            return $this->advertencia(
                'Modo debug',
                'APP_DEBUG está activo: en producción se muestran detalles sensibles. Desactívalo en .env.'
            );
        }

        return $this->ok('Modo debug', 'APP_DEBUG desactivado.');
    }

    private function versionPhp(): array
    {
        $version = PHP_VERSION;

        if (version_compare($version, '8.2.0', '<')) {
            return $this->error('Versión de PHP', $version.' — se requiere 8.2 o superior.');
        }

        return $this->ok('Versión de PHP', $version.'.');
    }

    private function ok(string $nombre, string $detalle): array
    {
        return [
            'nombre' => $nombre,
            'estado' => 'ok',
            'detalle' => $detalle,
        ];
    }

    private function advertencia(string $nombre, string $detalle): array
    {
        return [
            'nombre' => $nombre,
            'estado' => 'advertencia',
            'detalle' => $detalle,
        ];
    }

    private function error(string $nombre, string $detalle): array
    {
        return [
            'nombre' => $nombre,
            'estado' => 'error',
            'detalle' => $detalle,
        ];
    }
}
