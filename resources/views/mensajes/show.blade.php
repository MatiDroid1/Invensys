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

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div
                x-data="{
                    mensajes: [],
                    ultimoId: {{ $conversacion->mensajes->max('id') ?? 0 }},
                    cuerpo: '',
                    enviando: false,
                    error: '',
                    intervalo: null,
                    timerAviso: null,
                    aviso: '',
                    sonido: {{ $sonidoActivado ? 'true' : 'false' }},
                    urlListado: '{{ route('mensajes.listado', $conversacion) }}',
                    urlEnviar: '{{ route('mensajes.enviar', $conversacion) }}',
                    urlSonido: '{{ route('mensajes.sonido') }}',

                    iniciar() {
                        this.sondear();
                        this.intervalo = setInterval(() => this.sondear(), 5000);

                        // El navegador exige una interacción del usuario antes de
                        // dejar sonar audio. Aprovechamos el primer clic o tecla,
                        // pero sin emitir sonido: solo se reanuda el contexto.
                        const desbloquear = () => {
                            this.pista.desbloquear();
                            window.removeEventListener('pointerdown', desbloquear);
                            window.removeEventListener('keydown', desbloquear);
                        };
                        window.addEventListener('pointerdown', desbloquear);
                        window.addEventListener('keydown', desbloquear);

                        this.$nextTick(() => {
                            this.irAlFinal();
                            this.ajustarAltura();
                        });
                    },

                    async sondear() {
                        if (document.hidden) {
                            return;
                        }

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

                        // Solo suena cuando el mensaje es de otra persona: el
                        // propio eco no debe pitear.
                        const ajenos = nuevos.filter((mensaje) => !mensaje.propio);

                        // Si el usuario está leyendo historial no lo movemos de
                        // golpe al final.
                        const estabaAlFinal = this.estaAlFinal();

                        this.mensajes.push(...nuevos);

                        nuevos.forEach((mensaje) => {
                            if (mensaje.id > this.ultimoId) {
                                this.ultimoId = mensaje.id;
                            }
                        });

                        this.$nextTick(() => {
                            this.ajustarAltura();

                            if (estabaAlFinal) {
                                this.irAlFinal();
                            }
                        });

                        if (ajenos.length) {
                            this.pitido(ajenos.length);
                        }
                    },

                    /**.True si el hilo ya estaba al final, con margen de tolerancia. */
                    estaAlFinal() {
                        const hilo = this.$refs.hilo;

                        if (!hilo) {
                            return true;
                        }

                        return hilo.scrollHeight - hilo.scrollTop - hilo.clientHeight < 80;
                    },

                    /**
                     * Pitido corto generado con la Web Audio API, sin archivos
                     * de sonido: dos tonos descendentes, como una notificación.
                     */
                    pista: window.avisoSonoro,

                    pitido(cantidad) {
                        if (!this.sonido) {
                            return;
                        }

                        if (cantidad > 1) {
                            this.aviso = 'Recibió ' + cantidad + ' mensajes nuevos.';

                            clearTimeout(this.timerAviso);
                            this.timerAviso = setTimeout(() => {
                                this.aviso = '';
                            }, 4000);
                        }

                        // Un pitido por mensaje recibido, como pide el cliente.
                        this.pista.pitirVarios(cantidad);
                    },

                    alternarSonido() {
                        this.sonido = !this.sonido;
                        this.guardarPreferencia();
                        this.persistir();

                        // Al activar, se confirma con un pitido para que el
                        // usuario sepa que quedó funcionando.
                        if (this.sonido) {
                            this.pista.pitir();
                        }
                    },

                    guardarPreferencia() {
                        try {
                            window.localStorage.setItem(
                                'invensys.sonidoMensajes',
                                this.sonido ? '1' : '0'
                            );
                        } catch (error) {
                            // Modo privado puede bloquear localStorage: no es crítico.
                        }
                    },

                    leerPreferencia() {
                        try {
                            const guardado = window.localStorage.getItem('invensys.sonidoMensajes');

                            // La preferencia guardada en el servidor manda; el
                            // localStorage solo aplica si el usuario la cambió
                            // durante esta sesión.
                            if (guardado !== null) {
                                this.sonido = guardado === '1';
                            }
                        } catch (error) {
                            // Sin persistencia disponible.
                        }

                        this.persistir();
                    },

                    persistir() {
                        window.axios
                            .post(this.urlSonido, { sonido: this.sonido })
                            .catch(() => {
                                // Si falla, el pitido sigue funcionando en esta sesión.
                            });
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
                            this.$nextTick(() => this.ajustarAltura());
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

                    /** Crece con el contenido, hasta un tope razonable. */
                    ajustarAltura() {
                        const area = this.$refs.cuerpo;

                        if (!area) {
                            return;
                        }

                        area.style.height = 'auto';
                        area.style.height = Math.min(area.scrollHeight, 160) + 'px';
                    },

                    actualizarContador(total) {
                        document.querySelectorAll('[data-contador-mensajes]').forEach((elemento) => {
                            elemento.textContent = total;
                            elemento.classList.toggle('hidden', total === 0);
                        });
                    },
                }"
                x-init="leerPreferencia(); iniciar()"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-[calc(100vh-13rem)] min-h-[24rem]"
            >
                <div
                    x-ref="hilo"
                    class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 dark:bg-gray-900"
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

                <div class="relative shrink-0">

                    <div
                        x-show="aviso"
                        x-text="aviso"
                        style="display: none;"
                        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 px-4 py-2 bg-indigo-600 text-white text-xs rounded-full shadow-lg"
                    ></div>

                    <button
                        type="button"
                        x-show="!estaAlFinal()"
                        @click="irAlFinal()"
                        style="display: none;"
                        class="absolute bottom-full right-6 mb-3 inline-flex items-center gap-1 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-xs rounded-full shadow-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>

                        Ir al final
                    </button>

                </div>

                <div class="shrink-0 p-6 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">

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
                            x-ref="cuerpo"
                            name="cuerpo"
                            x-model="cuerpo"
                            rows="1"
                            maxlength="2000"
                            placeholder="Escriba su mensaje... (Enter para enviar, Shift+Enter para nueva línea)"
                            @input="ajustarAltura()"
                            @keydown.enter.prevent="if (!$event.shiftKey) enviar()"
                            class="block w-full resize-none rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100 dark:placeholder-gray-500"
                        ></textarea>

                        <div class="flex items-center justify-between gap-3 mt-3">
                            <div class="flex items-center gap-4">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="cuerpo.length">0</span>/2000
                                </span>

                                <button
                                    type="button"
                                    @click="alternarSonido()"
                                    class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                                    :title="sonido ? 'Desactivar notificación sonora' : 'Activar notificación sonora'"
                                >
                                    <svg x-show="sonido" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9v6m6-6v6m-8.5 4.5h11a2 2 0 002-2v-12a2 2 0 00-2-2h-11a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <svg x-show="!sonido" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 9.75L19.5 12m0 0l2.25 2.25M19.5 12l2.25-2.25M19.5 12l-2.25 2.25M3 9l9-6 9 6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    </svg>

                                    <span x-text="sonido ? 'Sonido activo' : 'Sonido silenciado'"></span>
                                </button>
                            </div>

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
