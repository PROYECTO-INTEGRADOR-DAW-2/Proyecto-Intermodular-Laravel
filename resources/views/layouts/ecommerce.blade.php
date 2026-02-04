<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', $title ?? config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap & Styles -->
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
        <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">

        
        @yield('extra_head')
        {{ $extra_head ?? '' }}
    </head>
    <body class="bg-light">
        <div class="min-vh-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-bottom shadow-sm">
                    <div class="container-fluid py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            @if(View::hasSection('header'))
                <header class="bg-white border-bottom shadow-sm">
                    <div class="container-fluid py-4">
                        @yield('header')
                    </div>
                </header>
            @endif

            @if (session('success'))
                <div class="alert alert-success m-3">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Page Content -->
            <main class="w-full">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
        @include('layouts.footer')

        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        
        <!-- Chat Widget Styles -->
        <style>
            #chat-widget {
                font-family: 'Figtree', sans-serif;
                position: fixed;
                bottom: 20px;
                right: 20px;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                z-index: 9999;
            }

            /* Greeting Bubble */
            #chat-greeting {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 12px;
                margin-right: 4px;
                transition: opacity 0.3s ease;
            }
            .greeting-text {
                background-color: white;
                color: #1f2937;
                padding: 10px 16px;
                border-radius: 9999px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                font-weight: 500;
                font-size: 14px;
                cursor: pointer;
                transition: transform 0.2s;
            }
            .greeting-text:hover {
                transform: scale(1.05);
            }
            #close-greeting {
                background-color: white;
                color: #6b7280;
                border: none;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            #close-greeting:hover {
                background-color: #f3f4f6;
            }

            /* Chat Toggle Button (FAB) */
            #chat-toggle {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background-color: #E53935; /* Red */
                border: none;
                cursor: pointer;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.3s, background-color 0.3s;
            }
            #chat-toggle:hover {
                background-color: #C62828; /* Darker Red */
                transform: scale(1.1);
            }
            #chat-toggle svg {
                width: 30px;
                height: 30px;
                color: white; /* White icon */
                fill: currentColor;
            }

            /* Chat Window */
            #chat-window {
                position: fixed;
                bottom: 90px;
                right: 20px;
                width: 350px;
                max-width: 90vw;
                height: 500px;
                max-height: 80vh;
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid #e5e7eb;
                transform-origin: bottom right;
                transition: all 0.3s ease;
            }
            #chat-window.hidden {
                opacity: 0;
                transform: scale(0.9);
                pointer-events: none;
                visibility: hidden; /* Ensure it doesn't block clicks */
            }
            #chat-window.visible {
                opacity: 1;
                transform: scale(1);
                pointer-events: auto;
                visibility: visible;
            }

            /* Header */
            .chat-header {
                background-color: #111827; /* Dark */
                color: white;
                padding: 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .chat-status {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .status-dot {
                width: 10px;
                height: 10px;
                background-color: #22c55e;
                border-radius: 50%;
                border: 2px solid white;
            }

            /* Messages Area */
            #chat-messages {
                flex: 1;
                padding: 16px;
                overflow-y: auto;
                background-color: #f9fafb;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .message-row {
                display: flex;
                width: 100%;
            }
            .message-row.user-msg {
                justify-content: flex-end;
            }
            .message-row.bot-msg {
                justify-content: flex-start;
            }
            .message-bubble {
                max-width: 85%;
                padding: 10px 16px;
                font-size: 14px;
                line-height: 1.5;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .user-bubble {
                background-color: #E53935; /* Red */
                color: white; /* White text */
                border-radius: 16px 16px 0 16px;
                font-weight: 500;
            }
            .bot-bubble {
                background-color: white;
                color: #374151;
                border: 1px solid #e5e7eb;
                border-radius: 16px 16px 16px 0;
            }

            /* Footer/Input */
            .chat-footer {
                padding: 12px;
                background-color: white;
                border-top: 1px solid #e5e7eb;
            }
            #chat-form {
                display: flex;
                gap: 8px;
            }
            #chat-input {
                flex: 1;
                background-color: #f3f4f6;
                border: none;
                border-radius: 9999px;
                padding: 10px 16px;
                font-size: 14px;
                outline: none;
            }
            #chat-input:focus {
                background-color: white;
                box-shadow: 0 0 0 2px #E53935;
            }
            #chat-send {
                background-color: #E53935; /* Red */
                color: white;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background-color 0.2s;
            }
            #chat-send:hover {
                background-color: #C62828;
            }
        </style>

        <div id="chat-widget">
            
            <!-- Custom Greeting Bubble -->
            <div id="chat-greeting">
                <button id="close-greeting" title="Cerrar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                <div id="open-trough-greeting" class="greeting-text">
                    Hola. ¿Puedo ayudarte?
                </div>
            </div>

            <!-- Chat Window -->
            <div id="chat-window" class="hidden">
                <!-- Header -->
                <div class="chat-header">
                    <div class="chat-status">
                         <div class="status-dot"></div>
                        <span style="font-weight: 600; font-size: 14px;">Asistente J&A Sports</span>
                    </div>
                    <button id="chat-close" style="background: none; border: none; color: #9ca3af; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                
                <!-- Messages -->
                <div id="chat-messages">
                    <div class="message-row bot-msg">
                        <div class="message-bubble bot-bubble">
                            <p style="margin: 0;">¡Hola! 👋</p>
                            <p style="margin: 4px 0 0 0;">Estoy aquí para ayudarte con tus compras.</p>
                        </div>
                    </div>
                </div>
    
                <!-- Input -->
                <div class="chat-footer">
                    <form id="chat-form">
                        <input type="text" id="chat-input" placeholder="Escribe aquí..." autocomplete="off">
                        <button type="submit" id="chat-send">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Chat Trigger Button (Red FAB) -->
            <button id="chat-toggle">
                <!-- Chat Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.804 21.644A6.707 6.707 0 006 21.75a6.721 6.721 0 003.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 01-.814 1.686.75.75 0 00.44 1.223zM8.25 10.875a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25zM10.875 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875-1.125a1.125 1.125 0 100 2.25 1.125 1.125 0 000-2.25z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('chat-toggle');
                const closeBtn = document.getElementById('chat-close');
                const greeting = document.getElementById('chat-greeting');
                const closeGreetingBtn = document.getElementById('close-greeting');
                const openThroughGreetingBtn = document.getElementById('open-trough-greeting');
                
                const chatWindow = document.getElementById('chat-window');
                const input = document.getElementById('chat-input');
                const form = document.getElementById('chat-form');
                const messagesContainer = document.getElementById('chat-messages');

                function toggleChat() {
                    const isHidden = chatWindow.classList.contains('hidden');
                    
                    if (isHidden) {
                        // Open
                        chatWindow.classList.remove('hidden');
                        chatWindow.classList.add('visible');
                        
                        // Hide greeting
                        if(greeting) greeting.style.display = 'none';
                        
                        setTimeout(() => input.focus(), 100);
                    } else {
                        // Close
                        chatWindow.classList.remove('visible');
                        chatWindow.classList.add('hidden');
                    }
                }
                
                // Greeting logic
                if (closeGreetingBtn) {
                    closeGreetingBtn.addEventListener('click', function(e) {
                         e.stopPropagation();
                         greeting.style.display = 'none';
                    });
                }
                if (openThroughGreetingBtn) {
                    openThroughGreetingBtn.addEventListener('click', toggleChat);
                }

                toggleBtn.addEventListener('click', toggleChat);
                closeBtn.addEventListener('click', toggleChat);

                function addMessage(text, isUser) {
                    const row = document.createElement('div');
                    row.className = isUser ? 'message-row user-msg' : 'message-row bot-msg';
                    
                    const bubble = document.createElement('div');
                    bubble.className = isUser ? 'message-bubble user-bubble' : 'message-bubble bot-bubble';
                    
                    bubble.innerHTML = text.replace(/\n/g, '<br>');
                    
                    row.appendChild(bubble);
                    messagesContainer.appendChild(row);
                    messagesContainer.scrollTo({
                        top: messagesContainer.scrollHeight,
                        behavior: 'smooth'
                    });
                }

                async function sendMessage() {
                    const text = input.value.trim();
                    if (!text) return;

                    addMessage(text, true);
                    input.value = '';
                    input.disabled = true;

                    // Loading indicator
                    const loadingId = 'loading-' + Date.now();
                    const loadingRow = document.createElement('div');
                    loadingRow.className = 'message-row bot-msg';
                    loadingRow.id = loadingId;
                    loadingRow.innerHTML = `
                        <div class="message-bubble bot-bubble">
                            <span style="font-style: italic; color: #6b7280; font-size: 12px;">Escribiendo...</span>
                        </div>
                    `;
                    messagesContainer.appendChild(loadingRow);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;

                    try {
                        const response = await fetch('/chat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ message: text })
                        });
                        
                        const data = await response.json();
                        document.getElementById(loadingId).remove();
                        
                        if (data.response) {
                            addMessage(data.response, false);
                        } else if (data.respuesta) {
                             addMessage(data.respuesta, false);
                        } else {
                            addMessage('Error: No response from server.', false);
                        }
                    } catch (e) {
                         const loader = document.getElementById(loadingId);
                         if(loader) loader.remove();
                        addMessage('Error: ' + e.message, false);
                    } finally {
                        input.disabled = false;
                        input.focus();
                    }
                }

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    sendMessage();
                });
            });
        </script>
    </body>
</html>
