<script setup>
defineProps({
    title: String,
    subTitle: String,
});
</script>
<template>
    <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
        <div class="flex flex-col justify-center gap-2">
            <h1 class="text-xl font-medium leading-none text-gray-900">
                {{ title ? title : '[Page Title]'}}
            </h1>
            <div class="flex items-center gap-2 text-sm font-normal text-gray-700">
                {{ subTitle }}
            </div>
        </div>
        <div v-if="$slots.default" class="flex items-center gap-2.5">
            <slot />
        </div>
    </div>
</template>
