/**
 * Aviso sonoro de mensajes entrantes, generado con la Web Audio API.
 *
 * No usa archivos de sonido: son dos tonos descendentes, como una
 * notificación. Se comparte entre el chat y el notificador global para que
 * el pitido suene siempre igual en todo el sistema.
 *
 * Los navegadores no permiten reproducir audio sin una interacción previa del
 * usuario en la página. Por eso `desbloquear()` es asíncrono y verifica el
 * estado real del contexto: antes esta función asumía que había funcionado y
 * el pitido fallaba en silencio.
 */

const volumen = 0.3;

/** Dos tonos descendentes, con rampa suave para que no chasqueen. */
const tonos = [
    { frecuencia: 880, inicio: 0, duracion: 0.1 },
    { frecuencia: 660, inicio: 0.13, duracion: 0.16 },
];

const sonar = (contexto) => {
    const ahora = contexto.currentTime;

    tonos.forEach(({ frecuencia, inicio, duracion }) => {
        const oscilador = contexto.createOscillator();
        const ganancia = contexto.createGain();

        oscilador.type = 'sine';
        oscilador.frequency.value = frecuencia;

        ganancia.gain.setValueAtTime(0.0001, ahora + inicio);
        ganancia.gain.exponentialRampToValueAtTime(volumen, ahora + inicio + 0.02);
        ganancia.gain.exponentialRampToValueAtTime(0.0001, ahora + inicio + duracion);

        oscilador.connect(ganancia);
        ganancia.connect(contexto.destination);

        oscilador.start(ahora + inicio);
        oscilador.stop(ahora + inicio + duracion + 0.02);
    });
};

const crearAvisoSonoro = () => ({
    contexto: null,

    /** El navegador no soporta Web Audio. */
    soportado: true,

    /** Sigue bloqueado por falta de interacción del usuario. */
    bloqueado: true,

    get activo() {
        return this.contexto !== null && this.contexto.state === 'running';
    },

    /**
     * Prepara el AudioContext sin emitir sonido.
     *
     * Debe llamarse dentro de un manejador de evento originado por el usuario
     * (clic, tecla). Devuelve una promesa que resuelve a `true` solo si el
     * contexto quedó realmente en marcha.
     */
    async desbloquear() {
        if (!this.soportado) {
            return false;
        }

        const Contexto = window.AudioContext || window.webkitAudioContext;

        if (!Contexto) {
            this.soportado = false;
            this.bloqueado = true;
            return false;
        }

        try {
            if (!this.contexto) {
                this.contexto = new Contexto();
            }

            if (this.contexto.state === 'suspended') {
                await this.contexto.resume();
            }

            // Se comprueba el estado real: `resume()` devuelve una promesa que
            // puede resolver sin efecto si no hubo una interacción previa.
            this.bloqueado = this.contexto.state !== 'running';

            return !this.bloqueado;
        } catch (error) {
            this.bloqueado = true;
            return false;
        }
    },

    /**
     * Intenta el pitido. Devuelve `false` si el navegador lo bloqueó, para que
     * la interfaz pueda avisarle al usuario en vez de fallar en silencio.
     */
    async pitir() {
        if (!(await this.desbloquear())) {
            return false;
        }

        try {
            sonar(this.contexto);
            return true;
        } catch (error) {
            return false;
        }
    },

    /**
     * Un pitido por cada mensaje recibido, separados para que se distingan.
     * Como `pitir` es asíncrono, se encadenan en vez de dispararse a la vez.
     *
     * Resuelve a `true` si al menos uno de los pitidos llegó a sonar.
     */
    async pitirVarios(cantidad) {
        let sonóAlguno = false;

        for (let i = 0; i < cantidad; i++) {
            if (i > 0) {
                await new Promise((resolver) => setTimeout(resolver, 340));
            }

            // Al primer fallo se corta: si el navegador lo bloqueó, repetir
            // cinco veces solo gastaría CPU.
            if (!(await this.pitir())) {
                return sonóAlguno;
            }

            sonóAlguno = true;
        }

        return sonóAlguno;
    },
});

const avisoSonoro = crearAvisoSonoro();

export default avisoSonoro;