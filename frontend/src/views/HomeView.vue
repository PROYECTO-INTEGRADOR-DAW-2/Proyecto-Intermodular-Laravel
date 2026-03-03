<script setup>
  import { useRoute } from 'vue-router';
  import { ref } from 'vue';

  

  



  // TAREAS
  // 1.- Obtener los productos mas comprados
  

</script>


<template>
  <main>
        <header id="header-home" class="d-flex align-items-center justify-content-center text-center" style="background-image: url('');">
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
                        <img src="" alt="Logotipo de Nike" class="fotoMasNovedades">
                        <img src="" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="#" aria-label="Ver novedades de Adidas">
                        <img src="" alt="Logotipo de Adidas" class="fotoMasNovedades">
                        <img src="" alt="" class="fotoSimboloAdidas d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="#" aria-label="Ver novedades de Puma">
                        <img src="" alt="Logotipo de Puma" class="fotoMasNovedades">
                        <img src="" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>

                <article class="col-6 col-md-6 col-lg-2 text-center">
                    <a href="#" aria-label="Ver novedades de Asics">
                        <img src="" alt="Logotipo de Asics" class="fotoMasNovedades">
                        <img src="" alt="" class="fotoSimbolo d-none d-sm-inline"
                            aria-hidden="true">
                    </a>
                </article>
            </div>
        </section>

        <div id="videos-carusel" class="container-fluid p-0 mt-5">
            <div class="video-container">
                <video src="" autoplay loop muted width="100%"
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

                <!-- Producto 1 ejemplo generar dinamicamente-->
                <div class="col-12 col-sm-6 col-lg-3 flex-shrink-0">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <a href="#" class="d-block text-center"
                            aria-label="Ver detalles de Nike Mercurial Superfly 10 Academy">
                            <img src="" alt="Zapatillas Nike Mercurial Superfly 10 Academy"
                                class="fotoMasComprados">
                        </a>
                        <div class="prod-desc p-2 text-center">
                            <strong>Nike Mercurial Superfly 10 Academy</strong>
                            <p>69,99 € <span>99,99 €</span></p>
                        </div>
                        <a href="#" class="button mt-auto w-100 text-center">Añadir al carrito</a>
                    </div>
                </div>

               

            </div>
        </section>
    </main>





</template>

<style scoped>
.home {
  padding: 2rem;
  text-align: center;
}
</style>
