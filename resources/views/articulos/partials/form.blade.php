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
            max="{{ \App\Support\Limites::STOCK_MINIMO_MAX }}"
            required
        />

        <p class="md-hint">
            Umbral usado para generar la alerta de stock bajo.
        </p>

        <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
    </div>

    {{-- Precio de venta --}}
    <div>
        <x-input-label for="precio_venta" value="Precio de venta" />

        @can('costos.editar')
            <x-text-input
                id="precio_venta"
                name="precio_venta"
                type="number"
                class="mt-1"
                :value="old('precio_venta', $esEdicion ? $articulo->precio_venta : '')"
                min="0"
                step="0.01"
                max="{{ \App\Support\Limites::PRECIO_MAX }}"
                placeholder="Opcional"
            />

            <p class="md-hint">
                Precio unitario de venta. Opcional: se usa para calcular el margen
                sobre el costo promedio.
            </p>
        @else
            {{-- El campo no se envía: el backend lo rechaza igual si llega. --}}
            <p class="md-field !bg-gray-50 !text-gray-400 dark:!bg-gray-800/60 dark:!text-gray-500">
                @if ($esEdicion && $articulo->precio_venta !== null)
                    {{ number_format((float) $articulo->precio_venta, 2, ',', '.') }}
                @else
                    Sin precio
                @endif
            </p>

            <p class="md-hint">
                Puedes mantener el artículo, pero no modificar su precio de venta.
            </p>
        @endcan

        <x-input-error :messages="$errors->get('precio_venta')" class="mt-2" />
    </div>

    {{-- Costo promedio (solo lectura, calculado por el sistema) --}}
    @if ($esEdicion)
        <div>
            <x-input-label for="costo_promedio" value="Costo promedio" />

            <x-text-input
                id="costo_promedio"
                name="costo_promedio"
                type="text"
                class="mt-1 bg-gray-50 dark:bg-gray-800/50"
                :value="$articulo->costo_promedio !== null ? number_format((float) $articulo->costo_promedio, 2, ',', '.') : ''"
                disabled
            />

            <p class="md-hint">
                Calculado automáticamente con el promedio ponderado de las
                entradas y ajustes positivos. No se puede editar: para
                corregirlo, registra una entrada con el costo correcto.
            </p>
        </div>
    @endif

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
