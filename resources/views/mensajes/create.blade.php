<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="md-title">Nuevo mensaje</h2>

                <p class="md-subtitle mt-0.5">Escribe a otro usuario del sistema.</p>
            </div>

            <a href="{{ route('mensajes.index') }}" class="md-btn md-btn-sm md-btn-outlined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>

                Volver a mis mensajes
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
            <x-alerta />

            <section class="md-card p-6">
                @if ($disponibles->isNotEmpty())
                    <p class="mb-6 rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-600 dark:bg-gray-700/40 dark:text-gray-300">
                        Elige un usuario para escribirle. Si ya existe una conversación
                        entre ustedes, se continuará en el mismo hilo.
                    </p>
                @endif

                {{--
                    Sin usuarios activos no hay nada que enviar, así que en vez de
                    mostrar un formulario inutilizable se explica por qué y se
                    ofrece el camino de vuelta.
                --}}
                @if ($disponibles->isEmpty())
                    <x-estado-vacio
                        titulo="No hay destinatarios"
                        descripcion="No hay otros usuarios activos a los que escribir."
                    >
                        <x-slot:accion>
                            <a href="{{ route('mensajes.index') }}" class="md-btn md-btn-md md-btn-outlined">
                                Volver a mis mensajes
                            </a>
                        </x-slot:accion>
                    </x-estado-vacio>
                @else
                    <form method="POST" action="{{ route('mensajes.store') }}">
                        @csrf

                        <div class="space-y-5">
                            <div>
                                <label for="usuario_receptor_id" class="md-label">Destinatario</label>

                                <select
                                    name="usuario_receptor_id"
                                    id="usuario_receptor_id"
                                    required
                                    class="md-field mt-1.5"
                                >
                                    <option value="">Selecciona un usuario...</option>

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

                                <x-input-error :messages="$errors->get('usuario_receptor_id')" class="md-error" />
                            </div>

                            <div>
                                <label for="cuerpo" class="md-label">Mensaje</label>

                                <textarea
                                    name="cuerpo"
                                    id="cuerpo"
                                    rows="6"
                                    maxlength="2000"
                                    placeholder="Escribe tu mensaje. Puedes dejarlo vacío y enviarlo luego desde la conversación."
                                    class="md-field mt-1.5"
                                >{{ old('cuerpo') }}</textarea>

                                <x-input-error :messages="$errors->get('cuerpo')" class="md-error" />
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                            <button type="submit" class="md-btn md-btn-md md-btn-filled">
                                Enviar mensaje
                            </button>

                            <a href="{{ route('mensajes.index') }}" class="md-btn md-btn-md md-btn-outlined">
                                Cancelar
                            </a>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>