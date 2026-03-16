<template>
  <div class="container py-5 my-5">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-6">
        <h1 class="display-4 fw-bold"><i class="bi bi-question-circle text-warning me-2"></i>Centro de Ayuda</h1>
        <p class="lead text-muted mt-3">¿En qué podemos ayudarte hoy? Encuentra respuestas rápidas o contáctanos.</p>
        
        <div class="input-group input-group-lg shadow-sm mt-4">
          <input type="text" class="form-control border-0 bg-light px-4" placeholder="Buscar preguntas frecuentes...">
          <button class="btn btn-dark px-4" type="button"><i class="bi bi-search"></i> Buscar</button>
        </div>
      </div>
    </div>
    
    <div class="row g-4 mt-2">
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 hover-card">
           <i class="bi bi-box2-heart text-warning display-4 mb-3"></i>
           <h4 class="fw-bold">Pedidos y Devoluciones</h4>
           <p class="text-muted">Consulta el estado de tu pedido y cómo devolver un producto si no te convence.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 hover-card">
           <i class="bi bi-credit-card text-warning display-4 mb-3"></i>
           <h4 class="fw-bold">Pagos</h4>
           <p class="text-muted">Información sobre métodos de pago aceptados, facturas y seguridad de tu tarjeta.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 hover-card">
           <i class="bi bi-headset text-warning display-4 mb-3"></i>
           <h4 class="fw-bold">Atención al Cliente</h4>
           <p class="text-muted">Si no encuentras tu respuesta, nuestros agentes están aquí para ayudarte de L-V.</p>
           <button class="btn btn-outline-dark mt-2 w-100 mt-auto">Contactar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.hover-card {
  transition: all 0.3s ease;
}
.hover-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
}
</style>
