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
                    <a href="#" aria-label="Ver novedades de Nike">
                        <img src="{{ asset('img/logoNike.jpg') }}" alt="Logotipo de Nike" class="fotoMasNovedades">
                        <img src="{{ asset('img/simboloNike.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="#" aria-label="Ver novedades de Adidas">
                        <img src="{{ asset('img/logoAdidass.jpg') }}" alt="Logotipo de Adidas" class="fotoMasNovedades">
                        <img src="{{ asset('img/AdidasSimbolo.png') }}" alt="" class="fotoSimboloAdidas d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="#" aria-label="Ver novedades de Puma">
                        <img src="{{ asset('img/logospuma.jpg') }}" alt="Logotipo de Puma" class="fotoMasNovedades">
                        <img src="{{ asset('img/SimboloPuma.png') }}" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="#" aria-label="Ver novedades de Asics">
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

                <!-- Producto 1 -->
                <div class="col-12 col-sm-6 col-lg-3 flex-shrink-0">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <a href="#" class="d-block text-center"
                            aria-label="Ver detalles de Nike Mercurial Superfly 10 Academy">
                            <img src="{{ asset('img/zapatillasNikeKM.png') }}" alt="Zapatillas Nike Mercurial Superfly 10 Academy"
                                class="fotoMasComprados">
                        </a>
                        <div class="prod-desc p-2 text-center">
                            <strong>Nike Mercurial Superfly 10 Academy</strong>
                            <p>69,99 € <span>99,99 €</span></p>
                        </div>
                        <a href="#" class="button mt-auto w-100 text-center">Añadir al carrito</a>
                    </div>
                </div>

                <!-- Producto 2 -->
                <div class="col-12 col-sm-6 col-lg-3 flex-shrink-0">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <a href="#" class="d-block text-center" aria-label="Ver detalles de Nike Zoomx 2060">
                            <img src="{{ asset('img/ZapasNikeMasComprado.png') }}" alt="Zapatillas Nike Zoomx 2060"
                                class="fotoMasComprados">
                        </a>
                        <div class="prod-desc p-2 text-center">
                            <strong>Nike Zoomx 2060</strong>
                            <p>120,00 €</p>
                        </div>
                        <a href="#" class="button mt-auto w-100 text-center">Añadir al carrito</a>
                    </div>
                </div>

                <!-- Producto 3 -->
                <div class="col-12 col-sm-6 col-lg-3 flex-shrink-0">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <a href="#" class="d-block text-center" aria-label="Ver detalles de Nike Impossibly Light">
                            <img src="{{ asset('img/nikeropa-removebg-preview.png') }}" alt="Chaqueta Nike Impossibly Light"
                                class="fotoMasComprados">
                        </a>
                        <div class="prod-desc p-2 text-center">
                            <strong>Nike Impossibly Light</strong>
                            <p>76,99 € <span>109,99 €</span></p>
                        </div>
                        <a href="#" class="button mt-auto w-100 text-center">Añadir al carrito</a>
                    </div>
                </div>

                <!-- Producto 4 -->
                <div class="col-12 col-sm-6 col-lg-3 flex-shrink-0">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <a href="#" class="d-block text-center" aria-label="Ver detalles de Calcetines Nike">
                            <img src="{{ asset('img/CalcetinesMasCmprado.png') }}" alt="Par de calcetines Nike"
                                class="fotoMasComprados">
                        </a>
                        <div class="prod-desc p-2 text-center">
                            <strong>Calcetines Nike</strong>
                            <p>19,99 €</p>
                        </div>
                        <a href="#" class="button mt-auto w-100 text-center">Añadir al carrito</a>
                    </div>
                </div>

            </div>
        </section>
    </main>
    <script src="{{ asset('js/anims.js') }}" defer></script>
@endsection

