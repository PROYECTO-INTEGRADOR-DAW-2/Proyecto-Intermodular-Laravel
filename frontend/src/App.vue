<script setup>
import { RouterLink, RouterView } from 'vue-router'
import Footer from './components/Footer.vue'
import MainMenu from './components/MainMenu.vue';
import Messages from './components/Messages.vue';
import { onMounted, onUnmounted } from 'vue'
import Lenis from 'lenis'

  let lenis = null
  let animationFrameId = null

  onMounted(() => {
    // Inicializamos Lenis cuando el DOM ya existe
    lenis = new Lenis({
      duration: 25, // Duración del scroll (ajustable)
      smoothWheel: true
    })

    function raf(time) {
      lenis.raf(time)
      animationFrameId = requestAnimationFrame(raf)
    }

    animationFrameId = requestAnimationFrame(raf)
  })

  // Limpiamos la memoria si el componente se destruye
  onUnmounted(() => {
    if (lenis) {
      lenis.destroy()
    }
    if (animationFrameId) {
      cancelAnimationFrame(animationFrameId)
    }
  })


</script>

<template>

  
  <header>
    <MainMenu />
  </header>

  <Messages />


  <RouterView />

  <Footer />

</template>

<style scoped>

</style>
