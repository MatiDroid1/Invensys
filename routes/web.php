<?php

use App\Http\Controllers\AcercaController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ConversacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/acerca', AcercaController::class)
        ->name('acerca');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('articulos', ArticuloController::class);
    Route::resource('movimientos', MovimientoController::class)
        ->only(['index', 'create', 'store']);
    Route::get(
        'movimientos/salida/create',
        [MovimientoController::class, 'createSalida']
    )->name('movimientos.salida.create');

    Route::post(
        'movimientos/salida',
        [MovimientoController::class, 'storeSalida']
    )->name('movimientos.salida.store');
    Route::resource('personas', PersonaController::class)
        ->except(['show']);
    Route::get('/kardex', [KardexController::class, 'index'])
        ->name('kardex.index');
    Route::resource('categorias', CategoriaController::class)
        ->except(['show']);
    Route::resource('unidades-medida', UnidadMedidaController::class)
        ->except(['show']);
    Route::resource('usuarios', UsuarioController::class)
        ->except(['show'])
        ->middleware('admin');
    Route::get('/reportes/stock', [ReporteController::class, 'stock'])
        ->name('reportes.stock');
    Route::get('/reportes/stock.csv', [ReporteController::class, 'stockCsv'])
        ->name('reportes.stock.csv');
    Route::get('/reportes/movimientos', [ReporteController::class, 'movimientos'])
        ->name('reportes.movimientos');
    Route::get('/reportes/movimientos.csv', [ReporteController::class, 'movimientosCsv'])
        ->name('reportes.movimientos.csv');
    Route::get('/reportes/entregas-persona', [ReporteController::class, 'entregasPersona'])
        ->name('reportes.entregas-persona');
    Route::get('/reportes/entregas-persona.csv', [ReporteController::class, 'entregasPersonaCsv'])
        ->name('reportes.entregas-persona.csv');
    Route::get(
        'movimientos/ajuste/create',
        [MovimientoController::class, 'createAjuste']
    )->name('movimientos.ajuste.create');

    Route::post(
        'movimientos/ajuste',
        [MovimientoController::class, 'storeAjuste']
    )->name('movimientos.ajuste.store');
    Route::get('/contacto', [ContactoController::class, 'create'])
        ->name('contacto.create');

    Route::post('/contacto', [ContactoController::class, 'store'])
        ->name('contacto.store');

    Route::get('/contacto/administracion', [ContactoController::class, 'index'])
        ->middleware('admin')
        ->name('contacto.index');

    Route::patch('/contacto/{id}/atender', [ContactoController::class, 'atender'])
        ->middleware('admin')
        ->name('contacto.atender');

    Route::get('/auditoria', [AuditoriaController::class, 'index'])
        ->middleware('admin')
        ->name('auditoria.index');

    // Debe declararse antes de /mensajes/{conversacion} para que "nueva" no
    // se interprete como el identificador de una conversación.
    Route::get('/mensajes/nueva', [ConversacionController::class, 'create'])
        ->name('mensajes.create');

    // Sondeo global: disponible en cualquier pantalla para detectar mensajes
    // entrantes sin necesidad de abrir el chat. Va antes de
    // /mensajes/{conversacion} para que "estado" no se tome como conversación.
    Route::get('/mensajes/estado', [MensajeController::class, 'estado'])
        ->name('mensajes.estado');

    Route::patch('/mensajes/sonido', [MensajeController::class, 'sonido'])
        ->name('mensajes.sonido');

    Route::get('/mensajes', [ConversacionController::class, 'index'])
        ->name('mensajes.index');

    Route::post('/mensajes', [ConversacionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('mensajes.store');

    Route::get('/mensajes/{conversacion}', [ConversacionController::class, 'show'])
        ->name('mensajes.show');

    Route::get('/mensajes/{conversacion}/listado', [MensajeController::class, 'index'])
        ->name('mensajes.listado');

    Route::post('/mensajes/{conversacion}/listado', [MensajeController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('mensajes.enviar');
});

require __DIR__.'/auth.php';
