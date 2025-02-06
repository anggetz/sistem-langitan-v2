<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    href: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: 'md',
    },
    color: String,
});
</script>
<template>
    <!-- TODO: Sampai sini -->
    <a class="btn btn-primary" :href="href">
        <slot />
    </a>
</template>
