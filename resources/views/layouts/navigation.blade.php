<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">

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
                            class="absolute left-0 top-full mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50"
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
                            class="absolute left-0 top-full mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50"
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
                                class="absolute left-0 top-full mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50"
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

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                        >
                            <div>{{ Auth::user()->name }}</div>

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

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">

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
        class="hidden sm:hidden"
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