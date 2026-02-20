<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <header>
    <nav>
      <RouterLink to="/">Inicio</RouterLink> | 
      <RouterLink to="/about">Sobre Nosotros</RouterLink>
    </nav>
  </header>

  <RouterView />
</template>

<style scoped>

</style>
