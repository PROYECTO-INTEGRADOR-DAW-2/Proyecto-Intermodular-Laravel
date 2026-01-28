<x-app-layout>
    <x-slot name="title">
        @yield('title', config('app.name', 'Laravel'))
    </x-slot>

    <x-slot name="extra_head">
        <meta name="description" content="J&A Sports - Tu tienda online de confianza para ropa y calzado deportivo de las mejores marcas.">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
        <link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script type="module">
            import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';
            createChat({
                webhookUrl: 'http://localhost:5678/webhook/e851bdf3-87dd-4b31-b1b7-3748dcea8cbe/chat'
            });
        </script>
    </x-slot>

    <x-slot name="header">
        @yield('header')
    </x-slot>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-2">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full">
        @yield('content')
    </div>

</x-app-layout>