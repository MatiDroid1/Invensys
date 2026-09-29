<?php

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Persona;
use App\Models\UnidadMedida;
use App\Models\User;

/**
 * Comprueba que cada vista del sistema se renderiza sin errores.
 */
function catalogoCompleto(): array
{
    $categoria = Categoria::factory()->create();
    $unidad = UnidadMedida::factory()->create();

    $articulo = Articulo::factory()->create([
        'categoria_id' => $categoria->id,
        'unidad_medida_id' => $unidad->id,
    ]);

    $persona = Persona::create([
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'identificador' => 'RUT-123',
        'email' => 'juan@example.com',
        'area' => 'Operaciones',
        'cargo' => 'Analista',
    ]);

    return [$articulo, $categoria, $unidad, $persona];
}

test('la portada pública se renderiza', function () {
    $this->get('/')->assertOk();
});

test('el dashboard se renderiza para un usuario autenticado', function () {
    catalogoCompleto();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk();
});

test('el listado de artículos se renderiza con y sin resultados', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('articulos.index'))
        ->assertOk()
        ->assertSee('No hay artículos registrados');

    catalogoCompleto();

    $this->actingAs($usuario)
        ->get(route('articulos.index'))
        ->assertOk();
});

test('el listado de artículos acepta los filtros de búsqueda', function () {
    catalogoCompleto();

    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->get(route('articulos.index', ['q' => 'inexistente-xyz']))
        ->assertOk();

    $this->actingAs($usuario)
        ->get(route('articulos.index', ['estado' => 'activos']))
        ->assertOk();

    $this->actingAs($usuario)
        ->get(route('articulos.index', ['estado' => 'inactivos']))
        ->assertOk();
});

test('las vistas de artículo se renderizan', function () {
    [$articulo] = catalogoCompleto();

    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('articulos.index'))->assertOk();
    $this->actingAs($usuario)->get(route('articulos.create'))->assertOk();
    $this->actingAs($usuario)->get(route('articulos.show', $articulo))->assertOk();
    $this->actingAs($usuario)->get(route('articulos.edit', $articulo))->assertOk();
});

test('el formulario de artículo muestra las unidades de medida', function () {
    $usuario = User::factory()->create();

    // Regresión: la vista iteraba $unidadesMedida, pero el controlador entregaba $unidades.
    $this->actingAs($usuario)
        ->get(route('articulos.create'))
        ->assertOk()
        ->assertDontSee('Undefined variable');
});

test('las vistas de movimiento se renderizan', function () {
    catalogoCompleto();

    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('movimientos.index'))->assertOk();
    $this->actingAs($usuario)->get(route('movimientos.create'))->assertOk();
    $this->actingAs($usuario)->get(route('movimientos.salida.create'))->assertOk();
    $this->actingAs($usuario)->get(route('movimientos.ajuste.create'))->assertOk();
});

test('el formulario de salida preselecciona el artículo recibido por query string', function () {
    [$articulo] = catalogoCompleto();

    $usuario = User::factory()->create();

    $contenido = $this->actingAs($usuario)
        ->get(route('movimientos.salida.create', ['articulo_id' => $articulo->id]))
        ->assertOk()
        ->getContent();

    // Blade escribe los atributos en varias líneas: normalizamos para comparar.
    $normalizado = preg_replace('/\s+/', ' ', $contenido);

    expect($normalizado)->toContain('value="'.$articulo->id.'" selected');
});

test('el formulario de salida no preselecciona nada sin query string', function () {
    catalogoCompleto();

    $contenido = $this->actingAs(User::factory()->create())
        ->get(route('movimientos.salida.create'))
        ->assertOk()
        ->getContent();

    expect(preg_replace('/\s+/', ' ', $contenido))->not->toContain('" selected');
});

test('las vistas de personas, categorías y unidades se renderizan', function () {
    [$articulo, $categoria, $unidad, $persona] = catalogoCompleto();

    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('personas.index'))->assertOk();
    $this->actingAs($usuario)->get(route('personas.create'))->assertOk();
    $this->actingAs($usuario)->get(route('personas.edit', $persona))->assertOk();

    $this->actingAs($usuario)->get(route('categorias.index'))->assertOk();
    $this->actingAs($usuario)->get(route('categorias.create'))->assertOk();
    $this->actingAs($usuario)->get(route('categorias.edit', $categoria))->assertOk();

    $this->actingAs($usuario)->get(route('unidades-medida.index'))->assertOk();
    $this->actingAs($usuario)->get(route('unidades-medida.create'))->assertOk();
    $this->actingAs($usuario)->get(route('unidades-medida.edit', $unidad->id))->assertOk();
});

test('la vista de kardex se renderiza con y sin artículo', function () {
    [$articulo] = catalogoCompleto();

    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('kardex.index'))->assertOk();

    $this->actingAs($usuario)
        ->get(route('kardex.index', ['articulo_id' => $articulo->id]))
        ->assertOk();
});

test('los reportes se renderizan', function () {
    catalogoCompleto();

    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get(route('reportes.stock'))->assertOk();
    $this->actingAs($usuario)->get(route('reportes.movimientos'))->assertOk();
    $this->actingAs($usuario)->get(route('reportes.entregas-persona'))->assertOk();
});

test('la vista de contacto se renderiza', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('contacto.create'))
        ->assertOk();
});

test('las vistas de administración se renderizan solo para administradores', function () {
    [$categoria, $unidad] = catalogoCompleto();

    $admin = User::factory()->create(['rol' => 'admin']);
    $usuario = User::factory()->create(['rol' => 'usuario']);

    $this->actingAs($admin)->get(route('usuarios.index'))->assertOk();
    $this->actingAs($admin)->get(route('usuarios.create'))->assertOk();
    $this->actingAs($admin)->get(route('auditoria.index'))->assertOk();
    $this->actingAs($admin)->get(route('contacto.index'))->assertOk();

    $this->actingAs($usuario)->get(route('usuarios.index'))->assertForbidden();
    $this->actingAs($usuario)->get(route('auditoria.index'))->assertForbidden();
    $this->actingAs($usuario)->get(route('contacto.index'))->assertForbidden();
});

test('los usuarios sin sesión son redirigidos al login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('articulos.index'))->assertRedirect(route('login'));
    $this->get(route('kardex.index'))->assertRedirect(route('login'));
});

test('el menú de navegación se muestra en español', function () {
    catalogoCompleto();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Movimientos')
        ->assertSee('Kardex')
        ->assertSee('Reportes');
});
