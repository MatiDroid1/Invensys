@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'md-error list-inside list-disc space-y-0.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="break-words">{{ $message }}</li>
        @endforeach
    </ul>
@endif