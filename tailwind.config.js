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

            /*
             * Elevaciones de Material.
             *
             * Antes se usaba `shadow` y `shadow-lg` de Tailwind, que en superficies
             * blancas grandes se leen como manchas grises. Estas cuatro alturas
             * están calibradas para separarse del fondo sin oscurecerlo, que es lo
             * que hace una sombra en Material: no un borde difuso, sino una
             * jerarquía de altura.
             */
            boxShadow: {
                e1: '0 1px 2px 0 rgb(16 24 40 / 0.06), 0 1px 3px 0 rgb(16 24 40 / 0.10)',
                e2: '0 2px 6px -1px rgb(16 24 40 / 0.08), 0 4px 12px -2px rgb(16 24 40 / 0.10)',
                e3: '0 8px 24px -4px rgb(16 24 40 / 0.12), 0 12px 32px -8px rgb(16 24 40 / 0.14)',
                e4: '0 20px 48px -12px rgb(16 24 40 / 0.20)',
            },

            /*
             * Curvas de animación de Material: "standard" para lo que aparece y
             * desaparece en la pantalla, "emphasized" para lo que el usuario
             * acaba de provocar (un botón que se presiona). La de Tailwind por
             * defecto es simétrica; Material acelera al entrar y frena al salir,
             * que es lo que hace que un menú se sienta ligero en vez de brusco.
             */
            transitionTimingFunction: {
                standard: 'cubic-bezier(0.2, 0, 0, 1)',
                emphasized: 'cubic-bezier(0.05, 0.7, 0.1, 1)',
                decelerate: 'cubic-bezier(0.05, 0.7, 0.1, 1)',
                accelerate: 'cubic-bezier(0.3, 0, 0.8, 0.15)',
            },

            borderRadius: {
                // Los diálogos y las hojas de Material son bastante redondeados.
                '4xl': '2rem',
            },
        },
    },

    plugins: [forms],
};