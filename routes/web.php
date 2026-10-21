<?php

use App\Http\Controllers\AcercaController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ArticuloImportacionController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ConversacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\OrganizacionController;
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
    Route::get('/mi-empresa/crear', [OrganizacionController::class, 'crear'])
        ->name('organizaciones.crear');
    Route::post('/mi-empresa/crear', [OrganizacionController::class, 'guardar'])
        ->name('organizaciones.guardar');
    Route::get('/mi-empresa/cambiar/{organizacion}', [OrganizacionController::class, 'seleccionar'])
        ->name('organizaciones.seleccionar');
    Route::get('/mi-empresa', [OrganizacionController::class, 'editar'])
        ->name('organizaciones.editar');
    Route::put('/mi-empresa', [OrganizacionController::class, 'actualizar'])
        ->name('organizaciones.actualizar');
    Route::patch('/mi-empresa', [OrganizacionController::class, 'actualizar']);

    Route::get('/acerca', AcercaController::class)
        ->name('acerca');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Artículos
    |--------------------------------------------------------------------------
    |
    | Las rutas se declaran una por una en vez de usar `Route::resource` para
    | poder colgarlas del permiso concreto que exigen. El nombre de la ruta
    | no cambia, así que las vistas y las pruebas siguen funcionando igual.
    |
    */

    // Importación masiva: se declara antes que el resto de artículos para que
    // "importar" no se interprete como el id de un artículo.
    Route::get('/articulos/importar', [ArticuloImportacionController::class, 'create'])
        ->middleware('permiso:importar')
        ->name('articulos.importar.create');
    Route::post('/articulos/importar', [ArticuloImportacionController::class, 'store'])
        ->middleware('permiso:importar')
        ->name('articulos.importar.store');
    Route::get('/articulos/importar/plantilla', [ArticuloImportacionController::class, 'plantilla'])
        ->middleware('permiso:importar')
        ->name('articulos.importar.plantilla');
    Route::post('/articulos/importar/previsualizar', [ArticuloImportacionController::class, 'previsualizar'])
        ->middleware('permiso:importar')
        ->name('articulos.importar.previsualizar');
    Route::post('/articulos/importar/confirmar', [ArticuloImportacionController::class, 'confirmar'])
        ->middleware('permiso:importar')
        ->name('articulos.importar.confirmar');

    Route::get('/articulos', [ArticuloController::class, 'index'])
        ->middleware('permiso:articulos.ver')
        ->name('articulos.index');
    Route::get('/articulos/create', [ArticuloController::class, 'create'])
        ->middleware('permiso:articulos.crear')
        ->name('articulos.create');
    Route::post('/articulos', [ArticuloController::class, 'store'])
        ->middleware('permiso:articulos.crear')
        ->name('articulos.store');
    Route::get('/articulos/{articulo}', [ArticuloController::class, 'show'])
        ->middleware('permiso:articulos.ver')
        ->name('articulos.show');
    Route::get('/articulos/{articulo}/edit', [ArticuloController::class, 'edit'])
        ->middleware('permiso:articulos.editar')
        ->name('articulos.edit');
    Route::match(['put', 'patch'], '/articulos/{articulo}', [ArticuloController::class, 'update'])
        ->middleware('permiso:articulos.editar')
        ->name('articulos.update');
    Route::delete('/articulos/{articulo}', [ArticuloController::class, 'destroy'])
        ->middleware('permiso:articulos.eliminar')
        ->name('articulos.destroy');

    /*
    |--------------------------------------------------------------------------
    | Movimientos
    |--------------------------------------------------------------------------
    */

    Route::get('/movimientos', [MovimientoController::class, 'index'])
        ->middleware('permiso:movimientos.ver')
        ->name('movimientos.index');
    Route::get('/movimientos/create', [MovimientoController::class, 'create'])
        ->middleware('permiso:movimientos.entrada')
        ->name('movimientos.create');
    Route::post('/movimientos', [MovimientoController::class, 'store'])
        ->middleware('permiso:movimientos.entrada')
        ->name('movimientos.store');

    Route::get('/movimientos/salida/create', [MovimientoController::class, 'createSalida'])
        ->middleware('permiso:movimientos.salida')
        ->name('movimientos.salida.create');
    Route::post('/movimientos/salida', [MovimientoController::class, 'storeSalida'])
        ->middleware('permiso:movimientos.salida')
        ->name('movimientos.salida.store');

    Route::get('/movimientos/ajuste/create', [MovimientoController::class, 'createAjuste'])
        ->middleware('permiso:movimientos.ajuste')
        ->name('movimientos.ajuste.create');
    Route::post('/movimientos/ajuste', [MovimientoController::class, 'storeAjuste'])
        ->middleware('permiso:movimientos.ajuste')
        ->name('movimientos.ajuste.store');

    // Anulación: no borra, genera el movimiento compensatorio.
    Route::post('/movimientos/{movimiento}/anular', [MovimientoController::class, 'anular'])
        ->middleware('permiso:movimientos.anular')
        ->name('movimientos.anular');

    /*
    |--------------------------------------------------------------------------
    | Personas
    |--------------------------------------------------------------------------
    */

    Route::get('/personas', [PersonaController::class, 'index'])
        ->middleware('permiso:personas.ver')
        ->name('personas.index');
    Route::get('/personas/create', [PersonaController::class, 'create'])
        ->middleware('permiso:personas.crear')
        ->name('personas.create');
    Route::post('/personas', [PersonaController::class, 'store'])
        ->middleware('permiso:personas.crear')
        ->name('personas.store');
    Route::get('/personas/{persona}/edit', [PersonaController::class, 'edit'])
        ->middleware('permiso:personas.editar')
        ->name('personas.edit');
    Route::match(['put', 'patch'], '/personas/{persona}', [PersonaController::class, 'update'])
        ->middleware('permiso:personas.editar')
        ->name('personas.update');
    Route::delete('/personas/{persona}', [PersonaController::class, 'destroy'])
        ->middleware('permiso:personas.eliminar')
        ->name('personas.destroy');

    /*
    |--------------------------------------------------------------------------
    | Catálogos auxiliares y administración
    |--------------------------------------------------------------------------
    */

    Route::get('/kardex', [KardexController::class, 'index'])
        ->middleware('permiso:movimientos.ver')
        ->name('kardex.index');
    Route::get('/kardex/pdf', [KardexController::class, 'pdf'])
        ->middleware('permiso:exportar')
        ->name('kardex.pdf');

    Route::get('/categorias', [CategoriaController::class, 'index'])
        ->middleware('permiso:categorias.gestionar')
        ->name('categorias.index');
    Route::get('/categorias/create', [CategoriaController::class, 'create'])
        ->middleware('permiso:categorias.gestionar')
        ->name('categorias.create');
    Route::post('/categorias', [CategoriaController::class, 'store'])
        ->middleware('permiso:categorias.gestionar')
        ->name('categorias.store');
    Route::get('/categorias/{categoria}/edit', [CategoriaController::class, 'edit'])
        ->middleware('permiso:categorias.gestionar')
        ->name('categorias.edit');
    Route::match(['put', 'patch'], '/categorias/{categoria}', [CategoriaController::class, 'update'])
        ->middleware('permiso:categorias.gestionar')
        ->name('categorias.update');
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])
        ->middleware('permiso:categorias.gestionar')
        ->name('categorias.destroy');

    Route::get('/unidades-medida', [UnidadMedidaController::class, 'index'])
        ->middleware('permiso:unidades.gestionar')
        ->name('unidades-medida.index');
    Route::get('/unidades-medida/create', [UnidadMedidaController::class, 'create'])
        ->middleware('permiso:unidades.gestionar')
        ->name('unidades-medida.create');
    Route::post('/unidades-medida', [UnidadMedidaController::class, 'store'])
        ->middleware('permiso:unidades.gestionar')
        ->name('unidades-medida.store');
    Route::get('/unidades-medida/{unidadMedida}/edit', [UnidadMedidaController::class, 'edit'])
        ->middleware('permiso:unidades.gestionar')
        ->name('unidades-medida.edit');
    Route::match(['put', 'patch'], '/unidades-medida/{unidadMedida}', [UnidadMedidaController::class, 'update'])
        ->middleware('permiso:unidades.gestionar')
        ->name('unidades-medida.update');
    Route::delete('/unidades-medida/{unidadMedida}', [UnidadMedidaController::class, 'destroy'])
        ->middleware('permiso:unidades.gestionar')
        ->name('unidades-medida.destroy');

    Route::get('/usuarios', [UsuarioController::class, 'index'])
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.store');
    Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.edit');
    Route::match(['put', 'patch'], '/usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.update');
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])
        ->middleware('permiso:usuarios.gestionar')
        ->name('usuarios.destroy');

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */

    Route::get('/reportes/stock', [ReporteController::class, 'stock'])
        ->middleware('permiso:articulos.ver')
        ->name('reportes.stock');
    Route::get('/reportes/stock.csv', [ReporteController::class, 'stockCsv'])
        ->middleware('permiso:exportar')
        ->name('reportes.stock.csv');
    Route::get('/reportes/inventario-valorizado.csv', [ReporteController::class, 'inventarioValorizadoCsv'])
        ->middleware('permiso:exportar')
        ->name('reportes.inventario-valorizado.csv');
    Route::get('/reportes/movimientos', [ReporteController::class, 'movimientos'])
        ->middleware('permiso:movimientos.ver')
        ->name('reportes.movimientos');
    Route::get('/reportes/movimientos.csv', [ReporteController::class, 'movimientosCsv'])
        ->middleware('permiso:exportar')
        ->name('reportes.movimientos.csv');
    Route::get('/reportes/entregas-persona', [ReporteController::class, 'entregasPersona'])
        ->middleware('permiso:movimientos.ver')
        ->name('reportes.entregas-persona');
    Route::get('/reportes/entregas-persona.csv', [ReporteController::class, 'entregasPersonaCsv'])
        ->middleware('permiso:exportar')
        ->name('reportes.entregas-persona.csv');

    /*
    |--------------------------------------------------------------------------
    | Mensajería, contacto y soporte
    |--------------------------------------------------------------------------
    */

    Route::get('/contacto', [ContactoController::class, 'create'])
        ->name('contacto.create');

    Route::post('/contacto', [ContactoController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('contacto.store');

    Route::get('/contacto/administracion', [ContactoController::class, 'index'])
        ->middleware('admin')
        ->name('contacto.index');

    Route::patch('/contacto/{id}/atender', [ContactoController::class, 'atender'])
        ->middleware('admin')
        ->name('contacto.atender');

    Route::get('/auditoria', [AuditoriaController::class, 'index'])
        ->middleware('permiso:auditoria.ver')
        ->name('auditoria.index');
    Route::get('/auditoria.csv', [AuditoriaController::class, 'indexCsv'])
        ->middleware('permiso:auditoria.ver')
        ->name('auditoria.csv');

    Route::get('/diagnostico', DiagnosticoController::class)
        ->middleware('admin')
        ->name('diagnostico');

    // Debe declararse antes de /mensajes/{conversacion} para que "nueva" no
    // se interprete como el identificador de una conversación.
    Route::get('/mensajes/nueva', [ConversacionController::class, 'create'])
        ->name('mensajes.create');

    // Sondeo global: disponible en cualquier pantalla para detectar mensajes
    // entrantes sin necesidad de abrir el chat. Va antes de
    // /mensajes/{conversacion} para que "estado" no se tome como conversación.
    Route::get('/mensajes/estado', [MensajeController::class, 'estado'])
        ->middleware('throttle:60,1')
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
