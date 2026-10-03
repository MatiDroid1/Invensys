<x-app-layout>
    <x-slot name="header">
        <h2 class="md-title">
            Editar persona
        </h2>
    </x-slot>

    <x-pagina-form :action="route('personas.update', $persona)" :method="'PUT'">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            <div>
                <label for="nombre" class="md-label">
                    Nombre
                </label>

                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    value="{{ old('nombre', $persona->nombre) }}"
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
                    value="{{ old('apellido', $persona->apellido) }}"
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
                    value="{{ old('identificador', $persona->identificador) }}"
                    maxlength="50"
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
                    value="{{ old('email', $persona->email) }}"
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
                    value="{{ old('area', $persona->area) }}"
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
                    value="{{ old('cargo', $persona->cargo) }}"
                    maxlength="100"
                    class="md-field mt-1"
                >
            </div>

            <div>
                <label for="activo" class="md-label">
                    Estado
                </label>

                <select
                    name="activo"
                    id="activo"
                    required
                    class="md-field mt-1"
                >
                    <option value="1" @selected(old('activo', $persona->activo) == 1)>
                        Activo
                    </option>

                    <option value="0" @selected(old('activo', $persona->activo) == 0)>
                        Inactivo
                    </option>
                </select>
            </div>
        </div>

        <x-slot:acciones>
            <button type="submit" class="md-btn md-btn-filled">
                Guardar cambios
            </button>

            <a href="{{ route('personas.index') }}" class="md-btn md-btn-text">
                Cancelar
            </a>
        </x-slot:acciones>
    </x-pagina-form>
</x-app-layout>