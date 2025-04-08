<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
  <span class="form-info text-danger font-normal" v-show="message">{{ message }}</span>
</template>
