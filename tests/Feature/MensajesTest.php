<?php

use App\Models\Conversacion;
use App\Models\Mensaje;
use App\Models\User;

function usuarioParticipante(): array
{
    return [
        User::factory()->create(['name' => 'Ana']),
        User::factory()->create(['name' => 'Beto']),
    ];
}

function iniciarConversacion(User $emisor, User $receptor, ?string $cuerpo = null): Conversacion
{
    $conversacion = Conversacion::create([
        'usuario_emisor_id' => $emisor->id,
        'usuario_receptor_id' => $receptor->id,
    ]);

    if ($cuerpo) {
        Mensaje::create([
            'conversacion_id' => $conversacion->id,
            'usuario_id' => $emisor->id,
            'cuerpo' => $cuerpo,
        ]);

        $conversacion->update(['ultimo_mensaje_en' => now()]);
    }

    return $conversacion;
}

test('la bandeja de mensajes se renderiza vacía', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('mensajes.index'))
        ->assertOk()
        ->assertSee('No tienes conversaciones todavía');
});

test('la bandeja destaca el apartado de mensajes sin responder', function () {
    [$ana, $beto] = usuarioParticipante();

    iniciarConversacion($ana, $beto, '¿Tienes los insumos listos?');

    $this->actingAs($beto)
        ->get(route('mensajes.index'))
        ->assertOk()
        ->assertSee('Sin responder')
        ->assertSee('Ana');
});

test('los mensajes recibidos se marcan como leidos al abrir la conversacion', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Hola');

    Mensaje::create([
        'conversacion_id' => $conversacion->id,
        'usuario_id' => $beto->id,
        'cuerpo' => 'Hola, Ana',
    ]);

    $this->actingAs($beto)
        ->get(route('mensajes.show', $conversacion))
        ->assertOk();

    expect($beto->fresh()->mensajesNoLeidos())->toBe(0);
});

test('un usuario no puede abrir la conversacion de otro', function () {
    [$ana, $beto] = usuarioParticipante();
    $otro = User::factory()->create();

    $conversacion = iniciarConversacion($ana, $beto, 'Privado');

    $this->actingAs($otro)
        ->get(route('mensajes.show', $conversacion))
        ->assertForbidden();

    $this->actingAs($otro)
        ->get(route('mensajes.listado', $conversacion))
        ->assertForbidden();

    $this->actingAs($otro)
        ->postJson(route('mensajes.enviar', $conversacion), ['cuerpo' => 'Hola'])
        ->assertForbidden();
});

test('iniciar una conversacion crea el hilo y el primer mensaje', function () {
    [$ana, $beto] = usuarioParticipante();

    $this->actingAs($ana)
        ->post(route('mensajes.store'), [
            'usuario_receptor_id' => $beto->id,
            'cuerpo' => 'Necesito un favor',
        ])
        ->assertRedirect();

    $conversacion = Conversacion::entre($ana->id, $beto->id);

    expect($conversacion)->not->toBeNull()
        ->and($conversacion->mensajes()->count())->toBe(1)
        ->and($beto->fresh()->mensajesNoLeidos())->toBe(1);
});

test('iniciar una conversacion con alguien con quien ya se habia hablado reutiliza el hilo', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Primer mensaje');

    $this->actingAs($beto)
        ->post(route('mensajes.store'), [
            'usuario_receptor_id' => $ana->id,
            'cuerpo' => 'Segundo mensaje',
        ])
        ->assertRedirect(route('mensajes.show', $conversacion));

    expect(Conversacion::count())->toBe(1)
        ->and($conversacion->mensajes()->count())->toBe(2);
});

test('no se puede iniciar una conversacion consigo mismo', function () {
    $ana = User::factory()->create();

    $this->actingAs($ana)
        ->post(route('mensajes.store'), [
            'usuario_receptor_id' => $ana->id,
            'cuerpo' => 'Hola a mí mismo',
        ])
        ->assertSessionHasErrors('usuario_receptor_id');

    expect(Conversacion::count())->toBe(0);
});

test('no se puede iniciar una conversacion con un usuario inactivo', function () {
    $ana = User::factory()->create();
    $inactivo = User::factory()->create(['activo' => false]);

    $this->actingAs($ana)
        ->post(route('mensajes.store'), [
            'usuario_receptor_id' => $inactivo->id,
            'cuerpo' => 'Hola',
        ])
        ->assertSessionHasErrors('usuario_receptor_id');
});

test('el sondeo devuelve solo los mensajes nuevos', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Primero');

    $this->actingAs($beto)
        ->getJson(route('mensajes.listado', $conversacion))
        ->assertOk()
        ->assertJsonCount(1, 'mensajes');

    Mensaje::create([
        'conversacion_id' => $conversacion->id,
        'usuario_id' => $ana->id,
        'cuerpo' => 'Segundo',
    ]);

    $this->actingAs($beto)
        ->getJson(route('mensajes.listado', ['conversacion' => $conversacion, 'despues' => 1]))
        ->assertOk()
        ->assertJsonCount(1, 'mensajes')
        ->assertJsonPath('mensajes.0.cuerpo', 'Segundo');
});

test('el sondeo informa cuantos mensajes quedan sin leer', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Uno');
    Mensaje::create([
        'conversacion_id' => $conversacion->id,
        'usuario_id' => $ana->id,
        'cuerpo' => 'Dos',
    ]);

    $this->actingAs($beto)
        ->getJson(route('mensajes.listado', $conversacion))
        ->assertOk()
        ->assertJsonPath('sin_responder', 0);
});

test('enviar un mensaje lo registra y actualiza el contador', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto);

    $this->actingAs($ana)
        ->postJson(route('mensajes.enviar', $conversacion), ['cuerpo' => 'Buen día'])
        ->assertCreated()
        ->assertJsonPath('mensaje.cuerpo', 'Buen día')
        ->assertJsonPath('mensaje.propio', true)
        ->assertJsonPath('sin_responder', 0);

    expect($conversacion->fresh()->ultimo_mensaje_en)->not->toBeNull()
        ->and($beto->fresh()->mensajesNoLeidos())->toBe(1);
});

test('no se puede enviar un mensaje vacio', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto);

    $this->actingAs($ana)
        ->postJson(route('mensajes.enviar', $conversacion), ['cuerpo' => '   '])
        ->assertJsonValidationErrors('cuerpo');

    expect($conversacion->mensajes()->count())->toBe(0);
});

test('el contador de la barra de navegacion muestra los mensajes pendientes', function () {
    [$ana, $beto] = usuarioParticipante();

    iniciarConversacion($ana, $beto, '¿Puedes revisar el stock?');

    $this->actingAs($beto)
        ->get(route('mensajes.index'))
        ->assertOk()
        ->assertSee('data-contador-mensajes', false);
});

test('los usuarios sin sesion son redirigidos al login', function () {
    $this->get(route('mensajes.index'))->assertRedirect(route('login'));
    $this->get(route('mensajes.create'))->assertRedirect(route('login'));
});
