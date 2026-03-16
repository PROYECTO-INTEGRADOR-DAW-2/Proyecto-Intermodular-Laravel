<script setup>
</script>

<template>
    <div class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <i class="bi bi-tree text-success" style="font-size: 4rem;"></i>
                    <h1 class="display-4 fw-bold mt-3">Nuestro Compromiso con el Planeta</h1>
                    <p class="lead text-muted">La sostenibilidad es el núcleo de lo que hacemos. Creemos en un futuro donde la moda y el deporte coexistan en armonía con la naturaleza.</p>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="fw-bold mb-4 text-success"><i class="bi bi-recycle me-2"></i>Materiales Reciclados</h3>
                        <p>Trabajamos incansablemente para reducir nuestra huella de carbono. Por ello, destacamos con nuestra etiqueta <strong>Eco</strong> todos los productos fabricados con un alto porcentaje de materiales reciclados o sostenibles, como poliéster reciclado, algodón orgánico y tintes ecológicos que ahorran agua en su producción.</p>
                        <p>Busca nuestro distintivo verde en el catálogo para tomar decisiones responsables con el medio ambiente.</p>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 bg-light">
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-box-seam text-primary fs-1 mb-3"></i>
                                <h4 class="fw-bold h5">Embalaje Ecológico</h4>
                                <p class="text-muted small mb-0">Todos nuestros pedidos se envían en cajas de cartón 100% reciclado y reciclable, eliminando los plásticos de un solo uso en nuestros embalajes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100 bg-light">
                            <div class="card-body p-4 text-center">
                                <i class="bi bi-cloud-check text-info fs-1 mb-3"></i>
                                <h4 class="fw-bold h5">Servidores Verdes</h4>
                                <p class="text-muted small mb-0">Nuestra plataforma está alojada en un centro de datos (Cloud) alimentado con energía 100% renovable, optimizando los recursos mediante técnicas avanzadas y eficientes.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-5 bg-success text-white">
                    <div class="card-body p-4 p-md-5 text-center">
                        <h3 class="fw-bold mb-3">Únete al movimiento</h3>
                        <p class="mb-4">Elegir productos sostenibles no es solo una compra, es una declaración de intenciones por el futuro de nuestro planeta.</p>
                        <router-link :to="{ name: 'products' }" class="btn btn-light btn-lg px-5 fw-bold text-success">Ver catálogo</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
