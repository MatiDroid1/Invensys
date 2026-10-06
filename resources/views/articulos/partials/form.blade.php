@php
    $esEdicion = isset($articulo);
    $claseInput = 'md-field mt-1';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

    {{-- Código --}}
    <div>
        <x-input-label for="codigo" value="Código" />

        <x-text-input
            id="codigo"
            name="codigo"
            type="text"
            class="mt-1"
            :value="old('codigo', $esEdicion ? $articulo->codigo : '')"
            maxlength="50"
            placeholder="Ej: MED-001"
            required
            autofocus
        />

        <x-input-error :messages="$errors->get('codigo')" class="mt-2" />
    </div>

    {{-- Nombre --}}
    <div>
        <x-input-label for="nombre" value="Nombre" />

        <x-text-input
            id="nombre"
            name="nombre"
            type="text"
            class="mt-1"
            :value="old('nombre', $esEdicion ? $articulo->nombre : '')"
            maxlength="150"
            required
        />

        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>

    {{-- Categoría --}}
    <div>
        <x-input-label for="categoria_id" value="Categoría" />

        <select
            name="categoria_id"
            id="categoria_id"
            class="{{ $claseInput }}"
            required
        >
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

        <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
    </div>

    {{-- Unidad de medida --}}
    <div>
        <x-input-label for="unidad_medida_id" value="Unidad de medida" />

        <select
            name="unidad_medida_id"
            id="unidad_medida_id"
            class="{{ $claseInput }}"
            required
        >
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

        <x-input-error :messages="$errors->get('unidad_medida_id')" class="mt-2" />
    </div>

    {{-- Descripción --}}
    <div class="md:col-span-2">
        <x-input-label for="descripcion" value="Descripción" />

        <textarea
            name="descripcion"
            id="descripcion"
            rows="3"
            class="md-field mt-1"
            placeholder="Detalle opcional del artículo"
        >{{ old('descripcion', $esEdicion ? $articulo->descripcion : '') }}</textarea>

        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
    </div>

    {{-- Stock mínimo --}}
    <div>
        <x-input-label for="stock_minimo" value="Stock mínimo" />

        <x-text-input
            id="stock_minimo"
            name="stock_minimo"
            type="number"
            class="mt-1"
            :value="old('stock_minimo', $esEdicion ? $articulo->stock_minimo : 0)"
            min="0"
            step="0.01"
            required
        />

        <p class="md-hint">
            Umbral usado para generar la alerta de stock bajo.
        </p>

        <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
    </div>

    {{-- Opciones --}}
    <div class="flex flex-col justify-center gap-3">
        <label class="flex items-center gap-2">
            <input
                type="checkbox"
                name="control_individual"
                value="1"
                @checked(old('control_individual', $esEdicion ? $articulo->control_individual : false))
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
            >
            <span class="text-sm text-gray-700 dark:text-gray-300">
                Control individual
            </span>
        </label>

        @if ($esEdicion)
            <label class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="activo"
                    value="1"
                    @checked(old('activo', $articulo->activo))
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
                >
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Artículo activo
                </span>
            </label>
        @endif
    </div>

</div>
