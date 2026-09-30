{{-- Aviso emergente y bandeja de mensajes, disponibles en toda la aplicación. --}}

@if (auth()->check())
    <div
        x-data="{ abierto: false }"
        x-show="abierto"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-4 end-4 z-50 w-80 max-w-[calc(100vw-2rem)]"
        role="status"
        aria-live="polite"
    >
        <template x-if="hayNovedad">
            <div class="rounded-lg shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">

                <div class="flex items-start gap-3 p-4">
                    <span class="inline-flex items-center justify-center shrink-0 w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                        </svg>
                    </span>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            <span x-text="aviso.remitente"></span>
                            <span class="font-normal text-gray-500 dark:text-gray-400">te escribió</span>
                        </p>

                        <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-300 break-words line-clamp-3" x-text="aviso.texto"></p>
                    </div>

                    <button
                        type="button"
                        @click="abierto = false"
                        class="shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                        aria-label="Cerrar aviso"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex border-t border-gray-200 dark:border-gray-700">
                    <template x-if="aviso.url">
                        <a
                            :href="aviso.url"
                            @click="abierto = false"
                            class="flex-1 px-4 py-2.5 text-center text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
                        >
                            Abrir conversación
                        </a>
                    </template>

                    <a
                        href="{{ route('mensajes.index') }}"
                        @click="abierto = false"
                        class="flex-1 px-4 py-2.5 text-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Ver bandeja
                    </a>
                </div>
            </div>
        </template>
    </div>
@endif