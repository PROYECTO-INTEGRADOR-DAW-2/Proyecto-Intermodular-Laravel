<script setup>
import { computed } from 'vue'

const props = defineProps({
  role: {
    type: String,
    required: true
  }
})

const badgeConfig = computed(() => {
  const configs = {
    admin:  { label: 'Admin',   class: 'bg-danger' },
    vendor: { label: 'Vendor',  class: 'bg-warning text-dark' },
    editor: { label: 'Editor',  class: 'bg-info text-dark' },
    user:   { label: 'Usuario', class: 'bg-secondary' },
  }
  return configs[props.role?.toLowerCase()] || { label: props.role, class: 'bg-secondary' }
})
</script>

<template>
  <span :class="['badge', badgeConfig.class]">
    {{ badgeConfig.label }}
  </span>
</template>
