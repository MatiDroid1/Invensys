/**
 * Aviso sonoro de mensajes entrantes, generado con la Web Audio API.
 *
 * No usa archivos de sonido: son dos tonos descendentes, como una
 * notificación. Se comparte entre el chat y el notificador global para que
 * el pitido suene siempre igual en todo el sistema.
 */

const tono = (contexto, frecuencia, inicio, duracion, volumen) => {
    const oscilador = contexto.createOscillator();
    const ganancia = contexto.createGain();

    oscilador.type = 'sine';
    oscilador.frequency.value = frecuencia;

    // Rampa suave para que no se oiga como un chasquido.
    ganancia.gain.setValueAtTime(0.0001, contexto.currentTime + inicio);
    ganancia.gain.exponentialRampToValueAtTime(volumen, contexto.currentTime + inicio + 0.02);
    ganancia.gain.exponentialRampToValueAtTime(0.0001, contexto.currentTime + inicio + duracion);

    oscilador.connect(ganancia);
    ganancia.connect(contexto.destination);

    oscilador.start(contexto.currentTime + inicio);
    oscilador.stop(contexto.currentTime + inicio + duracion + 0.02);
};

const crearAvisoSonoro = () => ({
    contexto: null,
    disponible: true,

    /**
     * Prepara el AudioContext sin emitir sonido.
     *
     * El navegador solo permite reproducir despues de una interaccion del
     * usuario, asi que esto se llama en el primer clic o tecla.
     */
    desbloquear() {
        try {
            const Contexto = window.AudioContext || window.webkitAudioContext;

            if (!Contexto) {
                this.disponible = false;
                return false;
            }

            this.contexto = this.contexto || new Contexto();

            if (this.contexto.state === 'suspended') {
                this.contexto.resume();
            }

            return true;
        } catch (error) {
            this.disponible = false;
            return false;
        }
    },

    /** Un pitido corto. */
    pitir() {
        try {
            if (!this.desbloquear()) {
                return false;
            }

            tono(this.contexto, 880, 0, 0.09, 0.12);
            tono(this.contexto, 660, 0.11, 0.13, 0.12);

            return true;
        } catch (error) {
            this.disponible = false;
            return false;
        }
    },

    /**
     * Un pitido por cada mensaje recibido, separados para que se distingan.
     */
    pitirVarios(cantidad) {
        for (let i = 0; i < cantidad; i++) {
            setTimeout(() => this.pitir(), i * 320);
        }
    },
});

const avisoSonoro = crearAvisoSonoro();

export default avisoSonoro;