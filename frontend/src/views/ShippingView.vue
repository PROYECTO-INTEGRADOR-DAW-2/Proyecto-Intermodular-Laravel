<template>
  <div class="container py-5 my-5">
    <h1 class="display-5 fw-bold text-center mb-5"><i class="bi bi-box-seam text-warning me-3"></i>Tarifas y Tiempos de Envío</h1>
    
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
           <table class="table table-hover align-middle mb-0">
             <thead class="table-dark">
               <tr>
                 <th class="py-3 px-4">Método de Envío</th>
                 <th class="py-3 px-4">Tiempo Estimado</th>
                 <th class="py-3 px-4 text-end">Costo</th>
               </tr>
             </thead>
             <tbody>
               <tr>
                 <td class="px-4 py-3 fw-bold">Estándar (Península)</td>
                 <td class="px-4 py-3 text-muted">3-5 días hábiles</td>
                 <td class="px-4 py-3 text-end">4.99 € <br> <small class="text-success">(Gratis > 50€)</small></td>
               </tr>
               <tr>
                 <td class="px-4 py-3 fw-bold">Exprés</td>
                 <td class="px-4 py-3 text-muted">24-48 horas</td>
                 <td class="px-4 py-3 text-end">8.99 €</td>
               </tr>
               <tr>
                 <td class="px-4 py-3 fw-bold">Baleares y Canarias</td>
                 <td class="px-4 py-3 text-muted">5-7 días hábiles</td>
                 <td class="px-4 py-3 text-end">12.00 €</td>
               </tr>
               <tr>
                 <td class="px-4 py-3 fw-bold">Recogida en Tienda</td>
                 <td class="px-4 py-3 text-muted">1-2 días hábiles</td>
                 <td class="px-4 py-3 text-end text-success fw-bold">Gratis</td>
               </tr>
             </tbody>
           </table>
        </div>
        
        <div class="alert alert-info border-0 rounded-4 shadow-sm" role="alert">
          <i class="bi bi-info-circle-fill me-2 fs-5"></i> Los plazos de envío pueden variar durante campañas especiales como Black Friday o Navidad.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>
