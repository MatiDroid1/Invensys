<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">

    {{--
        Red de seguridad: si algún día se agrega un enlace y la fila vuelve a
        quedar más ancha que la pantalla, este contenedor recorta el desborde
        en vez de dejar que la página entera se desplace de lado a lado.
    --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-visible">

        <!-- Primary Navigation Menu -->
        <div class="flex justify-between h-16 gap-3">
            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                {{--
                    La barra completa no cabe en pantallas angostas. Con ocho
                    entradas más los espacios entre ellas se necesitan cerca de
                    1150px, así que antes de ese ancho (xl = 1280px) se usa el
                    menú desplegable en vez de mostrar una fila que se desborda.

                    El espaciado se aprieta en el primer breakpoint y se
                    afloja cuando ya hay ancho de sobra.
                --}}
                <div class="hidden space-x-5 xl:space-x-7 xl:-my-px xl:ms-10 xl:flex items-center">

                    <!-- Panel -->
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                    >
                        Panel
                    </x-nav-link>

                    <!-- Artículos -->
                    <x-nav-link
                        :href="route('articulos.index')"
                        :active="request()->routeIs('articulos.*')"
                    >
                        Artículos
                    </x-nav-link>

                    <!-- Movimientos -->
                    <div
                        x-data="{ openMovimientos: {{ request()->routeIs('movimientos.*') ? 'true' : 'false' }} }"
                        class="relative"
                    >
                        <button
                            type="button"
                            @click="openMovimientos = !openMovimientos"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                            :class="openMovimientos
                                ? 'border-indigo-400 text-gray-900 dark:text-gray-100'
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        >
                            Movimientos

                            <svg
                                class="ms-1 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 11.586l4.707-4.293a1 1 0 011.414 1.414l-5.414 5a1 1 0 01-1.414 0l-5.414-5a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

<div
                            x-show="openMovimientos"
                            @click.outside="openMovimientos = false"
                            x-transition
                            x-cloak
                            class="absolute left-0 top-full mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-[1000]"
                            style="display: none;"
                        >
                            <a
                                href="{{ route('movimientos.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Historial
                            </a>

                            <a
                                href="{{ route('movimientos.create') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Nueva entrada
                            </a>

                            <a
                                href="{{ route('movimientos.salida.create') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Nueva salida
                            </a>

                            <a
                                href="{{ route('movimientos.ajuste.create') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Ajuste de inventario
                            </a>
                        </div>
                    </div>

                    <!-- Personas -->
                    <x-nav-link
                        :href="route('personas.index')"
                        :active="request()->routeIs('personas.*')"
                    >
                        Personas
                    </x-nav-link>

                    <!-- Kardex -->
                    <x-nav-link
                        :href="route('kardex.index')"
                        :active="request()->routeIs('kardex.*')"
                    >
                        Kardex
                    </x-nav-link>

                    <!-- Mensajes -->
                    <div class="relative inline-flex items-center">
                        <x-nav-link
                            :href="route('mensajes.index')"
                            :active="request()->routeIs('mensajes.*')"
                        >
                            Mensajes
                        </x-nav-link>

                        <span
                            data-contador-mensajes
                            class="absolute -top-1 -end-2 inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-red-600 text-white text-xs font-bold {{ $mensajesNoLeidos === 0 ? 'hidden' : '' }}"
                        >
                            {{ $mensajesNoLeidos }}
                        </span>
                    </div>

                    <!-- Reportes -->
                    <div
                        x-data="{ openReportes: {{ request()->routeIs('reportes.*') ? 'true' : 'false' }} }"
                        class="relative"
                    >
                        <button
                            type="button"
                            @click="openReportes = !openReportes"
                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                            :class="openReportes
                                ? 'border-indigo-400 text-gray-900 dark:text-gray-100'
                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        >
                            Reportes

                            <svg
                                class="ms-1 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 9.586l3.293-3.293a1 1 0 011.414 0l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

                        <div
                            x-show="openReportes"
                            @click.outside="openReportes = false"
                            x-transition
                            x-cloak
                            class="absolute left-0 top-full mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-[1000]"
                            style="display: none;"
                        >
                            <a
                                href="{{ route('reportes.stock') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Stock actual
                            </a>

                            <a
                                href="{{ route('reportes.movimientos') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Movimientos por período
                            </a>

                            <a
                                href="{{ route('reportes.entregas-persona') }}"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                Entregas por persona
                            </a>
                        </div>
                    </div>

                    <!-- Acerca del proyecto -->
                    <x-nav-link
                        :href="route('acerca')"
                        :active="request()->routeIs('acerca')"
                    >
                        Acerca de
                    </x-nav-link>

                    <!-- Administración -->
                    @if (Auth::user()->isAdmin())
                        <div
                            x-data="{ openAdmin: {{ request()->routeIs('usuarios.*', 'categorias.*', 'unidades-medida.*', 'contacto.*') ? 'true' : 'false' }} }"
                            class="relative"
                        >
                            <button
                                type="button"
                                @click="openAdmin = !openAdmin"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out"
                                :class="openAdmin
                                    ? 'border-indigo-400 text-gray-900 dark:text-gray-100'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            >
                                Administración

                                <svg
                                    class="ms-1 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 11.586l4.707-4.293a1 1 0 011.414 1.414l-5.414 5a1 1 0 01-1.414 0l-5.414-5a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>

<div
                            x-show="openAdmin"
                            @click.outside="openAdmin = false"
                            x-transition
                            x-cloak
                            class="absolute left-0 top-full mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-[1000]"
                            style="display: none;"
                        >
                                <a
                                    href="{{ route('usuarios.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    Usuarios
                                </a>

                                <a
                                    href="{{ route('categorias.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    Categorías
                                </a>

                                <a
                                    href="{{ route('unidades-medida.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    Unidades de medida
                                </a>

                                <a
                                    href="{{ route('contacto.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    Contacto
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Avisos de mensajes: funciona en cualquier pantalla -->
            <div class="-me-2 flex items-center gap-2 ms-3 sm:ms-0">

                {{-- Tema claro / oscuro --}}
                <div x-data="temaOscuro">
                    <button
                        type="button"
                        @click="alternar()"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150"
                        :aria-label="oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                        :title="oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    >
                        {{-- Luna: se muestra en modo claro para InvitAR a oscurecer --}}
                        <svg
                            x-show="!oscuro"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>

                        {{-- Sol: se muestra en modo oscuro para volver al claro --}}
                        <svg
                            x-show="oscuro"
                            x-cloak
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </button>
                </div>

                <div x-data="{ bandeja: false }" class="relative">
                    <button
                        type="button"
                        @click="bandeja = !bandeja; if (bandeja) marcarLeidas()"
                        class="relative inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150"
                        aria-label="Notificaciones de mensajes"
                        title="Notificaciones de mensajes"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>

                        <span
                            x-show="sinResponder > 0"
                            x-cloak
                            x-text="sinResponder"
                            class="absolute -top-0.5 -end-0.5 inline-flex items-center justify-center min-w-4 h-4 px-1 rounded-full bg-red-600 text-white text-[10px] font-bold"
                        ></span>
                    </button>

                    <div
                        x-show="bandeja"
                        @click.outside="bandeja = false"
                        x-transition
                        x-cloak
                        style="display: none;"
                        class="absolute right-0 top-full mt-2 w-80 max-h-96 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-[1000]"
                    >
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Mensajes</p>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="!conversaciones.length">
                                <p class="px-4 py-6 text-sm text-center text-gray-500 dark:text-gray-400">
                                    Todavía no tienes conversaciones.
                                </p>
                            </template>

                            <template x-for="conversacion in conversaciones" :key="conversacion.id">
                                <a
                                    :href="conversacion.url"
                                    @click="bandeja = false"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0"
                                >
                                    <span class="inline-flex items-center justify-center shrink-0 w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-semibold uppercase">
                                        <span x-text="conversacion.interlocutor.charAt(0)"></span>
                                    </span>

                                    <span class="flex-1 min-w-0">
                                        <span class="flex items-center justify-between gap-2">
                                            <span
                                                class="text-sm truncate"
                                                :class="conversacion.no_leidos > 0
                                                    ? 'font-semibold text-gray-900 dark:text-gray-100'
                                                    : 'text-gray-700 dark:text-gray-300'"
                                                x-text="conversacion.interlocutor"
                                            ></span>

                                            <span
                                                x-show="conversacion.no_leidos > 0"
                                                class="inline-flex items-center justify-center min-w-5 h-5 px-1 shrink-0 rounded-full bg-red-600 text-white text-xs font-bold"
                                                x-text="conversacion.no_leidos"
                                            ></span>
                                        </span>

                                        <span class="block mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate" x-text="conversacion.resumen"></span>
                                    </span>
                                </a>
                            </template>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700">
                            <label class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input
                                    type="checkbox"
                                    x-model="sonido"
                                    @change="alternarSonido()"
                                    class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                                >

                                <span class="text-sm text-gray-700 dark:text-gray-300">Sonido al recibir mensajes</span>
                            </label>

                            <template x-if="permiso === 'default'">
                                <button
                                    type="button"
                                    @click="pedirPermiso()"
                                    class="w-full px-4 py-3 text-left text-sm text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors border-t border-gray-100 dark:border-gray-700"
                                >
                                    Activar avisos cuando la pestaña esté en segundo plano
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden xl:flex xl:items-center">

                <x-dropdown align="right" width="48" :content-classes="'py-1 bg-white dark:bg-gray-700'">

                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                        >
                            {{--
                                El nombre se recorta en vez de empujar el resto
                                de la barra. Sin esto, un nombre largo
                                desbordaba la fila en pantallas medianas.
                            --}}
                            <div class="max-w-[9rem] truncate xl:max-w-[12rem]">
                                {{ Auth::user()->name }}
                            </div>

                            <div class="ms-1">
                                <svg
                                    class="fill-current h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l4 4a1 1 0 01-1.414 1.414l-4-4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">

                        <x-dropdown-link :href="route('profile.edit')">
                            Perfil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>

                    </x-slot>

                </x-dropdown>

                </div>

            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center xl:hidden">

                <button
                    @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{ 'hidden': open, 'inline-flex': !open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />

                        <path
                            :class="{ 'hidden': !open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div
        :class="{ 'block': open, 'hidden': !open }"
        class="hidden"
    >

        <div class="pt-2 pb-3 space-y-1">

            <!-- Panel -->
            <x-responsive-nav-link
                :href="route('dashboard')"
                :active="request()->routeIs('dashboard')"
            >
                Panel
            </x-responsive-nav-link>

            <!-- Artículos -->
            <x-responsive-nav-link
                :href="route('articulos.index')"
                :active="request()->routeIs('articulos.*')"
            >
                Artículos
            </x-responsive-nav-link>

            <!-- Movimientos -->
            <div class="pt-2 pb-2 border-t border-gray-200 dark:border-gray-600">

                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Movimientos
                </div>

                <x-responsive-nav-link
                    :href="route('movimientos.index')"
                    :active="request()->routeIs('movimientos.index')"
                >
                    Historial
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('movimientos.create')"
                    :active="request()->routeIs('movimientos.create')"
                >
                    Nueva entrada
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('movimientos.salida.create')"
                    :active="request()->routeIs('movimientos.salida.create')"
                >
                    Nueva salida
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('movimientos.ajuste.create')"
                    :active="request()->routeIs('movimientos.ajuste.create')"
                >
                    Ajuste de inventario
                </x-responsive-nav-link>

            </div>

            <!-- Personas -->
            <x-responsive-nav-link
                :href="route('personas.index')"
                :active="request()->routeIs('personas.*')"
            >
                Personas
            </x-responsive-nav-link>

            <!-- Kardex -->
            <x-responsive-nav-link
                :href="route('kardex.index')"
                :active="request()->routeIs('kardex.*')"
            >
                Kardex
            </x-responsive-nav-link>

            <!-- Mensajes -->
            <x-responsive-nav-link
                :href="route('mensajes.index')"
                :active="request()->routeIs('mensajes.*')"
            >
                <span class="flex justify-between items-center w-full">
                    <span>Mensajes</span>

                    <span
                        data-contador-mensajes
                        class="inline-flex items-center justify-center min-w-5 h-5 px-1 ml-2 rounded-full bg-red-600 text-white text-xs font-bold {{ $mensajesNoLeidos === 0 ? 'hidden' : '' }}"
                    >
                        {{ $mensajesNoLeidos }}
                    </span>
                </span>
            </x-responsive-nav-link>

            <!-- Acerca del proyecto -->
            <x-responsive-nav-link
                :href="route('acerca')"
                :active="request()->routeIs('acerca')"
            >
                Acerca de
            </x-responsive-nav-link>

            <!-- Reportes -->
            <div class="pt-2 pb-2 mt-2 border-t border-gray-200 dark:border-gray-600">

                <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Reportes
                </div>

                <x-responsive-nav-link
                    :href="route('reportes.stock')"
                    :active="request()->routeIs('reportes.stock')"
                >
                    Stock actual
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('reportes.movimientos')"
                    :active="request()->routeIs('reportes.movimientos')"
                >
                    Movimientos por período
                </x-responsive-nav-link>

                <x-responsive-nav-link
                    :href="route('reportes.entregas-persona')"
                    :active="request()->routeIs('reportes.entregas-persona')"
                >
                    Entregas por persona
                </x-responsive-nav-link>

            </div>

            <!-- Administración -->
            @if (Auth::user()->isAdmin())

                <div class="pt-2 pb-2 mt-2 border-t border-gray-200 dark:border-gray-600">

                    <div class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Administración
                    </div>

                    <x-responsive-nav-link
                        :href="route('usuarios.index')"
                        :active="request()->routeIs('usuarios.*')"
                    >
                        Usuarios
                    </x-responsive-nav-link>

                    <x-responsive-nav-link
                        :href="route('categorias.index')"
                        :active="request()->routeIs('categorias.*')"
                    >
                        Categorías
                    </x-responsive-nav-link>

                    <x-responsive-nav-link
                        :href="route('unidades-medida.index')"
                        :active="request()->routeIs('unidades-medida.*')"
                    >
                        Unidades de medida
                    </x-responsive-nav-link>

                    <x-responsive-nav-link
                        :href="route('contacto.index')"
                        :active="request()->routeIs('contacto.*')"
                    >
                        Contacto
                    </x-responsive-nav-link>

                </div>

            @endif

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">

            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    {{ Auth::user()->name }}
                </div>

                <div class="font-medium text-sm text-gray-500">
                    {{ Auth::user()->email }}
                </div>
            </div>

            <div class="mt-3 space-y-1">

                <x-responsive-nav-link :href="route('profile.edit')">
                    Perfil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>

            </div>
        </div>

    </div>
</nav>