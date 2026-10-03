@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-between">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="md-btn md-btn-sm h-9 cursor-not-allowed border border-gray-200 px-3 text-gray-400 opacity-60 dark:border-gray-700">
                Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="md-btn md-btn-sm md-btn-outlined h-9">
                Anterior
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="md-btn md-btn-sm md-btn-outlined h-9">
                Siguiente
            </a>
        @else
            <span aria-disabled="true" class="md-btn md-btn-sm h-9 cursor-not-allowed border border-gray-200 px-3 text-gray-400 opacity-60 dark:border-gray-700">
                Siguiente
            </span>
        @endif
    </nav>
@endif