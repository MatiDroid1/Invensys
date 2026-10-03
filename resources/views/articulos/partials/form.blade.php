@php
    $esEdicion = isset($articulo);
@endphp

{{--
    Los campos de alta y edición se comparten: lo único que cambia entre ambos
    es qué valor se precarga (o ninguno si se está creando). Por eso aquí solo
    vive el HTML y el `@include` decide qué se edita.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">

    {{-- Código --}}
    <div>
        <x-input-label for="codigo" value="Código" class="md-label" />

        <x-text-input
            id="codigo"
            name="codigo"
            type="text"
            class="md-field"
            :value="old('codigo', $esEdicion ? $articulo->codigo : '')"
            maxlength="50"
            placeholder="Ej: MED-001"
            required
            autofocus
        />

        <x-input-error :messages="$errors->get('codigo')" class="md-error" />
    </div>

    {{-- Nombre --}}
    <div>
        <x-input-label for="nombre" value="Nombre" class="md-label" />

        <x-text-input
            id="nombre"
            name="nombre"
            type="text"
            class="md-field"
            :value="old('nombre', $esEdicion ? $articulo->nombre : '')"
            maxlength="150"
            required
        />

        <x-input-error :messages="$errors->get('nombre')" class="md-error" />
    </div>

    {{-- Categoría --}}
    <div>
        <x-input-label for="categoria_id" value="Categoría" class="md-label" />

        <select name="categoria_id" id="categoria_id" class="md-field" required>
            <option value="">Seleccione una categoría</option>

            @foreach ($categorias as $categoria)
                <option
                    value="{{ $categoria->id }}"
                    @selected(old('categoria_id', $esEdicion ? $articulo->categoria_id : null) == $categoria->id)
                >
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('categoria_id')" class="md-error" />
    </div>

    {{-- Unidad de medida --}}
    <div>
        <x-input-label for="unidad_medida_id" value="Unidad de medida" class="md-label" />

        <select name="unidad_medida_id" id="unidad_medida_id" class="md-field" required>
            <option value="">Seleccione una unidad</option>

            @foreach ($unidadesMedida as $unidad)
                <option
                    value="{{ $unidad->id }}"
                    @selected(old('unidad_medida_id', $esEdicion ? $articulo->unidad_medida_id : null) == $unidad->id)
                >
                    {{ $unidad->nombre }}@if ($unidad->abreviatura) ({{ $unidad->abreviatura }})@endif
                </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('unidad_medida_id')" class="md-error" />
    </div>

    {{-- Descripción --}}
    <div class="md:col-span-2">
        <x-input-label for="descripcion" value="Descripción" class="md-label" />

        <textarea
            name="descripcion"
            id="descripcion"
            rows="3"
            class="md-field"
            placeholder="Detalle opcional del artículo"
        >{{ old('descripcion', $esEdicion ? $articulo->descripcion : '') }}</textarea>

        <x-input-error :messages="$errors->get('descripcion')" class="md-error" />
    </div>

    {{-- Stock mínimo --}}
    <div>
        <x-input-label for="stock_minimo" value="Stock mínimo" class="md-label" />

        <x-text-input
            id="stock_minimo"
            name="stock_minimo"
            type="number"
            class="md-field"
            :value="old('stock_minimo', $esEdicion ? $articulo->stock_minimo : 0)"
            min="0"
            step="0.01"
            required
        />

        <p class="md-hint">Umbral usado para generar la alerta de stock bajo.</p>

        <x-input-error :messages="$errors->get('stock_minimo')" class="md-error" />
    </div>

    {{-- Opciones --}}
    <div class="flex flex-col justify-center gap-2">
        <label class="flex cursor-pointer items-center gap-2.5 rounded-xl px-3 py-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
            <input
                type="checkbox"
                name="control_individual"
                value="1"
                @checked(old('control_individual', $esEdicion ? $articulo->control_individual : false))
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900"
            >
            <span class="text-sm text-gray-700 dark:text-gray-200">
                Control individual
            </span>
        </label>

        @if ($esEdicion)
            <label class="flex cursor-pointer items-center gap-2.5 rounded-xl px-3 py-2 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40">
                <input
                    type="checkbox"
                    name="activo"
                    value="1"
                    @checked(old('activo', $articulo->activo))
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900"
                >
                <span class="text-sm text-gray-700 dark:text-gray-200">
                    Artículo activo
                </span>
            </label>
        @endif
    </div>
</div>