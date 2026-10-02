/**
 * Tema claro / oscuro.
 *
 * Tailwind está configurado con `darkMode: 'class'`, así que todo depende de
 * que el elemento <html> tenga o no la clase `dark`. Este componente solo se
 * ocupa de alternarla y de recordar la elección.
 *
 * El primer pintado lo resuelve un script en línea del <head> (ver
 * layouts/tema.blade.php). Si el tema se aplicara recién desde Alpine, el
 * usuario vería un destello blanco antes de que la página se pintara oscura.
 */
export default function temaOscuro() {
    return {
        oscuro: false,

        init() {
            this.oscuro = document.documentElement.classList.contains('dark');
        },

        alternar() {
            this.oscuro = !this.oscuro;

            document.documentElement.classList.toggle('dark', this.oscuro);

            try {
                window.localStorage.setItem('invensys.tema', this.oscuro ? 'oscuro' : 'claro');
            } catch (error) {
                // Modo privado puede bloquear localStorage: el tema igual
                // funciona, solo no se recuerda al recargar.
            }
        },
    };
}