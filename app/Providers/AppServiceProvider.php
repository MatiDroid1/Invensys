<?php

namespace App\Providers;

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
        // El contador de mensajes sin leer vive en la barra de navegación, que
        // se incluye desde el layout, así que se comparte desde ahí.
        View::composer('layouts.app', function ($view) {
            $view->with('mensajesNoLeidos', auth()->check()
                ? auth()->user()->mensajesNoLeidos()
                : 0);
        });
    }
}
