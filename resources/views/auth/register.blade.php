<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <x-input-label for="nombre" :value="__('Name')" />
            <x-text-input id="nombre" class="input" type="text" name="nombre" :value="old('nombre')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="apellidos" :value="__('Surname')" />
            <x-text-input id="apellidos" class="input" type="text" name="apellidos" :value="old('apellidos')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('apellidos')" class="mt-1" />
        </div>

        <div class="mb-3">
            <x-input-label for="nombre_usuario" :value="__('Username')" />
            <x-text-input id="nombre_usuario" class="input" type="text" name="nombre_usuario" :value="old('nombre_usuario')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('nombre_usuario')" class="mt-1" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="input" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="contraseña" :value="__('Password')" />

            <x-text-input id="contraseña" class="input"
                            type="password"
                            name="contraseña"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('contraseña')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <x-input-label for="contraseña_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="contraseña_confirmation" class="input"
                            type="password"
                            name="contraseña_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('contraseña_confirmation')" class="mt-1" />
        </div>

        <div class="d-flex flex-column align-items-start gap-3">
            <a class="text-decoration-none" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
