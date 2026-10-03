<x-pagina-form
    titulo="Ajuste de inventario"
    :ruta="route('movimientos.ajuste.store')"
    :ruta-listado="route('movimientos.index')"
    texto-accion="Registrar ajuste"
>
    <x-slot:descripcion>
        <x-alerta
            tipo="warning"
            mensaje="Un ajuste modifica el saldo del inventario sin borrar ni modificar movimientos anteriores."
        />
    </x-slot:descripcion>

    <x-movimiento-campos
        :articulos="$articulos"
        :articulo-preseleccionado="$articuloPreseleccionado"
        placeholder-referencia="Ej: Conteo físico mensual"
    >
        <x-slot:extra>
            <div>
                <label for="tipo" class="md-label">Tipo de ajuste</label>

                <select name="tipo" id="tipo" class="md-field" required>
                    <option value="">Seleccione un tipo</option>

                    <option value="AJUSTE_POSITIVO" @selected(old('tipo') === 'AJUSTE_POSITIVO')>
                        Ajuste positivo
                    </option>

                    <option value="AJUSTE_NEGATIVO" @selected(old('tipo') === 'AJUSTE_NEGATIVO')>
                        Ajuste negativo
                    </option>
                </select>

                <x-input-error :messages="$errors->get('tipo')" class="md-error" />
            </div>
        </x-slot:extra>
    </x-movimiento-campos>
</x-pagina-form>