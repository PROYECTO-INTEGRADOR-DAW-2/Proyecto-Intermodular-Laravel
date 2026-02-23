<script setup>
import { useToastStore } from '@/stores/toast';
import { storeToRefs } from 'pinia';

const toastStore = useToastStore();
const { toasts } = storeToRefs(toastStore);

const removeToast = (id) => {
    toastStore.removeToast(id);
};
</script>

<template>
    <div class="toast-container position-fixed end-0 p-3" style="z-index: 1060; top: 80px;">
        <transition-group name="toast-slide">
            <div 
                v-for="toast in toasts" 
                :key="toast.id" 
                class="toast show align-items-center border-0 mb-2 shadow-lg" 
                role="alert" 
                aria-live="assertive" 
                aria-atomic="true"
                :class="{
                    'bg-success text-white': toast.type === 'success',
                    'bg-danger text-white': toast.type === 'error',
                    'bg-dark text-white': toast.type === 'info' || !toast.type
                }"
                style="min-width: 300px;"
            >
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i v-if="toast.type === 'success'" class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <i v-else-if="toast.type === 'error'" class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                        <i v-else class="bi bi-info-circle-fill me-2 fs-5"></i>
                        
                        <span class="fw-medium">{{ toast.message }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="removeToast(toast.id)" aria-label="Close"></button>
                </div>
            </div>
        </transition-group>
    </div>
</template>

<style scoped>
.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all 0.3s ease;
}
.toast-slide-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* Custom styling for "spectacular" look requested */
.toast {
    background: rgba(0, 0, 0, 0.9); /* Default fallback */
    backdrop-filter: blur(10px);
    border-radius: 12px;
}

.bg-black {
    background-color: #000 !important;
    border-left: 4px solid #333;
}

.bg-success {
    background: linear-gradient(135deg, #198754, #146c43) !important;
    border-left: 4px solid #0f5132;
}

.bg-danger {
    background: linear-gradient(135deg, #dc3545, #b02a37) !important;
    border-left: 4px solid #842029;
}
</style>
