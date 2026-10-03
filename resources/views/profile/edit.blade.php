<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Perfil
        </h2>
    </x-slot>

    <div class="md-page md-page-body">
        {{-- Los tres formularios se alinean a la izquierda y comparten el mismo
             ancho de columna: antes cada tarjeta tenía su propio `max-w-xl`
             centrado dentro de un `max-w-7xl`, así que quedaban desfasados. --}}
        <div class="max-w-2xl space-y-5">
            <section class="md-card p-5 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="md-card p-5 sm:p-6">
                @include('profile.partials.update-password-form')
            </section>

            <section class="md-card p-5 sm:p-6">
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
</x-app-layout>