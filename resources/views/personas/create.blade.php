<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Nueva persona
        </h2>
    </x-slot>

    <x-pagina-form :action="route('personas.store')">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label for="nombre" class="md-label">
                    Nombre
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    value="{{ old('nombre') }}"
                    maxlength="100"
                    required
                    class="md-field mt-1"
                >
            </div>

            <div>
                <label for="apellido" class="md-label">
                    Apellido
                </label>

                <input
                    type="text"
                    name="apellido"
                    id="apellido"
                    value="{{ old('apellido') }}"
                    maxlength="100"
                    required
                    class="md-field mt-1"
                >
            </div>

            <div>
                <label for="identificador" class="md-label">
                    Identificador
                </label>

                <input
                    type="text"
                    name="identificador"
                    id="identificador"
                    value="{{ old('identificador') }}"
                    maxlength="50"
                    placeholder="Ej: RUT, código interno, etc."
                    class="md-field mt-1"
                >
            </div>

            <div>
                <label for="email" class="md-label">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    maxlength="150"
                    class="md-field mt-1"
                >
            </div>

            <div>
                <label for="area" class="md-label">
                    Área
                </label>

                <input
                    type="text"
                    name="area"
                    id="area"
                    value="{{ old('area') }}"
                    maxlength="100"
                    class="md-field mt-1"
                >
            </div>

            <div>
                <label for="cargo" class="md-label">
                    Cargo
                </label>

                <input
                    type="text"
                    name="cargo"
                    id="cargo"
                    value="{{ old('cargo') }}"
                    maxlength="100"
                    class="md-field mt-1"
                >
            </div>
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar persona
            </button>

            <a href="{{ route('personas.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>