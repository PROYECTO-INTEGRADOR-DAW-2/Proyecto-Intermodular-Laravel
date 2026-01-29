@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active border-bottom border-primary fw-bold text-white'
            : 'nav-link text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
