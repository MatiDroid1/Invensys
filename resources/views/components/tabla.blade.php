@props([
    'alto' => false,
])

{{--
    Envoltorio de tabla.

    Existe por una razón concreta: casi todas las vistas envolvían la tabla en
    un `overflow-x-auto` suelto y, en dos de ellas, además con `-mx-4 sm:-mx-6`
    para poder pegar la tabla a los bordes de la tarjeta. El margen negativo
    funcionaba solo si el contenedor tenía exactamente ese padding, así que la
    tabla se salía de la tarjeta en cuanto el padding no coincidía.

    Aquí no hay margen negativo: la tarjeta define el ancho y el desplazamiento
    horizontal ocurre dentro de ella, de modo que las columnas quedan dentro
    del mismo marco que el título y los filtros.

    `alto` añade un alto máximo con el encabezado fijo, para historiales largos
    (kardex, movimientos, auditoría). Sin ese alto el encabezado pegajoso no
    tiene efecto, así que no se activa por defecto.
--}}
<div {{ $attributes->merge(['class' => $alto ? 'md-table-scroll-tall' : 'md-table-scroll']) }}>
    <table class="md-table">
        {{ $slot }}
    </table>
</div>