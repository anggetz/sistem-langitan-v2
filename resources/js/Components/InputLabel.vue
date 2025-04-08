<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
  <label class="form-label font-normal text-gray-900">
    <slot>{{ value }}</slot>
  </label>
</template>
