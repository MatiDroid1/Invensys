<x-pagina-form
    titulo="Nueva salida de inventario"
    :ruta="route('movimientos.salida.store')"
    :ruta-listado="route('movimientos.index')"
    texto-accion="Registrar salida"
>
    <x-slot:descripcion>
        Una salida descuenta unidades del stock y queda asentada en el Kardex.
    </x-slot:descripcion>

    <x-movimiento-campos
        :articulos="$articulos"
        :articulo-preseleccionado="$articuloPreseleccionado"
        placeholder-referencia="Ej: Solicitud interna #456"
    >
        <x-slot:extra>
            <div>
                <label for="persona_id" class="md-label">Persona que recibe</label>

                <select name="persona_id" id="persona_id" class="md-field" required>
                    <option value="">Seleccione una persona</option>

                    @foreach ($personas as $persona)
                        <option value="{{ $persona->id }}" @selected(old('persona_id') == $persona->id)>
                            {{ $persona->nombre_completo }}
                            @if ($persona->area)
                                - {{ $persona->area }}
                            @endif
                        </option>
                    @endforeach
                </select>

                <x-input-error :messages="$errors->get('persona_id')" class="md-error" />
            </div>
        </x-slot:extra>
    </x-movimiento-campos>
</x-pagina-form>