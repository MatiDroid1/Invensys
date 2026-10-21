<?php

namespace App\Providers;

use App\Enums\Permiso;
use App\Models\Articulo;
use App\Models\Persona;
use App\Models\User;
use App\Policies\ArticuloPolicy;
use App\Policies\MovimientoPolicy;
use App\Policies\PersonaPolicy;
use App\Policies\UsuarioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registrarPermisos();
        $this->registrarPolicies();

        // El contador de mensajes sin leer vive en la barra de navegación, que
        // se incluye desde el layout, así que se comparte desde ahí.
        View::composer('layouts.app', function ($view) {
            $view->with('mensajesNoLeidos', auth()->check()
                ? auth()->user()->mensajesNoLeidos()
                : 0);

            // El badge de stock bajo se muestra en la barra de navegación.
            $view->with('alertasStock', auth()->check()
                ? Articulo::stockBajo()->count()
                : 0);
        });
    }

    /**
     * Registra un Gate por cada permiso del vocabulario.
     *
     * Así `Gate::allows('articulos.editar')` y `@can('articulos.editar')`
     * funcionan en cualquier parte, y `User::puede()` usa exactamente la
     * misma fuente de verdad.
     */
    private function registrarPermisos(): void
    {
        foreach (Permiso::cases() as $permiso) {
            Gate::define(
                $permiso->value,
                fn ($usuario) => $usuario !== null && $usuario->puede($permiso)
            );
        }
    }

    /**
     * Declara las Policies de forma explícita.
     *
     * Laravel adivina el nombre de la policy por convención, pero declararla
     * aquí deja la relación a la vista y evita depender de esa adivinanza.
     */
    private function registrarPolicies(): void
    {
        Gate::policy(Articulo::class, ArticuloPolicy::class);
        Gate::policy(Movimiento::class, MovimientoPolicy::class);
        Gate::policy(Persona::class, PersonaPolicy::class);
        Gate::policy(User::class, UsuarioPolicy::class);
    }
}
