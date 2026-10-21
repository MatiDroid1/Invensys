<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Datos de mi empresa
        </h2>
    </x-slot>

    <div class="py-10 sm:py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                <div class="p-6 text-gray-900 dark:text-gray-100 sm:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 ring-1 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-200 dark:ring-emerald-700/60">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('organizaciones.actualizar') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="nombre" value="Nombre de la empresa / negocio *" />
                            <x-text-input id="nombre" class="mt-1 block w-full" type="text" name="nombre" :value="old('nombre', $organizacion->nombre)" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="razon_social" value="Razón social (opcional)" />
                                <x-text-input id="razon_social" class="mt-1 block w-full" type="text" name="razon_social" :value="old('razon_social', $organizacion->razon_social)" />
                                <x-input-error :messages="$errors->get('razon_social')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="rut" value="RUT / Identificador tributario (opcional)" />
                                <x-text-input id="rut" class="mt-1 block w-full" type="text" name="rut" :value="old('rut', $organizacion->rut)" />
                                <x-input-error :messages="$errors->get('rut')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="email" value="Correo electrónico de la empresa (opcional)" />
                            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $organizacion->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="telefono" value="Teléfono (opcional)" />
                                <x-text-input id="telefono" class="mt-1 block w-full" type="text" name="telefono" :value="old('telefono', $organizacion->telefono)" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="direccion" value="Dirección (opcional)" />
                                <x-text-input id="direccion" class="mt-1 block w-full" type="text" name="direccion" :value="old('direccion', $organizacion->direccion)" />
                                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                            <x-primary-button class="justify-center">Guardar cambios</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
