@if ($paginator->hasPages())
    {{--
        Paginación en Material y en el idioma de la aplicación.

        La versión que había era la de Laravel por defecto: botones cuadrados con
        borde, el número de página activo en gris y un contador en inglés
        ("Showing 1 to 10 of 250 results") en una aplicación que está toda en
        español. Además el contador no se leía en pantallas angostas porque se
        quedaba fuera con `hidden sm:flex`.
    --}}
    <nav role="navigation" aria-label="Paginación" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if ($paginator->firstItem())
                Mostrando
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $paginator->lastItem() }}</span>
                de
            @else
                Mostrando
            @endif

            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $paginator->total() }}</span>
            {{ $paginator->total() === 1 ? 'registro' : 'registros' }}
        </p>

        <div class="flex items-center gap-1.5">
            {{-- Anterior --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="md-btn md-btn-sm md-btn-outlined cursor-not-allowed opacity-50" aria-label="Página anterior">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 rtl:rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>

                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="md-btn md-btn-sm md-btn-outlined" aria-label="Página anterior">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 rtl:rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>

                    Anterior
                </a>
            @endif

            {{-- Números --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-gray-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-full bg-indigo-600 px-3 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-full px-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" aria-label="Ir a la página {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="md-btn md-btn-sm md-btn-outlined" aria-label="Página siguiente">
                    Siguiente

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 rtl:rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" class="md-btn md-btn-sm md-btn-outlined cursor-not-allowed opacity-50" aria-label="Página siguiente">
                    Siguiente

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 rtl:rotate-180">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif