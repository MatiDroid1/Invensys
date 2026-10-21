@props(['monto' => null, 'decimales' => 2])

{{-- Formato monetario en pesos chilenos: 1.250.000 $ — o "sin valorizar" si no hay costo. --}}
@if ($monto === null)
    <span class="text-gray-400 dark:text-gray-500" title="Sin costo registrado">—</span>
@else
    <span>{{ number_format((float) $monto, $decimales, ',', '.') }} $</span>
@endif
