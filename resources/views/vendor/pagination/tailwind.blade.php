@if ($paginator->hasPages())
    {{-- Paginación de Material: botones cápsula, la página actualfilled y en
         español. La versión de Laravel traía esquinas rectas, anillo azul y el
         texto "Showing ... results" en inglés, que rompía el resto de la
         pantalla. --}}
    <nav role="navigation" aria-label="Paginación" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Mostrando
            <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
            {{ $paginator->total() === 1 ? 'resultado' : 'resultados' }}
        </p>

        <div class="flex flex-wrap items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="md-btn md-btn-sm h-9 cursor-not-allowed border border-gray-200 px-3 text-gray-400 opacity-60 dark:border-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>

                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="md-btn md-btn-sm md-btn-outlined h-9">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>

                    Anterior
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="px-1 text-sm text-gray-400">&hellip;</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="md-btn md-btn-sm md-btn-filled h-9 min-w-9 px-3">
                                {{ $page }}
                            </span>
                        @else
                            <a
                                href="{{ $url }}"
                                class="md-btn md-btn-sm md-btn-text h-9 min-w-9 px-3"
                                aria-label="Ir a la página {{ $page }}"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="md-btn md-btn-sm md-btn-outlined h-9">
                    Siguiente

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" class="md-btn md-btn-sm h-9 cursor-not-allowed border border-gray-200 px-3 text-gray-400 opacity-60 dark:border-gray-700">
                    Siguiente

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif