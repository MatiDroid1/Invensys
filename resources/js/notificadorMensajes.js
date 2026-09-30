import avisoSonoro from './avisoSonoro';

/**
 * Notificador global de mensajes.
 *
 * Se monta en el layout, asi que esta activo en cualquier pantalla
 * (articulos, movimientos, kardex, reportes...). Sondea el servidor cada
 * 15 segundos y, si llego un mensaje de otra persona:
 *
 *   - sube el contador rojo de la barra de navegacion,
 *   - muestra un avisoemergente dentro de la pagina,
 *   - lanza una notificacion del sistema si la pestana esta en segundo plano,
 *   - y emite el pitido (si el usuario lo tiene activado).
 *
 * En el chat no se monta: alla el propio hilo ya se encarga de pintarlo y de
 * marcarlo como leido, asi se evita duplicar el pitido.
 */
export default function notificadorMensajes(config = {}) {
    return {
        urlEstado: config.urlEstado ?? '',
        urlSonido: config.urlSonido ?? '',
        sonido: Boolean(config.sonido),
        intervaloSegundos: config.intervalo ?? 15,

        cursor: null,
        sinResponder: Number(config.sinResponder ?? 0),
        conversaciones: config.conversaciones ?? [],
        mostrarBandeja: false,
        aviso: null,
        avisoVence: null,
        permiso: 'default',
        error: '',

        intervalo: null,

        get hayNovedad() {
            return this.aviso !== null;
        },

        iniciar() {
            this.leerPermiso();

            this.actualizarContador(this.sinResponder);

            // Primera pasada: sitúa el cursor sin avisar de mensajes que ya
            // existían antes de abrir la aplicación.
            this.sondear();

            this.intervalo = setInterval(() => this.sondear(), this.intervaloSegundos * 1000);

            // Al volver a la pestaña se consulta de inmediato en vez de
            // esperar al próximo intervalo.
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.sondear();
                }
            });

            // El navegador exige una interacción del usuario antes de permitir
            // audio o notificaciones del sistema.
            const despertar = () => {
                avisoSonoro.desbloquear();

                if (this.permiso === 'default') {
                    this.leerPermiso();
                }

                window.removeEventListener('pointerdown', despertar);
                window.removeEventListener('keydown', despertar);
            };

            window.addEventListener('pointerdown', despertar);
            window.addEventListener('keydown', despertar);
        },

        async sondear() {
            try {
                const respuesta = await window.axios.get(this.urlEstado, {
                    params: this.cursor === null ? {} : { desde: this.cursor },
                });

                const datos = respuesta.data;

                if (typeof datos.sin_responder === 'number') {
                    this.sinResponder = datos.sin_responder;
                    this.actualizarContador(datos.sin_responder);
                }

                if (Array.isArray(datos.conversaciones)) {
                    this.conversaciones = datos.conversaciones;
                }

                // La primera pasada solo deja situado el cursor. Aunque el
                // servidor enviara novedades, aquí se descartan: en ese
                // momento el usuario todavía no está mirando la pantalla.
                const primerSondeo = this.cursor === null;

                this.cursor = datos.cursor ?? this.cursor;

                if (!primerSondeo) {
                    this.recibir(datos.nuevos ?? []);
                }

                this.error = '';
            } catch (error) {
                // Fallos de red son normales: no se molesta al usuario.
                this.error = '';
            }
        },

        recibir(nuevos) {
            if (!Array.isArray(nuevos) || !nuevos.length) {
                return;
            }

            this.mostrarAviso(nuevos);
            this.avisoSonoro(nuevos.length);
        },

        avisoSonoro(cantidad) {
            if (this.sonido && avisoSonoro.disponible) {
                avisoSonoro.pitirVarios(cantidad);
            }
        },

        mostrarAviso(nuevos) {
            // Si ya hay un aviso en pantalla, se reemplazan los datos en vez de
            // acumular notificaciones encimadas.
            const remitente = nuevos.length === 1
                ? nuevos[0].remitente
                : `${nuevos.length} personas`;

            const texto = nuevos.length === 1
                ? nuevos[0].resumen
                : `${nuevos.length} mensajes nuevos`;

            this.aviso = {
                remitente,
                texto,
                url: nuevos.length === 1 ? nuevos[0].url : '',
            };

            clearTimeout(this.avisoVence);
            this.avisoVence = setTimeout(() => {
                this.aviso = null;
            }, 12000);

            this.notificarEscritorio(remitente, texto, this.aviso.url);
        },

        /**
         * Notificación del sistema operativo. Solo tiene sentido cuando la
         * pestaña está oculta: si el usuario está mirando la pantalla, el
         * aviso dentro de la página ya es suficiente.
         */
        notificarEscritorio(titulo, cuerpo, url) {
            if (!('Notification' in window) || this.permiso !== 'granted') {
                return;
            }

            if (!document.hidden) {
                return;
            }

            try {
                const notificacion = new Notification(`Nuevo mensaje de ${titulo}`, {
                    body: cuerpo,
                    icon: null,
                    tag: 'invensys-mensajes',
                });

                notificacion.onclick = () => {
                    window.focus();

                    if (url) {
                        window.location.href = url;
                    }

                    notificacion.close();
                };
            } catch (error) {
                // Algunos navegadores bloquean la constructor: no es crítico.
            }
        },

        leerPermiso() {
            if ('Notification' in window) {
                this.permiso = Notification.permission;
            } else {
                this.permiso = 'no-soportado';
            }
        },

        /** Se llama desde el botón para pedir permiso de forma explícita. */
        async pedirPermiso() {
            if (!('Notification' in window)) {
                return;
            }

            try {
                this.permiso = await Notification.requestPermission();
            } catch (error) {
                this.permiso = 'default';
            }
        },

        alternarSonido() {
            this.sonido = !this.sonido;

            try {
                window.localStorage.setItem('invensys.sonidoMensajes', this.sonido ? '1' : '0');
            } catch (error) {
                // Modo privado puede bloquear localStorage: no es crítico.
            }

            if (this.sonido) {
                avisoSonoro.pitir();
            }

            window.axios.post(this.urlSonido, { sonido: this.sonido }).catch(() => {});
        },

        actualizarContador(total) {
            this.sinResponder = total;

            document.querySelectorAll('[data-contador-mensajes]').forEach((elemento) => {
                elemento.textContent = total;
                elemento.classList.toggle('hidden', total === 0);
            });
        },

        async marcarLeidas() {
            // Al abrir la bandeja dejamos de molestar: el siguiente sondeo ya
            // contara esos mensajes como leídos por el chat.
            this.aviso = null;
            clearTimeout(this.avisoVence);

            await this.sondear();
        },
    };
}