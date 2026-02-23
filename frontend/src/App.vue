<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useCartStore } from '@/stores/cart';
import Navbar from '@/components/Navbar.vue';
import Footer from '@/components/Footer.vue';
import ChatWidget from '@/components/ChatWidget.vue';
import ToastNotification from '@/components/ToastNotification.vue';

const authStore = useAuthStore();
const cartStore = useCartStore();

onMounted(async () => {
  // Always refresh user data from API on app load (fixes stale localStorage role data)
  await authStore.fetchUser();

  if (cartStore.items.length > 0) {
      cartStore.fetchDetails();
  }
});
</script>

<template>
  <div class="d-flex flex-column min-vh-100">
    <Navbar />
    
    <ToastNotification />

    <main class="flex-grow-1">
      <router-view />
    </main>

    <Footer />
    <ChatWidget />
  </div>
</template>

<style>
/* Global styles */
</style>
