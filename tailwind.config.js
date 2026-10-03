import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // 'class' y no el valor por defecto ('media') porque el usuario elige el
    // tema desde la barra de navegación. El script del <head> se encarga de
    // poner la clase `dark` en <html> antes de pintar.
    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            // Elevación de Material: la sombra sube de `e0` (plano) a `e4`
            // (diálogo). Se nombra aparte de las sombras por defecto de
            // Tailwind porque estas últimas son difusas y no siguen una escala.
            boxShadow: {
                e0: '0 1px 2px 0 rgb(16 24 40 / 0.05)',
                e1: '0 1px 2px 0 rgb(16 24 40 / 0.06), 0 1px 3px 0 rgb(16 24 40 / 0.10)',
                e2: '0 1px 2px 0 rgb(16 24 40 / 0.06), 0 2px 6px 0 rgb(16 24 40 / 0.10)',
                e3: '0 4px 8px 0 rgb(16 24 40 / 0.08), 0 8px 24px 0 rgb(16 24 40 / 0.12)',
                e4: '0 8px 16px 0 rgb(16 24 40 / 0.10), 0 16px 40px 0 rgb(16 24 40 / 0.16)',
            },

            // Curvas de Material 3 para el movimiento de la interfaz.
            transitionTimingFunction: {
                standard: 'cubic-bezier(0.2, 0, 0, 1)',
                emphasized: 'cubic-bezier(0.05, 0.7, 0.1, 1)',
            },
        },
    },

    plugins: [forms],

    // `md-*` no depende de que hoy alguna vista use la clase: Tailwind elimina
    // de `@layer components` lo que no encuentra en el HTML, así que sin esta
    // lista una clase deja de compilarse en silencio en cuanto la última vista
    // que la usaba cambia. El sitio seguiría arrancando, pero sin estilos, que
    // es justo como se rompieron las cabeceras de las tablas.
    //
    // La lista es explícita y no un patrón: con `pattern: /^md-/` Tailwind
    // cruzaba cada clase con todas las variantes y el CSS pasaba de 67 kB a
    // 225 kB. Si añades una clase a `app.css`, añádela también aquí.
    safelist: [
        'md-appbar', 'md-badge', 'md-badge-danger', 'md-badge-success', 'md-badge-warning',
        'md-btn', 'md-btn-danger', 'md-btn-danger-outlined', 'md-btn-filled', 'md-btn-lg',
        'md-btn-md', 'md-btn-outlined', 'md-btn-sm', 'md-btn-text', 'md-btn-tonal',
        'md-card', 'md-card-plain', 'md-chip', 'md-divider', 'md-error', 'md-field',
        'md-hint', 'md-icon-btn', 'md-label', 'md-menu', 'md-menu-item', 'md-menu-title',
        'md-nav-item', 'md-nav-item-active', 'md-overline', 'md-page', 'md-page-body',
        'md-page-header', 'md-section', 'md-section-title', 'md-stat', 'md-subtitle',
        'md-table', 'md-table-scroll', 'md-table-scroll-tall', 'md-td', 'md-td-clip',
        'md-td-num', 'md-td-strong', 'md-th', 'md-th-num', 'md-th-sticky', 'md-title',
        'md-tr',
    ],
};