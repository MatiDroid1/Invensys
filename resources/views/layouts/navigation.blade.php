@php
    $esAdmin = Auth::user()->isAdmin();
    $iniciales = collect(explode(' ', trim(Auth::user()->name)))
        ->filter()
        ->take(2)
        ->map(fn ($palabra) => mb_strtoupper(mb_substr($palabra, 0, 1)))
        ->implode('');
@endphp

<nav x-data="{ abierto: false }" class="md-appbar">
    <div class="md-page">
        <div class="flex h-16 items-center gap-2">

            {{-- Marca --}}
            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2.5 rounded-xl pe-2">
                <x-application-logo class="h-8 w-auto fill-current text-indigo-600 dark:text-indigo-400" />

                <span class="hidden text-base font-semibold tracking-tight text-gray-900 dark:text-white xl:inline">
                    Invensys
                </span>
            </a>

            {{--
                Destinos principales.

                Antes eran diez entradas en una sola fila, así que a partir de
                1280px de ancho la barra entera se escondía detrás del
                hamburguesa y por debajo de eso no había forma de llegar a nada
                sin pulsar dos veces. Aquí se muestran las cinco secciones con las
                que se trabaja a diario y el resto vive en "Más".

                El aviso de mensajes lleva contador: es el dato que obliga a
                volver a la aplicación, así que se queda fuera del menú.
            --}}
            <div class="hidden min-w-0 flex-1 items-center gap-1 lg:flex">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Panel
                </x-nav-link>

                <x-nav-link :href="route('articulos.index')" :active="request()->routeIs('articulos.*')">
                    Artículos
                </x-nav-link>

                <x-nav-dropdown :active="request()->routeIs('movimientos.*')">
                    <x-slot:trigger>Movimientos</x-slot:trigger>

                    <a href="{{ route('movimientos.index') }}" class="md-menu-item">Historial</a>
                    <a href="{{ route('movimientos.create') }}" class="md-menu-item">Nueva entrada</a>
                    <a href="{{ route('movimientos.salida.create') }}" class="md-menu-item">Nueva salida</a>
                    <a href="{{ route('movimientos.ajuste.create') }}" class="md-menu-item">Ajuste de inventario</a>
                </x-nav-dropdown>

                <x-nav-link :href="route('personas.index')" :active="request()->routeIs('personas.*')">
                    Personas
                </x-nav-link>

                <x-nav-link :href="route('kardex.index')" :active="request()->routeIs('kardex.*')">
                    Kardex
                </x-nav-link>

                <span class="relative inline-flex">
                    <x-nav-link :href="route('mensajes.index')" :active="request()->routeIs('mensajes.*')">
                        Mensajes
                    </x-nav-link>

                    <span
                        data-contador-mensajes
                        class="absolute -end-1.5 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold text-white ring-2 ring-white dark:ring-gray-800 {{ $mensajesNoLeidos === 0 ? 'hidden' : '' }}"
                    >
                        {{ $mensajesNoLeidos }}
                    </span>
                </span>

                {{-- Todo lo que no se usa a diario --}}
                <x-nav-dropdown
                    align="right"
                    :active="request()->routeIs('reportes.*', 'acerca', 'usuarios.*', 'categorias.*', 'unidades-medida.*', 'contacto.*')"
                >
                    <x-slot:trigger>Más</x-slot:trigger>

                    <p class="md-menu-title">Reportes</p>

                    <a href="{{ route('reportes.stock') }}" class="md-menu-item">Stock actual</a>
                    <a href="{{ route('reportes.movimientos') }}" class="md-menu-item">Movimientos por período</a>
                    <a href="{{ route('reportes.entregas-persona') }}" class="md-menu-item">Entregas por persona</a>

                    @if ($esAdmin)
                        <p class="md-menu-title">Administración</p>

                        <a href="{{ route('usuarios.index') }}" class="md-menu-item">Usuarios</a>
                        <a href="{{ route('categorias.index') }}" class="md-menu-item">Categorías</a>
                        <a href="{{ route('unidades-medida.index') }}" class="md-menu-item">Unidades de medida</a>
                        <a href="{{ route('contacto.index') }}" class="md-menu-item">Contacto</a>
                    @endif

                    <div class="md-divider my-1.5"></div>

                    <a href="{{ route('acerca') }}" class="md-menu-item">Acerca de Invensys</a>
                </x-nav-dropdown>
            </div>

            {{-- Acciones --}}
            <div class="ms-auto flex shrink-0 items-center gap-0.5 lg:ms-0">

                {{-- Tema claro / oscuro --}}
                <div x-data="temaOscuro">
                    <button
                        type="button"
                        @click="alternar()"
                        class="md-icon-btn"
                        :aria-label="oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                        :title="oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    >
                        {{-- Luna: se muestra en modo claro para InvitAR a oscurecer --}}
                        <svg
                            x-show="!oscuro"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>

                        {{-- Sol: se muestra en modo oscuro para volver al claro --}}
                        <svg
                            x-show="oscuro"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </button>
                </div>

                {{-- Bandeja de mensajes --}}
                <div x-data="{ bandeja: false }" @keydown.escape.window="bandeja = false" class="relative">
                    <button
                        type="button"
                        @click="bandeja = !bandeja; if (bandeja) marcarLeidas()"
                        class="md-icon-btn"
                        :aria-expanded="bandeja"
                        aria-label="Notificaciones de mensajes"
                        title="Notificaciones de mensajes"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>

                        <span
                            x-show="sinResponder > 0"
                            x-cloak
                            x-text="sinResponder"
                            class="absolute -end-0.5 -top-0.5 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-800"
                        ></span>
                    </button>

                    <div
                        x-show="bandeja"
                        @click.outside="bandeja = false"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="display: none;"
                        class="md-menu right-0 w-80 origin-top-right p-0"
                    >
                        <p class="border-b border-gray-100 px-4 py-3 text-sm font-semibold text-gray-900 dark:border-gray-700/70 dark:text-gray-100">
                            Mensajes
                        </p>

                        <div class="max-h-80 overflow-y-auto">
                            <template x-if="!conversaciones.length">
                                <p class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Todavía no tienes conversaciones.
                                </p>
                            </template>

                            <template x-for="conversacion in conversaciones" :key="conversacion.id">
                                <a
                                    :href="conversacion.url"
                                    @click="bandeja = false"
                                    class="flex items-start gap-3 px-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/60"
                                >
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-semibold uppercase text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                                        <span x-text="conversacion.interlocutor.charAt(0)"></span>
                                    </span>

                                    <span class="min-w-0 flex-1">
                                        <span class="flex items-center justify-between gap-2">
                                            <span
                                                class="truncate text-sm"
                                                :class="conversacion.no_leidos > 0
                                                    ? 'font-semibold text-gray-900 dark:text-white'
                                                    : 'text-gray-700 dark:text-gray-300'"
                                                x-text="conversacion.interlocutor"
                                            ></span>

                                            <span
                                                x-show="conversacion.no_leidos > 0"
                                                class="inline-flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-indigo-600 px-1 text-xs font-bold text-white"
                                                x-text="conversacion.no_leidos"
                                            ></span>
                                        </span>

                                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400" x-text="conversacion.resumen"></span>
                                    </span>
                                </a>
                            </template>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700/70">
                            <label class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                <input
                                    type="checkbox"
                                    x-model="sonido"
                                    @change="alternarSonido()"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900"
                                >

                                <span class="text-sm text-gray-700 dark:text-gray-300">Sonido al recibir mensajes</span>
                            </label>

                            <template x-if="permiso === 'default'">
                                <button
                                    type="button"
                                    @click="pedirPermiso()"
                                    class="w-full border-t border-gray-100 px-4 py-3 text-left text-sm font-medium text-indigo-600 transition-colors hover:bg-indigo-50 dark:border-gray-700/70 dark:text-indigo-300 dark:hover:bg-indigo-500/10"
                                >
                                    Activar avisos cuando la pestaña esté en segundo plano
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{--
                    Menú de cuenta.

                    Antes mostraba el nombre completo más un chevron. Un nombre
                    largo empujaba la fila y era el motivo de que la barra se
                    descuadrara a partir de unos 1000px; ahora es un círculo con
                    las iniciales, que siempre mide lo mismo.
                --}}
                <div x-data="{ cuenta: false }" @keydown.escape.window="cuenta = false" class="relative">
                    <button
                        type="button"
                        @click="cuenta = !cuenta"
                        class="ms-1 inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        :aria-expanded="cuenta"
                        aria-label="Menú de cuenta"
                    >
                        {{ $iniciales }}
                    </button>

                    <div
                        x-show="cuenta"
                        @click.outside="cuenta = false"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="display: none;"
                        class="md-menu right-0 w-60 origin-top-right"
                    >
                        <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-700/70">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ Auth::user()->email }}
                            </p>
                        </div>

                        <div class="pt-1.5">
                            <a href="{{ route('profile.edit') }}" class="md-menu-item">Perfil</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="md-menu-item text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hamburguesa: sólo por debajo de `lg` --}}
            <div class="ms-1 flex items-center lg:hidden">
                <button
                    type="button"
                    @click="abierto = !abierto"
                    class="md-icon-btn"
                    :aria-expanded="abierto"
                    aria-label="Abrir el menú"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            :class="abierto ? 'hidden' : 'inline-flex'"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M4 7h16M4 12h16M4 17h10"
                        />

                        <path
                            :class="abierto ? 'inline-flex' : 'hidden'"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{--
        Menú de pantallas angostas.

        Va en un bloque propio y a todo el ancho, pegado al borde inferior de la
        barra, para que se lea como prolongación de la misma superficie y no como
        una lista flotante.
    --}}
    <div
        x-show="abierto"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        style="display: none;"
        class="border-t border-gray-100 bg-white lg:hidden dark:border-gray-700/70 dark:bg-gray-800"
    >
        <div class="md-page max-h-[calc(100vh-4rem)] space-y-1 overflow-y-auto py-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Panel
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('articulos.index')" :active="request()->routeIs('articulos.*')">
                Artículos
            </x-responsive-nav-link>

            <p class="md-menu-title pt-3">Movimientos</p>

            <x-responsive-nav-link :href="route('movimientos.index')" :active="request()->routeIs('movimientos.index')">
                Historial
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('movimientos.create')" :active="request()->routeIs('movimientos.create')">
                Nueva entrada
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('movimientos.salida.create')" :active="request()->routeIs('movimientos.salida.create')">
                Nueva salida
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('movimientos.ajuste.create')" :active="request()->routeIs('movimientos.ajuste.create')">
                Ajuste de inventario
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('personas.index')" :active="request()->routeIs('personas.*')">
                Personas
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('kardex.index')" :active="request()->routeIs('kardex.*')">
                Kardex
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('mensajes.index')" :active="request()->routeIs('mensajes.*')">
                <span class="flex w-full items-center justify-between gap-3">
                    <span>Mensajes</span>

                    <span
                        data-contador-mensajes
                        class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold text-white {{ $mensajesNoLeidos === 0 ? 'hidden' : '' }}"
                    >
                        {{ $mensajesNoLeidos }}
                    </span>
                </span>
            </x-responsive-nav-link>

            <p class="md-menu-title pt-3">Reportes</p>

            <x-responsive-nav-link :href="route('reportes.stock')" :active="request()->routeIs('reportes.stock')">
                Stock actual
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('reportes.movimientos')" :active="request()->routeIs('reportes.movimientos')">
                Movimientos por período
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('reportes.entregas-persona')" :active="request()->routeIs('reportes.entregas-persona')">
                Entregas por persona
            </x-responsive-nav-link>

            @if ($esAdmin)
                <p class="md-menu-title pt-3">Administración</p>

                <x-responsive-nav-link :href="route('usuarios.index')" :active="request()->routeIs('usuarios.*')">
                    Usuarios
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                    Categorías
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('unidades-medida.index')" :active="request()->routeIs('unidades-medida.*')">
                    Unidades de medida
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('contacto.index')" :active="request()->routeIs('contacto.*')">
                    Contacto
                </x-responsive-nav-link>
            @endif

            <div class="md-divider my-3"></div>

            <x-responsive-nav-link :href="route('acerca')" :active="request()->routeIs('acerca')">
                Acerca de Invensys
            </x-responsive-nav-link>
        </div>
    </div>
</nav>