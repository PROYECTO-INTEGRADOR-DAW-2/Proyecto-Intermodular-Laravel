@extends('layouts.ecommerce')

@section('title', 'Home')

@section('content')
    <main>
        <header id="header-home" class="d-flex align-items-center justify-content-center text-center" style="background-image: url('{{ asset('img/fondopagina.jpg') }}');">
            <div class="container-fluid">
                <h1>J&A Sports</h1>
                <p>Compra a montones y viste a tu propio estilo</p>
            </div>
        </header>

        <section id="novedades" class="container-fluid px-0 px-sm-3">
            <h2 class="text-center mb-5">Novedades</h2>
            <div id="brands" class="row justify-content-md-around g-0 g-sm-4">
                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Nike']]) }}" aria-label="Ver novedades de Nike">
                        <img src="{{ asset('img/logoNike.jpg') }}" alt="Logotipo de Nike" class="fotoMasNovedades">
                        <img src="{{ asset('img/simboloNike.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Adidas']]) }}" aria-label="Ver novedades de Adidas">
                        <img src="{{ asset('img/logoAdidass.jpg') }}" alt="Logotipo de Adidas" class="fotoMasNovedades">
                        <img src="{{ asset('img/AdidasSimbolo.png') }}" alt="" class="fotoSimboloAdidas d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Puma']]) }}" aria-label="Ver novedades de Puma">
                        <img src="{{ asset('img/logospuma.jpg') }}" alt="Logotipo de Puma" class="fotoMasNovedades">
                        <img src="{{ asset('img/SimboloPuma.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Asics']]) }}" aria-label="Ver novedades de Asics">
                        <img src="{{ asset('img/asicslogo.jpg') }}" alt="Logotipo de Asics" class="fotoMasNovedades">
                        <img src="{{ asset('img/SimboloAsics.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>
            </div>
        </section>

        <div id="videos-carusel" class="container-fluid p-0 mt-5">
            <div class="video-container">
                <video src="{{ asset('vid/Zapatillas nike.mp4') }}" autoplay loop muted width="100%"
                    aria-label="Video promocional de Zapatillas Nike"></video>
                <div class="video-desc d-flex flex-column justify-content-center align-items-center">
                    <h2>Nuevas Nike Gato 2.0</h2>
                    <a href="#" class="button">Ver producto</a>
                </div>
            </div>
        </div>

        <section id="mas-comprados" class="container mt-5 mb-5">
            <h2 class="text-center mb-5">Más comprados</h2>

            <div class="row flex-nowrap overflow-auto gx-3 products-carousel">
                @forelse($masComprados as $product)
                    <div class="col-12 col-sm-6 col-lg-3 flex-shrink-0">
                        <div class="h-100 d-flex flex-column justify-content-between">
                            <a href="{{ route('products.show', $product->id) }}" class="d-block text-center"
                                aria-label="Ver detalles de {{ $product->nombre }}">
                                @if($product->img)
                                    <img src="{{ Str::startsWith($product->img, 'http') ? $product->img : (Str::startsWith($product->img, 'img/') ? asset($product->img) : asset('img/' . $product->img)) }}"
                                        alt="{{ $product->nombre }}"
                                        class="fotoMasComprados"
                                        style="object-fit: contain; max-height: 200px; width: auto;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 200px;">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="prod-desc p-2 text-center">
                                <strong class="d-block text-truncate" title="{{ $product->nombre }}">{{ $product->nombre }}</strong>
                                <p class="mb-0">
                                    {{ number_format($product->precio, 2) }} €
                                    @if($product->oferta)
                                        <span class="text-decoration-line-through text-muted small ms-1">{{ number_format($product->precio * 1.2, 2) }} €</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('cart.add', $product->id) }}" class="button mt-auto w-100 text-center">Añadir al carrito</a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>No hay productos destacados en este momento.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
    <script src="{{ asset('js/anims.js') }}" defer></script>
@endsection

