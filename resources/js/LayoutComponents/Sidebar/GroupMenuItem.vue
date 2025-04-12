<script setup>
defineProps({
  title: String,
})
</script>

<template>
  <div class="menu-item pt-2.25 pb-px">
    <span class="menu-heading uppercase text-2sm font-medium text-gray-500 ps-[10px] pe-[10px]">
      {{ title }}
    </span>
  </div>
</template>
