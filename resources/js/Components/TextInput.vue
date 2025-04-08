<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
  <input class="input" v-model="model" />
</template>
