<script setup>
import { inject, onMounted } from 'vue';

const breadcrumbItems = inject('breadcrumbItems')
const props = defineProps({
  'content': {
    type: String,
    required: true
  }
})

onMounted(() => {
  breadcrumbItems.value = props.content.split(' > ')
})

</script>

<template></template>
