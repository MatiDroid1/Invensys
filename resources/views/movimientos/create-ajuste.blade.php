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

        <x-movimiento-campos
            :articulos="$articulos"
            :articulo-preseleccionado="$articuloPreseleccionado"
            placeholder-referencia="Ej: Conteo físico mensual"
        >
            <x-slot:extra>
                <div>
                    <label for="tipo" class="md-label">
                        Tipo de ajuste
                    </label>

                    <select
                        name="tipo"
                        id="tipo"
                        required
                        class="md-field"
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

                    <x-input-error :messages="$errors->get('tipo')" class="md-error" />
                </div>
            </x-slot:extra>
        </x-movimiento-campos>

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
