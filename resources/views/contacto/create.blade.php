<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Contacto
        </h2>
    </x-slot>

    <x-pagina-form :action="route('contacto.store')">
        <section class="md-card-plain p-4">
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Utiliza este formulario para enviar una consulta, sugerencia o solicitud.
            </p>
        </section>

        <div>
            <label for="nombre" class="md-label">
                Nombre
            </label>

            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre', auth()->user()->name) }}"
                maxlength="150"
                required
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="email" class="md-label">
                Email
            </label>

            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', auth()->user()->email) }}"
                maxlength="150"
                required
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="asunto" class="md-label">
                Asunto
            </label>

            <input
                type="text"
                name="asunto"
                id="asunto"
                value="{{ old('asunto') }}"
                maxlength="150"
                required
                placeholder="Ej: Consulta sobre inventario"
                class="md-field mt-1"
            >
        </div>

        <div>
            <label for="mensaje" class="md-label">
                Mensaje
            </label>

            <textarea
                name="mensaje"
                id="mensaje"
                rows="6"
                required
                placeholder="Escriba su mensaje..."
                class="md-field mt-1"
            >{{ old('mensaje') }}</textarea>
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Enviar mensaje
            </button>

            <a href="{{ route('dashboard') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>