{{-- Fila de menú. Material usa una fila de altura cómoda con el fondo apenas
     teñido al pasar el mouse, y nada de borde ni sombra propia: el contenedor
     del menú aporta el marco. --}}
<a {{ $attributes->merge(['class' => 'block w-full rounded-lg px-3 py-2.5 text-start text-sm text-gray-700 transition-colors duration-100 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700/60 dark:hover:text-white']) }}>{{ $slot }}</a>