<template>
  <footer class="bg-dark text-white pt-5 pb-4 mt-auto">
    <div class="container text-center text-md-start">
      <div class="row text-center text-md-start">
        <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-danger">J&A Sports</h5>
          <p>
            Tu tienda de deportes de confianza. Encuentra las mejores marcas y productos
            para llevar tu rendimiento al siguiente nivel.
          </p>
        </div>

        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Productos</h5>
          <p><router-link :to="{ name: 'products', query: { sexo: 'Hombre' } }" class="text-white text-decoration-none">Hombre</router-link></p>
          <p><router-link :to="{ name: 'products', query: { sexo: 'Mujer' } }" class="text-white text-decoration-none">Mujer</router-link></p>
          <p><router-link :to="{ name: 'products', query: { categoria: 'Zapatillas' } }" class="text-white text-decoration-none">Zapatillas</router-link></p>
          <p><router-link :to="{ name: 'products' }" class="text-white text-decoration-none">Ver Todo</router-link></p>
        </div>

        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Enlaces útiles</h5>
          <p><router-link :to="{ name: 'sostenibilidad' }" class="text-white text-decoration-none">Sostenibilidad</router-link></p>
          <p><router-link :to="{ name: 'profile' }" class="text-white text-decoration-none">Tu Cuenta</router-link></p>
          <p><router-link :to="{ name: 'afiliados' }" class="text-white text-decoration-none">Ser Afiliado</router-link></p>
          <p><router-link :to="{ name: 'tarifas-envio' }" class="text-white text-decoration-none">Tarifas de Envío</router-link></p>
          <p><router-link :to="{ name: 'ayuda' }" class="text-white text-decoration-none">Ayuda</router-link></p>
        </div>

        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
          <h5 class="text-uppercase mb-4 font-weight-bold text-warning">Contacto</h5>
          <p><i class="bi bi-house-door me-3"></i> Valencia, ES</p>
          <p><i class="bi bi-envelope me-3"></i> info@jasports.com</p>
          <p><i class="bi bi-phone me-3"></i> + 34 234 567 88</p>
        </div>
      </div>

      <hr class="mb-4">

      <div class="row align-items-center">
        <div class="col-md-7 col-lg-8">
          <p>Copyright © 2024 Todos los derechos reservados por:
            <strong class="text-warning">J&A Sports</strong>
          </p>
        </div>
        <div class="col-md-5 col-lg-4">
          <div class="text-center text-md-right">
            <ul class="list-unstyled list-inline">
              <li class="list-inline-item">
                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-facebook"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-twitter-x"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-google"></i></a>
              </li>
              <li class="list-inline-item">
                <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="bi bi-linkedin"></i></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>

<style scoped>
/* Footer specific styles if any additional needed */
</style>
