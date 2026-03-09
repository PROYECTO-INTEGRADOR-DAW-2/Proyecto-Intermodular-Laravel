<script setup>
import { RouterLink, RouterView} from 'vue-router'
import Footer from './components/Footer.vue'
import MainMenu from './components/MainMenu.vue';
</script>

<template>
  <header>
    <MainMenu />
  </header>


  <RouterView />

  <Footer />

</template>

<style scoped>

</style>
