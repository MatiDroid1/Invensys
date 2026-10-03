{{--
    Barra de navegación.

    Decisiones de diseño que no se ven en el marcado pero conviene no perder:

    - Va fija arriba con fondo semitransparente y desenfoque (`sticky` +
      `backdrop-blur`). Es una aplicación de inventario: se pasa mucho rato
      mirando la misma lista y la barra no debería tapar contenido ni flotar
      sobre él.

    - Su `z-40` está por debajo del `z-50` de los modales y de los avisos
      emergentes, a propósito: un modal tiene que cubrir la barra, no quedar
      debajo. Los paneles desplegables suben a `z-50`, pero dentro del contexto
      de apilado de la barra, así que tampoco se escapan por encima de un modal.

    - La entrada activa se marca con una pastilla de fondo (componente
      `x-nav-link`) y no con un filete inferior como antes: con ocho entradas la
      raya de dos píxeles se perdía.

    - La barra completa no cabe en pantallas angostas, así que por debajo de
      `xl` (1280px) en vez de mostrar una fila que se desborda, las entradas
      caen a un panel desplegable con la misma jerarquía, agrupadas por sección.
--}}

@php
    $esAdmin = Auth::user()->isAdmin();

    // Iniciales para el avatar: dos letras del nombre, como en los listados de
    // contactos. Si el nombre viniera vacío se muestra algo en vez de un
    // círculo pelado.
    $iniciales = collect(preg_split('/\s+/u', trim(Auth::user()->name)) ?: [])
        ->filter()
        ->take(2)
        ->map(fn ($palabra) => mb_strtoupper(mb_substr($palabra, 0, 1)))
        ->implode('') ?: '?';
@endphp

<nav
    x-data="{ abierto: false }"
    @keydown.escape.window="abierto = false"
    class="sticky top-0 z-40 border-b border-gray-200/80 bg-white/85 backdrop-blur dark:border-gray-700/80 dark:bg-gray-900/85"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-16 items-center gap-2">

            {{-- Marca --}}
            <a
                href="{{ route('dashboard') }}"
                class="group flex shrink-0 items-center gap-2.5 rounded-lg pe-2"
            >
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm transition-colors group-hover:bg-indigo-500 dark:bg-indigo-500 dark:group-hover:bg-indigo-400">
                    <x-application-logo class="h-5 w-5" />
                </span>

                <span class="hidden text-[15px] font-semibold tracking-tight text-gray-900 dark:text-white sm:block">
                    Invensys
                </span>
            </a>

            {{-- El filete separa la marca del menú y desaparece cuando el menú
                 también desaparece, para que la barra no se parta en dos. --}}
            <span class="hidden h-6 w-px bg-gray-200 dark:bg-gray-700 xl:block"></span>

            {{-- Entradas (escritorio) --}}
            <div class="hidden items-center gap-1 xl:flex">

                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Panel
                </x-nav-link>

                <x-nav-link :href="route('articulos.index')" :active="request()->routeIs('articulos.*')">
                    Artículos
                </x-nav-link>

                <x-nav-dropdown
                    label="Movimientos"
                    :activo="request()->routeIs('movimientos.*')"
                >
                    <x-nav-dropdown-link
                        :href="route('movimientos.index')"
                        :activo="request()->routeIs('movimientos.index')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </x-slot>

                        Historial
                    </x-nav-dropdown-link>

                    <x-nav-dropdown-link
                        :href="route('movimientos.create')"
                        :activo="request()->routeIs('movimientos.create')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        </x-slot>

                        Nueva entrada
                    </x-nav-dropdown-link>

                    <x-nav-dropdown-link
                        :href="route('movimientos.salida.create')"
                        :activo="request()->routeIs('movimientos.salida.create')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m7.5 7.5h-15" /></svg>
                        </x-slot>

                        Nueva salida
                    </x-nav-dropdown-link>

                    <x-nav-dropdown-link
                        :href="route('movimientos.ajuste.create')"
                        :activo="request()->routeIs('movimientos.ajuste.create')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                        </x-slot>

                        Ajuste de inventario
                    </x-nav-dropdown-link>
                </x-nav-dropdown>

                <x-nav-link :href="route('personas.index')" :active="request()->routeIs('personas.*')">
                    Personas
                </x-nav-link>

                <x-nav-link :href="route('kardex.index')" :active="request()->routeIs('kardex.*')">
                    Kardex
                </x-nav-link>

                {{-- El contador cuelga del propio enlace, no de un contenedor
                     aparte, para que el badge no se desalinee al cambiar el
                     ancho del texto. --}}
                <div class="relative inline-flex">
                    <x-nav-link :href="route('mensajes.index')" :active="request()->routeIs('mensajes.*')">
                        Mensajes
                    </x-nav-link>

                    <span
                        data-contador-mensajes
                        class="absolute -end-1.5 -top-1 inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-5 text-white ring-2 ring-white dark:ring-gray-900 {{ $mensajesNoLeidos === 0 ? 'hidden' : '' }}"
                    >
                        {{ $mensajesNoLeidos }}
                    </span>
                </div>

                <x-nav-dropdown
                    label="Reportes"
                    :activo="request()->routeIs('reportes.*')"
                >
                    <x-nav-dropdown-link
                        :href="route('reportes.stock')"
                        :activo="request()->routeIs('reportes.stock')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75" /></svg>
                        </x-slot>

                        Stock actual
                    </x-nav-dropdown-link>

                    <x-nav-dropdown-link
                        :href="route('reportes.movimientos')"
                        :activo="request()->routeIs('reportes.movimientos')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </x-slot>

                        Movimientos por período
                    </x-nav-dropdown-link>

                    <x-nav-dropdown-link
                        :href="route('reportes.entregas-persona')"
                        :activo="request()->routeIs('reportes.entregas-persona')"
                    >
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </x-slot>

                        Entregas por persona
                    </x-nav-dropdown-link>
                </x-nav-dropdown>

                <x-nav-link :href="route('acerca')" :active="request()->routeIs('acerca')">
                    Acerca de
                </x-nav-link>

                @if ($esAdmin)
                    <x-nav-dropdown
                        label="Administración"
                        :activo="request()->routeIs('usuarios.*', 'categorias.*', 'unidades-medida.*', 'contacto.*')"
                    >
                        <x-nav-dropdown-link :href="route('usuarios.index')" :activo="request()->routeIs('usuarios.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                            </x-slot>

                            Usuarios
                        </x-nav-dropdown-link>

                        <x-nav-dropdown-link :href="route('categorias.index')" :activo="request()->routeIs('categorias.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" /></svg>
                            </x-slot>

                            Categorías
                        </x-nav-dropdown-link>

                        <x-nav-dropdown-link :href="route('unidades-medida.index')" :activo="request()->routeIs('unidades-medida.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                            </x-slot>

                            Unidades de medida
                        </x-nav-dropdown-link>

                        <x-nav-dropdown-link :href="route('contacto.index')" :activo="request()->routeIs('contacto.*')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                            </x-slot>

                            Contacto
                        </x-nav-dropdown-link>
                    </x-nav-dropdown>
                @endif
            </div>

            {{-- Acciones de la derecha --}}
            <div class="ms-auto flex items-center gap-0.5 sm:gap-1">

                {{-- Tema claro / oscuro --}}
                <div x-data="temaOscuro">
                    <button
                        type="button"
                        @click="alternar()"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
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

                {{-- Bandeja de mensajes: funciona en cualquier pantalla --}}
                <div x-data="{ bandeja: false }" class="relative">
                    <button
                        type="button"
                        @click="bandeja = !bandeja; if (bandeja) marcarLeidas()"
                        :aria-expanded="bandeja.toString()"
                        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
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
                            class="absolute end-1 top-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-4 text-white ring-2 ring-white dark:ring-gray-900"
                        ></span>
                    </button>

                    <div
                        x-show="bandeja"
                        @click.outside="bandeja = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-cloak
                        style="display: none;"
                        class="absolute end-0 top-full mt-2 w-80 max-h-[26rem] overflow-y-auto rounded-2xl bg-white p-1.5 shadow-e3 ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10 z-50"
                    >
                        <p class="px-2.5 pb-2 pt-1.5 text-sm font-semibold text-gray-900 dark:text-white">
                            Mensajes
                        </p>

                        <div class="max-h-72 overflow-y-auto">
                            <template x-if="!conversaciones.length">
                                <p class="px-2.5 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    Todavía no tienes conversaciones.
                                </p>
                            </template>

                            <template x-for="conversacion in conversaciones" :key="conversacion.id">
                                <a
                                    :href="conversacion.url"
                                    @click="bandeja = false"
                                    class="flex items-start gap-3 rounded-lg px-2.5 py-2.5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/60"
                                >
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-300">
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
                                                class="inline-flex h-5 min-w-[1.25rem] shrink-0 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold text-white"
                                                x-text="conversacion.no_leidos"
                                            ></span>
                                        </span>

                                        <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400" x-text="conversacion.resumen"></span>
                                    </span>
                                </a>
                            </template>
                        </div>

                        <div class="mt-1.5 border-t border-gray-100 pt-1.5 dark:border-gray-700">
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2.5 py-2.5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/60">
                                <input
                                    type="checkbox"
                                    x-model="sonido"
                                    @change="alternarSonido()"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600"
                                >

                                <span class="text-sm text-gray-700 dark:text-gray-300">Sonido al recibir mensajes</span>
                            </label>

                            <template x-if="permiso === 'default'">
                                <button
                                    type="button"
                                    @click="pedirPermiso()"
                                    class="w-full rounded-lg px-2.5 py-2.5 text-start text-sm text-indigo-600 transition-colors hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-gray-700/60"
                                >
                                    Activar avisos cuando la pestaña esté en segundo plano
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Cuenta --}}
                <div
                    x-data="{ cuenta: false }"
                    @keydown.escape.window="cuenta = false"
                    class="relative"
                >
                    <button
                        type="button"
                        @click="cuenta = !cuenta"
                        :aria-expanded="cuenta.toString()"
                        aria-label="Menú de la cuenta"
                        class="flex items-center gap-2 rounded-full ps-1 pe-1.5 py-1 transition-colors hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 sm:pe-2 dark:hover:bg-gray-800"
                    >
                        {{-- El nombre se recorta en vez de empujar el resto de la
                             barra: sin esto, un nombre largo desbordaba la fila
                             en pantallas medianas. --}}
                        <span class="hidden max-w-[12rem] truncate text-sm font-medium text-gray-700 dark:text-gray-200 lg:block">
                            {{ Auth::user()->name }}
                        </span>

                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-900 text-xs font-semibold uppercase text-white dark:bg-white dark:text-gray-900">
                            {{ $iniciales }}
                        </span>

                        <svg
                            class="hidden h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200 sm:block"
                            :class="cuenta ? 'rotate-180' : ''"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"
                        >
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l4.707-4.293a1 1 0 011.414 1.414l-5.414 5a1 1 0 01-1.414 0l-5.414-5a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div
                        x-show="cuenta"
                        @click.outside="cuenta = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        x-cloak
                        style="display: none;"
                        class="absolute end-0 top-full mt-2 w-64 rounded-2xl bg-white p-1.5 shadow-e3 ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10 z-50"
                    >
                        <div class="px-3 py-2.5">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ Auth::user()->email }}
                            </p>
                        </div>

                        <div class="my-1.5 h-px bg-gray-100 dark:bg-gray-700"></div>

                        <x-nav-dropdown-link :href="route('profile.edit')">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0012 21a8.966 8.966 0 00-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </x-slot>

                            Perfil
                        </x-nav-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-nav-dropdown-link
                                :href="route('logout')"
                                tono="peligro"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                <x-slot name="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                </x-slot>

                                Cerrar sesión
                            </x-nav-dropdown-link>
                        </form>
                    </div>
                </div>

                {{-- Botón de menú para pantallas angostas --}}
                <button
                    type="button"
                    @click="abierto = !abierto"
                    :aria-expanded="abierto.toString()"
                    aria-label="Abrir el menú"
                    class="ms-1 inline-flex h-10 w-10 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 xl:hidden dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            x-show="!abierto"
                            x-cloak
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M4 7h16M4 12h16M4 17h10"
                        />

                        <path
                            x-show="abierto"
                            x-cloak
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

    {{-- Menú de pantallas angostas --}}
    <div
        x-show="abierto"
        x-cloak
        style="display: none;"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="border-t border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 xl:hidden"
    >
        <div class="max-h-[calc(100vh-4rem)] space-y-6 overflow-y-auto px-4 py-5 sm:px-6">

            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Principal
                </p>

                <div class="space-y-0.5">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Panel
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('articulos.index')" :active="request()->routeIs('articulos.*')">
                        Artículos
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('personas.index')" :active="request()->routeIs('personas.*')">
                        Personas
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('kardex.index')" :active="request()->routeIs('kardex.*')">
                        Kardex
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('mensajes.index')" :active="request()->routeIs('mensajes.*')">
                        <span class="flex w-full items-center justify-between gap-2">
                            <span>Mensajes</span>

                            <span
                                data-contador-mensajes
                                class="inline-flex min-w-[1.25rem] items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold leading-5 text-white {{ $mensajesNoLeidos === 0 ? 'hidden' : '' }}"
                            >
                                {{ $mensajesNoLeidos }}
                            </span>
                        </span>
                    </x-responsive-nav-link>
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Movimientos
                </p>

                <div class="space-y-0.5">
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
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Reportes
                </p>

                <div class="space-y-0.5">
                    <x-responsive-nav-link :href="route('reportes.stock')" :active="request()->routeIs('reportes.stock')">
                        Stock actual
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('reportes.movimientos')" :active="request()->routeIs('reportes.movimientos')">
                        Movimientos por período
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('reportes.entregas-persona')" :active="request()->routeIs('reportes.entregas-persona')">
                        Entregas por persona
                    </x-responsive-nav-link>
                </div>
            </div>

            @if ($esAdmin)
                <div>
                    <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Administración
                    </p>

                    <div class="space-y-0.5">
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
                    </div>
                </div>
            @endif

            <div>
                <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                    Acerca de
                </p>

                <div class="space-y-0.5">
                    <x-responsive-nav-link :href="route('acerca')" :active="request()->routeIs('acerca')">
                        Acerca de
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('profile.edit')">
                        Perfil
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link
                            :href="route('logout')"
                            tono="peligro"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            Cerrar sesión
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>

            <p class="px-3 pb-2 text-xs text-gray-400 dark:text-gray-500">
                {{ Auth::user()->email }}
            </p>
        </div>
    </div>
</nav>