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

        <x-contacto-campos />

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
