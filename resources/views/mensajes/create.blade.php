<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nuevo mensaje
        </h2>
    </x-slot>

    <x-pagina-form :action="route('mensajes.store')">
        <section class="md-card-plain p-4">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Elige un usuario para escribirle. Si ya existe una conversación entre ustedes, se continuará en el mismo hilo.
            </p>
        </section>

        @if ($disponibles->isEmpty())

            <p class="text-sm text-gray-600 dark:text-gray-300">
                No hay otros usuarios activos a los que escribir.
            </p>

            <a href="{{ route('mensajes.index') }}" class="md-btn md-btn-text">
                Volver a mis mensajes
            </a>

        @else

            <div>
                <label for="usuario_receptor_id" class="md-label">
                    Destinatario
                </label>

                <select
                    name="usuario_receptor_id"
                    id="usuario_receptor_id"
                    required
                    class="md-field mt-1"
                >
                    <option value="">Seleccione un usuario...</option>

                    @foreach ($disponibles as $usuario)
                        <option
                            value="{{ $usuario->id }}"
                            @selected((int) old('usuario_receptor_id', $destinatario?->id) === $usuario->id)
                        >
                            {{ $usuario->name }}
                            @if ($usuario->isAdmin())
                                (admin)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="cuerpo" class="md-label">
                    Mensaje
                </label>

                <textarea
                    name="cuerpo"
                    id="cuerpo"
                    rows="6"
                    maxlength="2000"
                    placeholder="Escriba su mensaje. Puede dejarlo vacío y enviarlo luego desde la conversación."
                    class="md-field mt-1"
                >{{ old('cuerpo') }}</textarea>
            </div>

        @endif

        {{-- El slot se declara siempre: el esqueleto lo imprime aunque quede vacío. --}}
        <x-slot:acciones>
            @if (! $disponibles->isEmpty())
                <button type="submit" class="md-btn md-btn-filled">
                    Enviar mensaje
                </button>

                <a href="{{ route('mensajes.index') }}" class="md-btn md-btn-text">
                    Cancelar
                </a>
            @endif
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>