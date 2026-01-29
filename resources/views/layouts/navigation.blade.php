<nav id="nav-section" class="navbar navbar-dark navbar-expand-lg fixed-top" style="background-color: #1F1F1F;">
    <div class="container-fluid">

        <!-- Mobile Logo and Toggler -->
        <div class="d-flex align-items-center justify-content-between w-100 d-lg-none">
            <a href="{{ url('/') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logotipo de J&A Sports" class="logoPagina img-fluid"
                    style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation"
                style="border: 1px solid #D72631;">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
        </div>


        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="row w-100 align-items-center g-0">

                <!-- Desktop Logo -->
                <div class="col-lg-1 d-none d-lg-block text-center">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="Logotipo de J&A Sports" class="logoPagina img-fluid">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                    <div class="row align-items-center justify-content-around">
                        <ul id="main-nav" class="col d-flex flex-column flex-lg-row justify-content-around list-unstyled m-0 gx-0 gap-2 gap-lg-0">
                            <li><a href="#" class="text-white text-decoration-none">Hombre</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Mujer</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Niños</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Productos</a></li>
                        </ul>

                        @auth
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="col text-white text-decoration-none">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endauth
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="col-lg-4 col-md-12 my-3 my-lg-0 px-lg-3">
                    <input type="text" placeholder="Buscar" aria-label="Buscar productos" class="form-control w-100 bg-dark text-white border-secondary">
                </div>

                <!-- Icons -->
                <div class="col-lg-2 col-md-12 d-flex justify-content-center justify-content-lg-end">
                    <ul id="icon-nav" class="d-flex list-unstyled m-0 gap-3 align-items-center">
                        @auth
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white p-0 d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ asset('img/user.png') }}" alt="Icono de usuario">
                                    <span class="ms-2 d-none d-lg-inline">{{ Auth::user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                {{ __('Log Out') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}" aria-label="Iniciar sesión">
                                    <img src="{{ asset('img/user.png') }}" alt="Icono de usuario">
                                </a>
                            </li>
                        @endauth
                        
                        <li>
                            <a href="#" aria-label="Ver carrito">
                                <img src="{{ asset('img/carrito.png') }}" alt="Icono del carrito">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

