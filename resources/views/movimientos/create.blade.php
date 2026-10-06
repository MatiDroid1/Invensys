<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva entrada de inventario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('movimientos.store')">
        <div>
            <label for="articulo_id" class="md-label">
                Artículo
            </label>

            <select
                name="articulo_id"
                id="articulo_id"
                class="md-field mt-1"
                required
            >
                <option value="">Seleccione un artículo</option>

                @foreach ($articulos as $articulo)
                    <option
                        value="{{ $articulo->id }}"
                        @selected(old('articulo_id', $articuloPreseleccionado?->id) == $articulo->id)
                    >
                        {{ $articulo->codigo }} - {{ $articulo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="cantidad" class="md-label">
                Cantidad
            </label>

            <input
                type="number"
                name="cantidad"
                id="cantidad"
                value="{{ old('cantidad') }}"
                min="0.01"
                step="0.01"
                class="md-field mt-1"
                required
            >
        </div>

        <div>
            <label for="fecha_movimiento" class="md-label">
                Fecha y hora
            </label>

            <input
                type="datetime-local"
                name="fecha_movimiento"
                id="fecha_movimiento"
                value="{{ old('fecha_movimiento', now()->format('Y-m-d\TH:i')) }}"
                class="md-field mt-1"
                required
            >
        </div>

        <div>
            <label for="referencia" class="md-label">
                Referencia
            </label>

            <input
                type="text"
                name="referencia"
                id="referencia"
                value="{{ old('referencia') }}"
                maxlength="100"
                placeholder="Ej: Envío Casa Matriz #123"
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="observaciones" class="md-label">
                Observaciones
            </label>

            <textarea
                name="observaciones"
                id="observaciones"
                rows="4"
                class="md-field mt-1"
            >{{ old('observaciones') }}</textarea>
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Registrar entrada
            </button>

            <a href="{{ route('dashboard') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>