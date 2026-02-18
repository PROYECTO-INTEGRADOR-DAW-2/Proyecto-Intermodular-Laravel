<script setup>
import { useRole } from '../composables/useRole'

const props = defineProps({
  permission: {
    type: String,
    default: ''
  },
  role: {
    type: String,
    default: ''
  }
})

const { can, hasRole } = useRole()
</script>

<template>
  <template v-if="(permission && can(permission)) || (role && hasRole(role))">
    <slot></slot>
  </template>
</template>
