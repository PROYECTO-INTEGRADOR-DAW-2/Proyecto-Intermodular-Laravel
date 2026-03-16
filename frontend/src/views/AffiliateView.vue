<template>
  <div class="container py-5 my-5">
    <div class="row min-vh-50 justify-content-center align-items-center">
      <div class="col-md-8 text-center text-md-start">
        <h1 class="display-4 fw-bold text-dark mb-4 border-bottom pb-3"><i class="bi bi-person-plus text-warning me-2"></i>Programa de Afiliados</h1>
        <p class="lead text-muted mb-4">
          Únete a nuestro programa de afiliados y empieza a ganar comisiones promocionando nuestros productos deportivos. ¡Es la manera perfecta de monetizar tu pasión!
        </p>
        <div class="card shadow-sm border-0 bg-light p-4 rounded-4 my-4">
          <div class="row align-items-center">
            <div class="col-sm-4 text-center">
               <i class="bi bi-wallet2 display-3 text-warning"></i>
            </div>
            <div class="col-sm-8 text-center text-sm-start mt-3 mt-sm-0">
               <h5>Gana hasta un 15%</h5>
               <p class="text-muted mb-0">En comisiones por ventas referidas directas.</p>
            </div>
          </div>
        </div>
        <button class="btn btn-dark btn-lg px-5 hover-lift">Solicitar Información</button>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
