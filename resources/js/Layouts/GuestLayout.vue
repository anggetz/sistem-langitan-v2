<script setup>
import { onMounted } from 'vue';
import { KTTogglePassword } from '@/../metronic/core/components/toggle-password';

onMounted(() => {
    KTTogglePassword.init();
});
</script>

<style>
.page-bg {
    background-image: url('assets/media/images/2600x1200/bg-10.png');
}
.dark .page-bg {
    background-image: url('assets/media/images/2600x1200/bg-10-dark.png');
}
</style>

<template>
    <slot />
</template>
