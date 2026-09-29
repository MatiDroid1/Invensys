<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Conversación con {{ $interlocutor->name }}
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $interlocutor->email }}
                </p>
            </div>

            <a
                href="{{ route('mensajes.index') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
            >
                Volver a mensajes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div
                x-data="{
                    mensajes: [],
                    ultimoId: {{ $conversacion->mensajes->max('id') ?? 0 }},
                    cuerpo: '',
                    enviando: false,
                    error: '',
                    intervalo: null,
                    urlListado: '{{ route('mensajes.listado', $conversacion) }}',
                    urlEnviar: '{{ route('mensajes.enviar', $conversacion) }}',

                    iniciar() {
                        this.sondear();
                        this.intervalo = setInterval(() => this.sondear(), 5000);
                        this.$nextTick(() => this.irAlFinal());
                    },

                    async sondear() {
                        try {
                            const respuesta = await window.axios.get(this.urlListado, {
                                params: { despues: this.ultimoId },
                            });

                            this.recibir(respuesta.data.mensajes, respuesta.data.sin_responder);
                        } catch (error) {
                            this.error = 'No se pudieron actualizar los mensajes. Revise su conexión.';
                        }
                    },

                    recibir(nuevos, sinResponder) {
                        if (typeof sinResponder === 'number') {
                            this.actualizarContador(sinResponder);
                        }

                        if (!nuevos.length) {
                            return;
                        }

                        this.mensajes.push(...nuevos);

                        nuevos.forEach((mensaje) => {
                            if (mensaje.id > this.ultimoId) {
                                this.ultimoId = mensaje.id;
                            }
                        });

                        this.$nextTick(() => this.irAlFinal());
                    },

                    async enviar() {
                        const texto = this.cuerpo.trim();

                        if (!texto || this.enviando) {
                            return;
                        }

                        this.enviando = true;
                        this.error = '';

                        try {
                            const respuesta = await window.axios.post(this.urlEnviar, {
                                cuerpo: texto,
                            });

                            this.cuerpo = '';
                            this.recibir([respuesta.data.mensaje], respuesta.data.sin_responder);
                        } catch (error) {
                            this.error = error.response?.data?.errors?.cuerpo?.[0]
                                ?? 'No se pudo enviar el mensaje.';
                        } finally {
                            this.enviando = false;
                        }
                    },

                    irAlFinal() {
                        if (this.$refs.hilo) {
                            this.$refs.hilo.scrollTop = this.$refs.hilo.scrollHeight;
                        }
                    },

                    actualizarContador(total) {
                        document.querySelectorAll('[data-contador-mensajes]').forEach((elemento) => {
                            elemento.textContent = total;
                            elemento.classList.toggle('hidden', total === 0);
                        });
                    },
                }"
                x-init="iniciar()"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div
                    x-ref="hilo"
                    class="h-[55vh] overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-gray-900"
                >

                    @if ($conversacion->mensajes->isEmpty())

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Aún no hay mensajes en esta conversación.
                        </p>

                    @else

                        @foreach ($conversacion->mensajes as $mensaje)
                            @if ($mensaje->fueEnviadoPor(auth()->id()))
                                <div class="flex justify-end">
                                    <div class="max-w-lg rounded-lg px-4 py-2 bg-indigo-600 text-white">
                                        <div class="whitespace-pre-line">
                                            {{ $mensaje->cuerpo }}
                                        </div>

                                        <div class="text-xs opacity-75 mt-1 text-right">
                                            {{ $mensaje->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-lg rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            {{ $mensaje->usuario->name }}
                                        </div>

                                        <div class="whitespace-pre-line">
                                            {{ $mensaje->cuerpo }}
                                        </div>

                                        <div class="text-xs text-gray-400 mt-1 text-right">
                                            {{ $mensaje->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    @endif

                    <template x-for="mensaje in mensajes" :key="mensaje.id">
                        <div
                            class="flex"
                            :class="mensaje.propio ? 'justify-end' : 'justify-start'"
                        >
                            <div
                                class="max-w-lg rounded-lg px-4 py-2"
                                :class="mensaje.propio
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm'"
                            >
                                <div
                                    class="text-xs font-semibold"
                                    :class="mensaje.propio ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400'"
                                    x-show="!mensaje.propio"
                                    x-text="mensaje.nombre"
                                ></div>

                                <div class="whitespace-pre-line" x-text="mensaje.cuerpo"></div>

                                <div
                                    class="text-xs mt-1 text-right"
                                    :class="mensaje.propio ? 'opacity-75' : 'text-gray-400'"
                                    x-text="mensaje.creado_en"
                                ></div>
                            </div>
                        </div>
                    </template>

                </div>

                <div class="p-6 border-t border-gray-200 dark:border-gray-700">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($error->all() as $mensajeError)
                                    <li>{{ $mensajeError }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div
                        x-show="error"
                        x-text="error"
                        style="display: none;"
                        class="mb-4 p-4 bg-red-100 text-red-800 rounded-md"
                    ></div>

                    <form @submit.prevent="enviar()">
                        <textarea
                            name="cuerpo"
                            x-model="cuerpo"
                            rows="3"
                            maxlength="2000"
                            placeholder="Escriba su mensaje... (Ctrl+Enter para enviar)"
                            @keydown.ctrl.enter.prevent="enviar()"
                            class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:placeholder-gray-500"
                        ></textarea>

                        <div class="flex items-center justify-between gap-3 mt-3">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="cuerpo.length">0</span>/2000
                            </span>

                            <button
                                type="submit"
                                :disabled="enviando || !cuerpo.trim()"
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span x-show="!enviando">Enviar</span>
                                <span x-show="enviando" style="display: none;">Enviando...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
