{{--
    Aplica el tema claro u oscuro antes de que se pinte la página.

   Va en línea y sin `defer` a propósito: si fuera un archivo externo, el
    navegador dibujaría primero el contenido en claro y recién después
    oscurecería todo, un destello muy visible.

    Tailwind usa `darkMode: 'class'`, de modo que basta con poner o quitar la
    clase `dark` en <html>.
--}}
<script>
    (function () {
        try {
            var guardado = window.localStorage.getItem('invensys.tema');

            // Sin elección previa se respeta la preferencia del sistema.
            var oscuro = guardado !== null
                ? guardado === 'oscuro'
                : window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.classList.toggle('dark', oscuro);
        } catch (error) {
            // localStorage bloqueado: se queda en claro, que es el tema por
            // defecto de la aplicación.
        }
    })();
</script>