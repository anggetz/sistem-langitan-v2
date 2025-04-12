<script setup>
defineProps({
    href: {
        type: String,
        required: true,
    },
    size: {
        type: String,
        default: 'md',
    },
    color: {
        type: String,
        default: 'light',
    },
});
</script>
<template>
    <a :class="`btn btn-${size} btn-${color}`" :href="href">
        <slot>Button</slot>
    </a>
</template>
