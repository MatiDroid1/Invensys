import './bootstrap';

import Alpine from 'alpinejs';
import notificadorMensajes from './notificadorMensajes';
import avisoSonoro from './avisoSonoro';
import temaOscuro from './tema';

window.Alpine = Alpine;

// El aviso sonoro es un singleton compartido: el chat y el notificador global
// deben sonar exactamente igual, sin crear dos contextos de audio.
window.avisoSonoro = avisoSonoro;

Alpine.data('notificadorMensajes', notificadorMensajes);
Alpine.data('temaOscuro', temaOscuro);

Alpine.start();