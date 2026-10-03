@props(['messages'])

{{--
    Mensaje de error.

    Antes era una lista con viñetas en `text-sm`, que pesa más que el campo que
    está corrigiendo. Material lo trata como texto de apoyo del campo: chico,
    sin viñeta, pegado debajo.
--}}
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'md-error list-inside list-disc space-y-0.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="break-words">{{ $message }}</li>
        @endforeach
    </ul>
@endif