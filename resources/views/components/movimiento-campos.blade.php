@props([
    'articulos',
    'articuloPreseleccionado' => null,
    'placeholderReferencia' => null,
    'mostrarStock' => true,
    'mostrarCosto' => false,
])

{{--
    Campos comunes a los tres formularios de movimiento (entrada, salida y
    ajuste).

    Antes cada uno repetía los mismos cinco campos con el mismo HTML, y cualquier
    cambio de estilo había que aplicarlo tres veces. Aquí viven una sola vez; lo
    que cambia entre ellos (a quién se le entrega, si el ajuste sube o baja)
    entra por el slot `extra`.
--}}

{{-- Clave de idempotencia del envío.

     El servidor la guarda junto al movimiento. Si la misma operación se envía
     dos veces (doble clic, botón que se queda pulsado, reintento del
     navegador, pestaña duplicada), el segundo envío reconoce la clave y
     devuelve el movimiento ya registrado en vez de crear otro.

     Tras un error de validación se conserva la misma clave con `old()`: la
     operación sigue siendo la misma, y como en ese caso no se llegó a
     guardar nada, no hay conflicto. --}}
<input
    type="hidden"
    name="idempotency_key"
    value="{{ old('idempotency_key', \Illuminate\Support\Str::uuid()->toString()) }}"
>

<div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">
    <div>
        <label for="articulo_id" class="md-label">Artículo</label>

        <select name="articulo_id" id="articulo_id" class="md-field" required>
            <option value="">Seleccione un artículo</option>

            @foreach ($articulos as $articulo)
                <option
                    value="{{ $articulo->id }}"
                    @selected(old('articulo_id', $articuloPreseleccionado?->id) == $articulo->id)
                >
                    {{ $articulo->codigo }} - {{ $articulo->nombre }}
                    @if ($mostrarStock)
                        (Stock: {{ number_format($articulo->stock_actual, 2, ',', '.') }})
                    @endif
                </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('articulo_id')" class="md-error" />
    </div>

    {{-- Campo propio del tipo de movimiento (destinatario, sentido del ajuste). --}}
    {{ $extra ?? '' }}

    <div>
        <label for="cantidad" class="md-label">Cantidad</label>

        <input
            type="number"
            name="cantidad"
            id="cantidad"
            value="{{ old('cantidad') }}"
            min="0.01"
            step="0.01"
            max="{{ \App\Support\Limites::CANTIDAD_MAX }}"
            class="md-field"
            required
        >

        <x-input-error :messages="$errors->get('cantidad')" class="md-error" />
    </div>

    @if ($mostrarCosto)
        <div>
            <label for="costo_unitario" class="md-label">
                Costo unitario
            </label>

            @can('costos.editar')
                <input
                    type="number"
                    name="costo_unitario"
                    id="costo_unitario"
                    value="{{ old('costo_unitario') }}"
                    min="0"
                    step="0.01"
                    max="{{ \App\Support\Limites::COSTO_MAX }}"
                    placeholder="Ej: 1250,50"
                    class="md-field"
                >

                <p class="md-hint">
                    Opcional. Es el costo de compra de cada unidad y alimenta el
                    promedio ponderado del artículo. Déjalo vacío si aún no valoras
                    el inventario. En ajustes negativos se ignora.
                </p>
            @else
                {{-- El campo no se envía: el backend lo rechaza igual si llega. --}}
                <p class="md-field !bg-gray-50 !text-gray-400 dark:!bg-gray-800/60 dark:!text-gray-500">
                    Sin permiso para fijar el costo
                </p>

                <p class="md-hint">
                    Registrar el movimiento está permitido; fijar su costo no. El
                    costo unitario lo carga quien administra los costos.
                </p>
            @endcan

            <x-input-error :messages="$errors->get('costo_unitario')" class="md-error" />
        </div>
    @endif

    <div>
        <label for="fecha_movimiento" class="md-label">Fecha y hora</label>

        <input
            type="datetime-local"
            name="fecha_movimiento"
            id="fecha_movimiento"
            value="{{ old('fecha_movimiento', now()->format('Y-m-d\TH:i')) }}"
            class="md-field"
            required
        >

        <x-input-error :messages="$errors->get('fecha_movimiento')" class="md-error" />
    </div>

    <div class="md:col-span-2">
        <label for="referencia" class="md-label">Referencia</label>

        <input
            type="text"
            name="referencia"
            id="referencia"
            value="{{ old('referencia') }}"
            maxlength="100"
            placeholder="{{ $placeholderReferencia ?? 'Ej: documento asociado al movimiento' }}"
            class="md-field"
        >

        <x-input-error :messages="$errors->get('referencia')" class="md-error" />
    </div>

    <div class="md:col-span-2">
        <label for="observaciones" class="md-label">Observaciones</label>

        <textarea name="observaciones" id="observaciones" rows="4" class="md-field">{{ old('observaciones') }}</textarea>

        <x-input-error :messages="$errors->get('observaciones')" class="md-error" />
    </div>
</div>