<script setup>
import { RouterLink, RouterView } from 'vue-router'
import Footer from './components/Footer.vue'
import MainMenu from './components/MainMenu.vue';
import Messages from './components/Messages.vue';
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
