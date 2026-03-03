<script setup>
import { RouterLink, RouterView} from 'vue-router'
import Footer from './components/Footer.vue'
</script>

<template>
  <header>
    <nav>
      <RouterLink to="/">Inicio</RouterLink> | 
      <RouterLink to="/about">Sobre Nosotros</RouterLink>
    </nav>
  </header>


  <RouterView />

  <Footer />

</template>

<style scoped>

</style>
