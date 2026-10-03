@props([
    'articulos',
    'articuloPreseleccionado' => null,
    'placeholderReferencia' => null,
    'mostrarStock' => true,
])

{{--
    Campos comunes a los tres formularios de movimiento (entrada, salida y
    ajuste).

    Antes cada uno repetía los mismos cinco campos con el mismo HTML, y cualquier
    cambio de estilo había que aplicarlo tres veces. Aquí viven una sola vez; lo
    que cambia entre ellos (a quién se le entrega, si el ajuste sube o baja)
    entra por el slot `extra`.
--}}
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
            class="md-field"
            required
        >

        <x-input-error :messages="$errors->get('cantidad')" class="md-error" />
    </div>

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