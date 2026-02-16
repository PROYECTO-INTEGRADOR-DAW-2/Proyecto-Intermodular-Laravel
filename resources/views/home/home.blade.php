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
                        <img src="{{ asset('imgNike/simboloNike.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                        <img src="{{ asset('imgNike/logoNike.jpg') }}" alt="Logotipo de Nike" class="fotoMasNovedades">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Adidas']]) }}" aria-label="Ver novedades de Adidas">
                        <img src="{{ asset('imgAdidas/AdidasSimbolo.png') }}" alt="" class="fotoSimboloAdidas d-none d-sm-inline"
                            aria-hidden="true">
                        <img src="{{ asset('imgAdidas/logoAdidass.jpg') }}" alt="Logotipo de Adidas" class="fotoMasNovedades">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Puma']]) }}" aria-label="Ver novedades de Puma">
                        <img src="{{ asset('imgPuma/SimboloPuma.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                        <img src="{{ asset('imgPuma/logospuma.jpg') }}" alt="Logotipo de Puma" class="fotoMasNovedades">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="{{ route('products.index', ['marca' => ['Asics']]) }}" aria-label="Ver novedades de Asics">
                        <img src="{{ asset('imgAsics/SimboloAsics.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                        <img src="{{ asset('imgAsics/asicslogo.jpg') }}" alt="Logotipo de Asics" class="fotoMasNovedades">
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
                    <a href="{{ $promotedProduct ? route('products.show', $promotedProduct->id) : route('products.index') }}" class="button">Ver producto</a>
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
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}"
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
                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('products.show', $product->id) }}" class="button flex-grow-1 text-center text-decoration-none py-1">Ver producto</a>
                                <a href="{{ route('wishlist.add', $product->id) }}" class="btn btn-outline-danger d-flex align-items-center justify-content-center p-1" style="width: 35px; height: 35px;" title="Añadir a lista de deseos">
                                    <i class="bi bi-heart"></i>
                                </a>
                            </div>
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

