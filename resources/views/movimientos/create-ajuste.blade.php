<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Ajuste de inventario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('movimientos.ajuste.store')">
        <section class="md-card-plain p-4">
            <p class="text-sm text-amber-700 dark:text-amber-300">
                Un ajuste modifica el saldo del inventario sin borrar ni modificar movimientos anteriores.
            </p>
        </section>

        <div>
            <label for="articulo_id" class="md-label">
                Artículo
            </label>

            <select
                name="articulo_id"
                id="articulo_id"
                required
                class="md-field mt-1"
            >
                <option value="">
                    Seleccione un artículo
                </option>

                @foreach ($articulos as $articulo)
                    <option
                        value="{{ $articulo->id }}"
                        @selected(old('articulo_id', $articuloPreseleccionado?->id) == $articulo->id)
                    >
                        {{ $articulo->codigo }}
                        -
                        {{ $articulo->nombre }}
                        (Stock:
                        {{ number_format($articulo->stock_actual, 2, ',', '.') }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="tipo" class="md-label">
                Tipo de ajuste
            </label>

            <select
                name="tipo"
                id="tipo"
                required
                class="md-field mt-1"
            >
                <option value="">
                    Seleccione un tipo
                </option>

                <option
                    value="AJUSTE_POSITIVO"
                    @selected(old('tipo') === 'AJUSTE_POSITIVO')
                >
                    Ajuste positivo
                </option>

                <option
                    value="AJUSTE_NEGATIVO"
                    @selected(old('tipo') === 'AJUSTE_NEGATIVO')
                >
                    Ajuste negativo
                </option>
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
                required
                class="md-field mt-1"
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
                required
                class="md-field mt-1"
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
                placeholder="Ej: Conteo físico mensual"
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
                placeholder="Indique el motivo del ajuste"
                class="md-field mt-1"
            >{{ old('observaciones') }}</textarea>
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Registrar ajuste
            </button>

            <a href="{{ route('movimientos.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>