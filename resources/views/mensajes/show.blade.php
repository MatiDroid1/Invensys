<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h2 class="md-title truncate">Conversación con {{ $interlocutor->name }}</h2>

                <p class="md-subtitle mt-0.5 truncate">{{ $interlocutor->email }}</p>
            </div>

            <a href="{{ route('mensajes.index') }}" class="md-btn md-btn-sm md-btn-outlined">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>

                Volver a mensajes
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">

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
                    audioBloqueado: true,
                    soltarDesbloqueo: null,
                    urlListado: '{{ route('mensajes.listado', $conversacion) }}',
                    urlEnviar: '{{ route('mensajes.enviar', $conversacion) }}',
                    urlSonido: '{{ route('mensajes.sonido') }}',

                    iniciar() {
                        this.sondear();
                        this.intervalo = setInterval(() => this.sondear(), 5000);

                        // El navegador exige una interacción del usuario antes de
                        // dejar sonar audio. La escucha se retira solo cuando el
                        // AudioContext queda realmente en marcha: si se
                        // retirara en el primer toque y el navegador lo
                        // rechazara, el sonido ya no volvería a activarse
                        // durante el resto de la sesión en esta pantalla.
                        this.soltarDesbloqueo = async () => {
                            if (await this.pista.desbloquear()) {
                                window.removeEventListener('pointerdown', this.soltarDesbloqueo);
                                window.removeEventListener('keydown', this.soltarDesbloqueo);
                                this.audioBloqueado = false;
                            }
                        };
                        window.addEventListener('pointerdown', this.soltarDesbloqueo);
                        window.addEventListener('keydown', this.soltarDesbloqueo);

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
                        this.pista.pitirVarios(cantidad).then((sono) => {
                            this.audioBloqueado = !sono;
                        });
                    },

                    alternarSonido() {
                        this.sonido = !this.sonido;
                        this.guardarPreferencia();
                        this.persistir();

                        // Al activar, se confirma con un pitido para que el
                        // usuario sepa que quedó funcionando.
                        if (this.sonido) {
                            this.pista.pitir().then((sono) => {
                                this.audioBloqueado = !sono;
                            });
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
                class="md-card flex h-[calc(100vh-13rem)] min-h-[24rem] flex-col overflow-hidden"
            >
                <div
                    x-ref="hilo"
                    class="flex-1 overflow-y-auto space-y-4 bg-gray-50 p-6 dark:bg-gray-900"
                >

                    @if ($conversacion->mensajes->isEmpty())

                        <x-estado-vacio
                            titulo="Conversación vacía"
                            descripcion="Aún no hay mensajes en esta conversación."
                        />

                    @else

                        @foreach ($conversacion->mensajes as $mensaje)
                            @if ($mensaje->fueEnviadoPor(auth()->id()))
                                <div class="flex justify-end">
                                    <div class="max-w-lg break-words rounded-2xl rounded-br-md bg-indigo-600 px-4 py-2 text-white">
                                        <div class="whitespace-pre-line">
                                            {{ $mensaje->cuerpo }}
                                        </div>

                                        <div class="mt-1 text-right text-xs opacity-75">
                                            {{ $mensaje->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex justify-start">
                                    <div class="max-w-lg break-words rounded-2xl rounded-bl-md bg-white px-4 py-2 text-gray-900 shadow-e1 dark:bg-gray-700 dark:text-gray-100">
                                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            {{ $mensaje->usuario->name }}
                                        </div>

                                        <div class="whitespace-pre-line">
                                            {{ $mensaje->cuerpo }}
                                        </div>

                                        <div class="mt-1 text-right text-xs text-gray-400">
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
                                class="max-w-lg break-words rounded-2xl px-4 py-2"
                                :class="mensaje.propio
                                    ? 'rounded-br-md bg-indigo-600 text-white'
                                    : 'rounded-bl-md bg-white text-gray-900 shadow-e1 dark:bg-gray-700 dark:text-gray-100'"
                            >
                                <div
                                    class="text-xs font-semibold"
                                    :class="mensaje.propio ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400'"
                                    x-show="!mensaje.propio"
                                    x-text="mensaje.nombre"
                                ></div>

                                <div class="whitespace-pre-line" x-text="mensaje.cuerpo"></div>

                                <div
                                    class="mt-1 text-right text-xs"
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
                        class="md-chip absolute bottom-full left-1/2 mb-3 -translate-x-1/2 bg-indigo-600 text-white shadow-e3"
                    ></div>

                    {{--
                        El navegador no deja reproducir audio hasta que el
                        usuario interactúa con la página. Antes el pitido fallaba
                        en silencio; ahora se explica qué hacer.
                    --}}
                    <div
                        x-show="sonido && audioBloqueado"
                        x-cloak
                        style="display: none;"
                        class="md-chip absolute bottom-full left-1/2 mb-3 -translate-x-1/2 gap-2 border border-amber-300 bg-amber-50 text-amber-900 shadow-e3 dark:border-amber-700 dark:bg-amber-500/15 dark:text-amber-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M21.485 12a9 9 0 01-2.831 6.364M4.393 4.393A9.99 9.99 0 002.25 12c0 2.72.86 5.22 2.28 7.28m0-15.06A9.99 9.99 0 0121.75 12c0 2.72-.86 5.22-2.28 7.28M12 15v.007" />
                        </svg>

                        <span>Haz clic en cualquier parte para activar el sonido</span>
                    </div>

                    <button
                        type="button"
                        x-show="!estaAlFinal()"
                        @click="irAlFinal()"
                        style="display: none;"
                        class="md-chip absolute bottom-full right-6 mb-3 border border-gray-200 bg-white text-gray-700 shadow-e3 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>

                        Ir al final
                    </button>

                </div>

                <div class="shrink-0 border-t border-gray-100 bg-white p-6 dark:border-gray-700/70 dark:bg-gray-800">

                    @if ($errors->any())
                        <div class="mb-4 rounded-2xl bg-red-50 px-4 py-3 dark:bg-red-500/10">
                            <ul class="space-y-0.5 text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $mensajeError)
                                    <li>{{ $mensajeError }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div
                        x-show="error"
                        x-text="error"
                        style="display: none;"
                        class="mb-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-300"
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
                            class="md-field resize-none"
                        ></textarea>

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-4">
                                <span class="text-xs tabular-nums text-gray-500 dark:text-gray-400">
                                    <span x-text="cuerpo.length">0</span>/2000
                                </span>

                                <button
                                    type="button"
                                    @click="alternarSonido()"
                                    class="inline-flex items-center gap-2 text-xs text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
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
                                class="md-btn md-btn-md md-btn-filled disabled:cursor-not-allowed disabled:opacity-50"
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
