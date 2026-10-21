<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva salida de inventario
        </h2>
    </x-slot>

    <x-pagina-form :action="route('movimientos.salida.store')">
        <x-movimiento-campos
            :articulos="$articulos"
            :articulo-preseleccionado="$articuloPreseleccionado"
            placeholder-referencia="Ej: Solicitud interna #456"
        >
            <x-slot:extra>
                <div>
                    <label for="persona_id" class="md-label">
                        Persona que recibe
                    </label>

                    <select
                        name="persona_id"
                        id="persona_id"
                        class="md-field"
                        required
                    >
                        <option value="">Seleccione una persona</option>

                        @foreach ($personas as $persona)
                            <option
                                value="{{ $persona->id }}"
                                @selected(old('persona_id') == $persona->id)
                            >
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

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Registrar salida
            </button>

            <a href="{{ route('movimientos.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>
