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

test('el chat se abre con el panel de altura fija y el hilo con scroll', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Hola');

    $this->actingAs($ana)
        ->get(route('mensajes.show', $conversacion))
        ->assertOk()
        ->assertSee('h-[calc(100vh-13rem)]', false)
        ->assertSee('flex-1 overflow-y-auto', false)
        ->assertSee('shrink-0', false);
});

test('el chat incluye los controles de envio y el aviso sonoro', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Hola');

    $this->actingAs($ana)
        ->get(route('mensajes.show', $conversacion))
        ->assertOk()
        ->assertSee('alternarSonido', false)
        ->assertSee('window.avisoSonoro', false)
        ->assertSee('document.hidden', false);
});

test('la preferencia de sonido se guarda y se respeta al reabrir el chat', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Hola');

    // Por defecto el sonido viene activo.
    $this->actingAs($ana)
        ->get(route('mensajes.show', $conversacion))
        ->assertOk()
        ->assertSee('sonido: true', false);

    $this->actingAs($ana)
        ->patchJson(route('mensajes.sonido'), ['sonido' => false])
        ->assertOk()
        ->assertJsonPath('sonido', false);

    expect($ana->fresh()->sonido_mensajes)->toBeFalse();

    // Al reabrir, la vista ya arranca con el sonido silenciado.
    $this->actingAs($ana)
        ->get(route('mensajes.show', $conversacion))
        ->assertOk()
        ->assertSee('sonido: false', false);
});

test('la preferencia de sonido puede volver a activarse', function () {
    $ana = User::factory()->create();

    $this->actingAs($ana)
        ->patchJson(route('mensajes.sonido'), ['sonido' => false])
        ->assertOk();

    $this->actingAs($ana)
        ->patchJson(route('mensajes.sonido'), ['sonido' => true])
        ->assertOk()
        ->assertJsonPath('sonido', true);

    expect($ana->fresh()->sonido_mensajes)->toBeTrue();
});

test('la preferencia de sonido exige un valor booleano', function () {
    $ana = User::factory()->create();

    $this->actingAs($ana)
        ->patchJson(route('mensajes.sonido'), ['sonido' => 'quizás'])
        ->assertJsonValidationErrors('sonido');
});

test('el sondeo global informa los mensajes pendientes', function () {
    [$ana, $beto] = usuarioParticipante();

    iniciarConversacion($ana, $beto, '¿Tienes los insumos listos?');

    // El sondeo cada 3 segundos es ligero: no arrastra el resumen de
    // conversaciones, que recién se pide al abrir la bandeja.
    $this->actingAs($beto)
        ->getJson(route('mensajes.estado'))
        ->assertOk()
        ->assertJsonPath('sin_responder', 1)
        ->assertJsonMissingPath('conversaciones');

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado', ['bandeja' => 1]))
        ->assertOk()
        ->assertJsonPath('conversaciones.0.interlocutor', 'Ana')
        ->assertJsonPath('conversaciones.0.no_leidos', 1)
        ->assertJsonPath('conversaciones.0.resumen', '¿Tienes los insumos listos?');
});

test('el sondeo global no marca los mensajes como leídos', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Hola');

    $this->actingAs($beto)->getJson(route('mensajes.estado'))->assertOk();

    // Sondear desde otras pantallas no puede cerrar el hilo: solo el chat lo hace.
    expect($conversacion->mensajes()->whereNull('leido_en')->count())->toBe(1);
});

test('el sondeo global no avisa de mensajes anteriores al primer sondeo', function () {
    [$ana, $beto] = usuarioParticipante();

    iniciarConversacion($ana, $beto, 'Mensaje que ya existía');

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado'))
        ->assertOk()
        ->assertJsonCount(0, 'nuevos')
        ->assertJsonPath('sin_responder', 1);
});

test('el sondeo global avisa lo que llega después del cursor', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Primero');

    $primerSondeo = $this->actingAs($beto)->getJson(route('mensajes.estado'))->json();
    $cursor = $primerSondeo['cursor'];

    expect($primerSondeo['nuevos'])->toBe([]);

    // Llega un mensaje nuevo mientras el usuario está en artículos.
    Mensaje::create([
        'conversacion_id' => $conversacion->id,
        'usuario_id' => $ana->id,
        'cuerpo' => 'Segundo mensaje',
    ]);

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado', ['desde' => $cursor]))
        ->assertOk()
        ->assertJsonCount(1, 'nuevos')
        ->assertJsonPath('nuevos.0.cuerpo', 'Segundo mensaje')
        ->assertJsonPath('nuevos.0.remitente', 'Ana')
        ->assertJsonPath('nuevos.0.interlocutor', 'Ana')
        ->assertJsonPath('sin_responder', 2);

    // Un segundo sondeo con el cursor actualizado ya no repite el aviso.
    $cursorNuevo = Mensaje::max('id');

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado', ['desde' => $cursorNuevo]))
        ->assertOk()
        ->assertJsonCount(0, 'nuevos');
});

test('el sondeo global nunca devuelve los mensajes propios', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto);

    $cursor = $this->actingAs($beto)->getJson(route('mensajes.estado'))->json()['cursor'];

    // Beto escribe: es su propio mensaje, no debe avisarse a sí mismo.
    Mensaje::create([
        'conversacion_id' => $conversacion->id,
        'usuario_id' => $beto->id,
        'cuerpo' => 'Nota de Beto',
    ]);

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado', ['desde' => $cursor]))
        ->assertOk()
        ->assertJsonCount(0, 'nuevos')
        ->assertJsonPath('sin_responder', 0);
});

test('el sondeo global no expone conversaciones ajenas', function () {
    [$ana, $beto] = usuarioParticipante();
    [$carlos, $diana] = usuarioParticipante();

    iniciarConversacion($carlos, $diana, 'Conversación privada');

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado'))
        ->assertOk()
        ->assertJsonPath('sin_responder', 0);

    $this->actingAs($beto)
        ->getJson(route('mensajes.estado', ['bandeja' => 1]))
        ->assertOk()
        ->assertJsonCount(0, 'conversaciones');
});

test('el sondeo global exige sesión iniciada', function () {
    $this->getJson(route('mensajes.estado'))->assertUnauthorized();
});

test('el notificador se monta en el resto de pantallas y no en el chat', function () {
    [$ana, $beto] = usuarioParticipante();

    $conversacion = iniciarConversacion($ana, $beto, 'Hola');

    $this->actingAs($beto)
        ->get(route('articulos.index'))
        ->assertOk()
        ->assertSee('notificadorMensajes', false)
        ->assertSee(route('mensajes.estado'), false);

    // En el chat el hilo ya se encarga de pintarlo: evita el pitido duplicado.
    $this->actingAs($beto)
        ->get(route('mensajes.show', $conversacion))
        ->assertOk()
        ->assertDontSee('notificadorMensajes', false);
});
