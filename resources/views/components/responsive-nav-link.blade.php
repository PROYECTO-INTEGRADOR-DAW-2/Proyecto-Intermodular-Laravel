@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link active bg-light text-primary fw-bold ps-3'
            : 'nav-link text-secondary ps-3';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
